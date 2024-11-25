<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;


class EnvController extends Controller
{

    public function index()
    {
        $envPath = base_path('.env');
        $envVariables = [];

        if (file_exists($envPath)) {
            $envContent = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            foreach ($envContent as $line) {
                if (strpos($line, '=') !== false && substr($line, 0, 1) != '#') {
                    list($key, $value) = explode('=', $line, 2);
                    $envVariables[$key] = $value;
                }
            }
        }

        return view('admin.env.list', compact('envVariables'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'key' => 'required|string',
            'value' => 'required|string',
        ]);

        $key = $request->input('key');
        $value = $request->input('value');
        $this->updateEnv($key, $value);

        return redirect()->back()->with('success', "Environment variable {$key} updated successfully.");
    }

    private function updateEnv($key, $value)
    {
        $path = base_path('.env');

        if (file_exists($path)) {
            $envContent = file_get_contents($path);

            if (preg_match("/^{$key}=.*/m", $envContent)) {
                $envContent = preg_replace("/^{$key}=.*/m", "{$key}={$value}", $envContent);
            } else {
                $envContent .= PHP_EOL . "{$key}={$value}";
            }

            file_put_contents($path, $envContent);
        }
    }
}
