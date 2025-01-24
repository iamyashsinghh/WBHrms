<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Employee;
use App\Models\Attendance; // Assuming there is an Attendance model
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Facades\Log;

class SandwichPolicy extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:sandwichpolicy';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Apply sandwich policy for employees based on their weekdays.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Get today's day
        $today = Carbon::now();

        // Ensure this runs only on Tuesday
        if ($today->dayOfWeek !== Carbon::TUESDAY) {
            $this->info("This command is designed to run on Tuesdays only.");
            return;
        }
        
        // Fetch all employees
        $employees = Employee::where('is_active', '1')->whereIn('weekdays', ['sat-sun', 'sun'])->get();

        foreach ($employees as $employee) {
            // Get weekdays and compute attendance range
            $weekdays = $employee->weekdays;
            $attendanceRange = $this->getAttendanceRange($weekdays);
            $this->info($attendanceRange['start']);
            $this->info($attendanceRange['end']);

            // Fetch attendance records within the computed range
            $attendanceRecords = Attendance::where('emp_code', $employee->emp_code)
                ->whereBetween('date', [$attendanceRange['start'], $attendanceRange['end']])
                ->pluck('status', 'date');

            // e.g. [ '2025-01-17' => 'absent', '2025-01-18' => 'wo', ... ]

            // 3) Convert to a day-by-day status array
            $statusSequence = $this->buildDailyStatuses($attendanceRange['start'], $attendanceRange['end'], $attendanceRecords);

            // 4) Check if any of our custom sandwich patterns appear
            $sandwich = $this->checkDynamicSandwich($statusSequence);

            // Log or process based on the result
            if ($sandwich) {
                $this->info("Sandwich policy applies for Employee {$employee->emp_code}.");
                $period = CarbonPeriod::create($attendanceRange['start'], $attendanceRange['end']);

                foreach ($period as $date) {
                    $dateStr = $date->format('Y-m-d');

                    // Retrieve or create attendance record
                    $attendance = Attendance::firstOrNew([
                        'emp_code' => $employee->emp_code,
                        'date'     => $dateStr,
                    ]);
                        $attendance->status = 'absent';
                        $attendance->save();
                }
            } else {
                $this->info("Sandwich policy does not apply for Employee {$employee->emp_code}.");
            }
        }

        $this->info("Sandwich policy processed successfully.");
    }

    /**
     * Convert short weekday names (e.g., "sat-sun") to full names (e.g., "Saturday, Sunday")
     * and compute the attendance range based on weekdays.
     *
     * @param string $weekdays
     * @return array
     */
    private function getAttendanceRange(string $weekdays): array
    {
        // Map short weekday names to Carbon constants
        $dayMap = [
            'mon' => Carbon::MONDAY,
            'tue' => Carbon::TUESDAY,
            'wed' => Carbon::WEDNESDAY,
            'thu' => Carbon::THURSDAY,
            'fri' => Carbon::FRIDAY,
            'sat' => Carbon::SATURDAY,
            'sun' => Carbon::SUNDAY,
        ];

        // Split the string (e.g., "sat-sun") and normalize
        $days = array_filter(explode('-', strtolower(trim($weekdays))));

        // Convert to Carbon weekday constants
        $carbonDays = [];
        foreach ($days as $day) {
            if (isset($dayMap[$day])) {
                $carbonDays[] = $dayMap[$day];
            }
        }

        // If none of the days are valid, return a null range
        if (empty($carbonDays)) {
            return ['start' => null, 'end' => null];
        }

        // Get "today" for reference
        $today = Carbon::now();

        // ------------------------------------------------------------------
        // SPECIAL CASE 1: If the employee has both Saturday (6) and Sunday (0),
        // we want the LAST weekend relative to today: Friday -> Monday.
        //
        // For example, if today is 2025-01-24 (Friday), the "last Saturday" is 2025-01-18
        // and the "last Sunday" is 2025-01-19, so the date range is Jan 17 (Fri) to Jan 20 (Mon).
        // ------------------------------------------------------------------
        if (in_array(Carbon::SATURDAY, $carbonDays) && in_array(Carbon::SUNDAY, $carbonDays)) {
            // Find the most recent Saturday
            $lastSaturday = $today->copy()->previous(Carbon::SATURDAY);
            // The most recent Sunday
            $lastSunday = $today->copy()->previous(Carbon::SUNDAY);

            // The Friday before that Saturday
            $start = $lastSaturday->copy()->previous(Carbon::FRIDAY);
            // The Monday after that Sunday
            $end = $lastSunday->copy()->next(Carbon::MONDAY);

            return [
                'start' => $start->format('Y-m-d'),
                'end'   => $end->format('Y-m-d'),
            ];
        }

        // ------------------------------------------------------------------
        // SPECIAL CASE 2: If only Sunday (0), you want
        // the most recent Saturday -> next Monday
        // ------------------------------------------------------------------
        if (count($carbonDays) === 1 && in_array(Carbon::SUNDAY, $carbonDays)) {
            $lastSaturday = $today->copy()->previous(Carbon::SATURDAY);
            $end = $lastSaturday->copy()->next(Carbon::MONDAY);

            return [
                'start' => $lastSaturday->format('Y-m-d'),
                'end'   => $end->format('Y-m-d'),
            ];
        }

        // ------------------------------------------------------------------
        // DEFAULT CASE: For any other mix of weekdays, do a dynamic range:
        // find the earliest (min) and latest (max) Carbon weekday from the set,
        // then get the previous occurrence of the earliest and
        // the next occurrence of the latest relative to "today".
        // ------------------------------------------------------------------
        $minDay = min($carbonDays); // earliest weekday constant
        $maxDay = max($carbonDays); // latest weekday constant

        $start = $today->copy()->previous($minDay);
        $end   = $today->copy()->next($maxDay);

        return [
            'start' => $start->format('Y-m-d'),
            'end'   => $end->format('Y-m-d'),
        ];
    }

    /**
     * Build a day-by-day status array from $startDate to $endDate.
     * Anything not 'present' (or missing) is normalized to 'absent', 'wo', or 'unmarked'.
     *
     * @param string $startDate
     * @param string $endDate
     * @param \Illuminate\Support\Collection $attendanceRecords (key: date, value: status)
     * @return string[] e.g. ['absent', 'wo', 'unmarked', ...]
     */
    private function buildDailyStatuses(string $startDate, string $endDate, $attendanceRecords): array
    {
        $period = CarbonPeriod::create($startDate, $endDate);
        $sequence = [];

        foreach ($period as $date) {
            $dateStr = $date->format('Y-m-d');
            // If there's a record in attendance, we check it.
            if (isset($attendanceRecords[$dateStr])) {
                $status = strtolower($attendanceRecords[$dateStr]); // e.g. 'wo', 'absent', 'present'
                // Keep only those statuses you need, map everything else to 'present' or your choice
                if ($status === 'wo') {
                    $sequence[] = 'wo';
                } elseif ($status === 'absent') {
                    $sequence[] = 'absent';
                } else {
                    // e.g. 'present', 'leave' -> decide how you want to label it
                    // but typically doesn't matter since your patterns are only about 'absent', 'wo', 'unmarked'
                    $sequence[] = 'present';
                }
            } else {
                // No record => unmarked
                $sequence[] = 'unmarked';
            }
        }
        Log::info($sequence);
        return $sequence;
    }

    /**
     * Check if any custom patterns appear in the $statusSequence.
     * Patterns:
     *   1) ['absent', 'wo', 'absent']
     *   2) ['absent', 'wo', 'wo', 'absent']
     *   3) ['unmarked', 'wo', 'wo', 'absent']
     *   4) ['unmarked', 'wo', 'wo', 'unmarked']
     *   5) ['absent', 'wo', 'wo', 'unmarked']
     *
     * @param string[] $statusSequence
     * @return bool True if any pattern is found
     */
    private function checkDynamicSandwich(array $statusSequence): bool
    {
        // 1) Generate all possible combos of these 3 statuses for length 3 and 4
        $statuses = ['absent', 'wo', 'unmarked'];

        $patterns3 = $this->generateAllPatterns($statuses, 3);
        $patterns4 = $this->generateAllPatterns($statuses, 4);

        // Merge them into one big pattern list
        $allPatterns = array_merge($patterns3, $patterns4);

        // 2) Slide over $statusSequence and check if any sub-array matches
        $seqLen = count($statusSequence);

        for ($i = 0; $i < $seqLen; $i++) {
            foreach ($allPatterns as $pattern) {
                $pLen = count($pattern);
                // Make sure we have enough room
                if ($i + $pLen <= $seqLen) {
                    $segment = array_slice($statusSequence, $i, $pLen);

                    if ($segment === $pattern) {
                        return true; // Found a match => it's a sandwich
                    }
                }
            }
        }

        return false; // No match
    }

    /**
     * Generate all possible patterns of the given length from an array of items.
     * e.g., items=['absent','wo','unmarked'], length=3 => 3^3=27 combos
     */
    private function generateAllPatterns(array $items, int $length): array
    {
        $results = [];
        $count = count($items);
        // There are $count^$length combos
        $total = pow($count, $length);

        for ($i = 0; $i < $total; $i++) {
            $temp = $i;
            $pattern = [];

            for ($j = 0; $j < $length; $j++) {
                $pattern[] = $items[$temp % $count];
                $temp = intdiv($temp, $count);
            }

            $results[] = $pattern;
        }

        return $results;
    }
}
