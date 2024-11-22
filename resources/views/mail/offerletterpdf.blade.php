<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Offer Letter</title>
    <style>
        @page {
            margin: 0;
        }

        body {
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
            font-size: 14px;
        }

        h1,
        h2,
        h3,
        h4,
        h5,
        h6 {
            font-size: 20px;
            line-height: 10px;
            font-weight: 300;
            line-height: 16px;
        }

        h3 {
            font-size: 18px;
            font-weight: bold;
        }

        h4 {
            font-size: 16px;
            font-weight: bold;
            line-height: 0px !important;
        }

        .header {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            height: 170px;
        }

        .header img {
            height: 170px;
            width: 100%;
        }

        .footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            height: 80px;
        }

        .footer img {
            height: 80px;
            width: 100%;
        }

        .content {
            padding: 230px 50px 120px 50px;
            margin: 0;
        }

        .content2 {
            padding: 180px 50px 120px 50px;
            margin: 0;
        }

        .page-break {
            page-break-after: always;
        }
    </style>
</head>

<body>
    <!-- Header -->
    <div class="header">
        <img src="https://wbcrm.in/offerletterheader.png" alt="Header Image" loading="eager">
    </div>
    <div class="footer">
        <img src="https://wbcrm.in/offerletterfooter.png" alt="Footer Image" loading="eager">
    </div>

    <div class="content">
        <h2>Name: <b>{{ $data['candidate_name'] }}</b></h2>
        <h2>Position: <b>{{ $data['position'] }}</b></h2>
        <h2>Date of Joining: <b>{{ $data['start_date'] }}</b></h2>

        <h2 style="color:#891010; text-decoration: underline; font-family: auto; text-align: center; margin-top: 50px;">
            <b>EMPLOYMENT TERMS AND CONDITION</b>
        </h2>

        <div style="margin-top: 40px;">
            (These Terms are mere representations & highlights of company’s rules & regulations related to employees;
            please
            refer to detailed policy guidelines for better clarity & understanding. Employees are expected to obey these
            rules
            and policies. In case of any conflict between these highlights and detailed policies, detailed policies in
            the employee
            handbook shall prevail. All behaviour/ actions or deviations from standard rules/ policies shall be subject
            to
            disciplinary actions as per the guidelines).
        </div>

        <h3 style="margin-top: 35px">1. Probation Period</h3>
        <div style="margin-top: 20px;">
            The first six (6) months of employment are probationary. During this time both parties may assess
            suitability of
            employment with each other. This also provides management with an opportunity to assess skill levels and
            address
            areas of potential concern. During the probation period, employment can be terminated by the company for any
            reason whatsoever, without cause, and without notice. However, employees would have to serve notice period
            of
            15 Days or payment in lieu of notice in the event of a resignation before the completion of six months.
        </div>
        <h3 style="margin-top: 35px">2. Minimum Service Period</h3>
        <div style="margin-top: 20px;">
            The Company spends substantial time, cost & efforts on the learning & development of an employee; hence the
            company expects that an employee shall serve a minimum period of 1 Year. Resignation or Termination before 1
            Year would be penalised with deduction of 1 month pay. No Experience or Relieving certificate would be
            issued in
            case of less than 1 year served. This period shall start after successful completion of the probationary
            period.
        </div>
        <h3 style="margin-top: 35px">3. Trainings</h3>
        <div style="margin-top: 20px;">
            During the employment, if the company conducts any training session, then it has to be compulsorily attended
            (At
            least 80% attendance is compulsory); otherwise employees will be liable to disciplinary action as per the
            rules of
            the company.
        </div>
    </div>

    <div class="page-break"></div>
    <div class="content2">
        <h3 style="margin-top: 10px">4. Job Responsibility</h3>
        <div style="margin-top: 20px;">
            You shall carry out the job as per your job profile and such other jobs connected with or incidental to
            which is
            necessary for business of the Company. You shall not do any other work assigned to you, which includes
            receiving
            any advance money or giving credit to any person connected in any way or having any dealings with the
            company,
            without the prior written consent of the Company.
        </div>
        <h3 style="margin-top: 25px">5. Company Rules & Policies</h3>
        <div style="margin-top: 20px;">
            You shall abide by all the rules & regulations of the company. You shall be bound by the rules, regulations
            and orders
            promulgated by the management in relation to conduct, discipline and policy matters.
        </div>
        <h3 style="margin-top: 25px">6. Legal Settlements and Collections</h3>
        <div style="margin-top: 20px;">
            While working as an employee if you enter into any business transaction with any party on behalf of the
            company
            within your permissible limits, it shall be your responsibility to ensure recovery of outstanding as soon as
            possible
            and to remit to the company within 24 hours with receipt of the sum by you or in your account or at
            instruction of
            yours by anyone else if any outstanding remains at the time of leaving the services of the company. It shall
            be your
            sole responsibility to recover for remittance to the company before you proceed to settle your legal dues in
            full and
            final settlement of your account.
        </div>
        <h3 style="margin-top: 25px">7. Property in your Possession</h3>
        <div style="margin-top: 20px;">
            While you are in employment with the company, you may be given or handed over the company's property and /
            or
            equipment such as equipment, vehicles, telephones, computers, and software etc. for official use and you
            shall take
            care of them, including their upkeep. The Company’s property is not for private use.
            <br />
            <br />
            These devices are to be used strictly for the company’s business, and are not permitted off grounds unless
            authorised. The Company’s property must be used in the manner for which it was intended. On cessation of
            employment with the Company, you shall return all documents, books, papers relating to the affairs of the
            Company,
            purchased with the Company's money, which may have come to you, and also any property of the Company in your
            possession. If the physical damage is found, the damage/ loss will be recovered from FNF settlement.
        </div>
        <h3 style="margin-top: 25px">8. Professional Conduct</h3>
        <div style="margin-top: 20px;">
            To ensure that the work environment is safe, comfortable and productive, the Company expects its employees
            to
            adhere to standard professional conduct and integrity. Employees should be respectful, courteous, and
            mindful of
            others' feelings and needs. General cooperation between co-workers and managers is expected. Employees found
            behaving unprofessionally may be subject to disciplinary action.
        </div>
    </div>
    <div class="page-break"></div>
    <div class="content2">
        <h3 style="margin-top: 35px">9. Mobile Usage</h3>
        <div style="margin-top: 20px;">
            The use of personal cell phones while at work may result in a hazard or distraction to the user and /or
            co-employees.
            This policy is meant to ensure that the cell phone use while at work is both safe and does not disrupt
            business
            operations.
            <br />
            <br />
            <b>A</b>. Mobile phones should be set to silent/vibrate in the work environment.<br />
            <b>B</b>. If it is necessary to make or answer a call, then it should be done so in a private area.<br />
            <b>C</b>. All personal calls should be limited to not more than 5 minutes.<br />
            <b>D</b>. If it is necessary to speak on the phone in the presence of the others, then do so in low
            tones.<br />
            <b>E</b>. Don't disturb your colleagues by answering personal calls at your desk.<br />
            <b>F</b>. Don't answer your mobile while in a meeting.<br />
            <b>G</b>. Ensure that you choose a ringtone that isn't likely to drive colleagues around the bend.<br />
            <b>H</b>. Use of headphones or earphones is strictly prohibited during office hours.<br /><br />
            If anyone is found on long personal calls without any reason, then they could be monetarily penalised or
            disciplinaryaction would be taken against them as per policies & procedures of the company.
        </div>
        <h3 style="margin-top: 35px">10. IT information storage and security</h3>
        <div style="margin-top: 20px;">
            Any storage devices (CD's, USI's, Floppy Disks used by employees at the workplace, located at company's
            address,
            acknowledge that these devices and their contents are the property of the company. Furthermore, it should be
            understood by employees that company equipment should be used for company business only during normal
            working hours. Downloading personal materials onto company equipment can be harmful to the said equipment
            and
            should not be done.
        </div>

        <h3 style="margin-top: 35px">11. Salary Transfer</h3>
        <div style="margin-top: 20px;">
            Salaries are transferred on 30/31st (last Day) of each month as per attendance cycle of 15th of previous
            month to
            14th of current month. If the salary date falls on a holiday, transfers will be distributed on the closest
            business day
            before the holiday. Any change in name, address, telephone number, marital status, Bank Details or
            exemptions
            claimed by an employee must be reported to the HR department at earliest possible.
        </div>
        <h3 style="margin-top: 35px">12. Software</h3>
        <div style="margin-top: 20px;">
            Company computers, internet and emails are privileged resources that must be used only to complete essential
            job-related functions. Employees are not permitted to download any "unauthorised software, files or programs
            and
            must receive permission from a supervisor before installing any new software on a company computer. Files or
            programs stored on company computers may not be copied for personal use or any unauthorised use whatsoever.
        </div>
    </div>
    <div class="page-break"></div>
    <div class="content2">
        <h3 style="margin-top: 25px">13. Good Attendance Policy</h3>
        <div style="margin-top: 20px;">
            The company maintains normal working hours of 10:00am. to 6:30pm. The Hours may vary depending on the work
            location and job responsibilities. Managers will provide employees with their work schedule. If an employee
            has any
            questions regarding his/her work schedule, then the employee should contact the CLH of the company directly.
            The
            company does not tolerate absenteeism without any substantial reason. Employees who will be late to or
            absent
            from work should notify his/her manager in advance, or as soon as practicable in the event of an emergency.
            Chronic
            absenteeism may result in disciplinary action.
        </div>
        <h3 style="margin-top: 25px">The other attendance rules are as follows:</h3>
        <div style="margin-top: 20px;">
            <b>A</b>. Working days are on rotational basis and decided by the management, with one week-off in weekdays
            (i.e.,
            Six
            days a week) being one day off. However, in case any employee is asked to attend office on a Week day or
            holiday,
            then it would be adjusted by compensatory leave on any other working day. Week-off once decided, can’t be
            swiped
            without management consent.
            <br />
            <br />
            <b>B</b>. Official time for the office will be 10:00 AM to 6:30 PM. However , Grace period is of 15 mins
            .You must
            reach the
            office by 10:15 AM Sharp. Lunch Time for the office will be of 30 minutes
            <br />
            <br />
            <b> C</b>. Prior permission from managers with approval must be obtained for any leave(s) application,
            whether
            explicit or
            implicit, and manager/ HR consent on the same is mandatory. All leave requests must be sent & approved
            through
            your mail to your manager/HR.
            <br />
            <br />
            <b>D</b>. Employees applying leave for more than 5 days must apply, at least 15 days prior to the date of
            leave. No
            application regarding leave(s) will be entertained for a shorter application period.
            <br />
            <br />
            <b>E</b>. If anybody needs leave on both one day before and after week-off/holiday, then it will be counted
            as
            sandwich
            leave and decrease his/her leave account by 3 days.
            <br />
            <br />
            <b>F</b>. If any leave is taken without any information or permission,then it will be marked as absent and
            salary
            will be
            deducted for that.
            <br />
            <br />
            <b>G</b>. If any employee leaves the office/ workspace/ client place/ department visit/ gone out for office
            work
            and goes
            back home, without intimation & due approval of HR in Unauthorised/ Uninformed manner, it will be treated as
            Half
            Day
        </div>

        <h3 style="margin-top: 35px">14. Dress Code</h3>
        <div style="margin-top: 20px;">
            A person's personal appearance and hygiene are a reflection of his character and attitude. Employees are
            expected
            to dress appropriately for their individual work responsibilities and positions. We expect you to be dressed
            in formal
            attire on all regular working days.
        </div>
    </div>
    <div class="page-break"></div>
    <div class="content2">
        <h3 style="margin-top: 20px">15. The company perceives the following holidays</h3>
        <div
            style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 10px; margin-top: 20px; margin-left: 20px;">
            <h4>1. Republic Day</h4>
            <h4>2. Independence Day</h4>
            <h4>3. Gandhi Jayanti</h4>
            <h4>4. Raksha Bandhan</h4>
            <h4>5. Dusshera</h4>
            <h4>6. Diwali</h4>
            <h4>7. Holi</h4>
            <h4>8. Bhai Dooj</h4>
            <h4>9. Maha Shivratri</h4>
            <h4>10. Guru Nanak Jayanti</h4>
        </div>
        <h3 style="margin-top: 30px">16. Deduction from Salary</h3>
        <div style="margin-top: 20px;">
            The Company is obliged to deduct Income Tax at source as per provisions of Income Tax Act and Rules.
            Accordingly,
            you are required to submit all required proof of permitted savings/ investments and other details from time
            to time to
            enable the company to comply with the provisions of law in the event of non-compliance by you as aforesaid.
            If the
            company is required to pay any interest or payment under Income Tax Act, it shall deduct the amount as may
            be paid
            or payable from your salary or other payments and you shall allow the company to comply with these
            requirements
            without objection.<br /><br />
            <b>A</b>. Penalty: The employees who have once joined the company and are working at our client location
            should
            ensure
            that after resignation/ termination, they should not work at any of our client sites for at least next 2
            years. If in case
            they are found working at any of our client sites strict legal action would be taken or can be penalised by
            the
            company which in no case shall be less than 3 months last drawn salary.<br /><br />
            <b>B</b>. During the course of employment with the Company, you will acquire, gain, generate, gather, and
            develop
            knowledge of and be given access to business information about products activities, know-how methods or
            refinements and business plans and business secrets and other information concerning the products/ business
            of the
            company, hereinafter called the "SECRETS". You will be liable for prosecution for damages for divulgence,
            sharing or
            parting any of such information during course of employment and on cessation for at least 2 years period.
        </div>
    </div>
    <div class="page-break"></div>
    <div class="content2">
        <h3 style="margin-top: 20px">17. Privacy</h3>
        <div style="margin-top: 20px;">
            Employee and employer share a relationship based on trust and mutual respect. However the company retains
            the
            right to access all company property including computers, desks, file cabinets, storage facilities, and
            files and folders,
            electronic or otherwise, all the time. Employees should not have any expectations of privacy when on company
            grounds or while using company property.
            <br />
            <br />
            All documents, files, voice-mails and electronic information, including mails and other communications,
            created,
            received or maintained on or through company property are the property of the company and not of the
            employee.
            Therefore employees should have no expectation of privacy over those files,documents or IPES.
        </div>
        <h3 style="margin-top: 35px">18. Resignation</h3>
        <div style="margin-top: 20px;">
            of resignation. The Employer may waive the notice period in whole or in part at any time by providing
            payment of
            regular wages for the period so waived. Full and Final Settlement will be done after 45 Days of Serving the
            last day
            of Notice period if all the company assets are submitted, otherwise, counting of 45 days would start after
            submitting
            the Assets.<br />
            No leave shall be permissible during the 30-day notice period. However, week offs are allowed.<br /><br />
            During the 30-day notice period, you are expected to achieve the set target assigned by your manager. For
            the
            successful completion of your notice period, you need to achieve a minimum of 75% of the given target. In
            the case
            of not attaining 75% of the set target, it will lead to a strict deduction in your full and final settlement
            (F&F) that shall
            be decided by the management.<br /><br />
            It is your responsibility to contact us at any time during this notice period regarding your performance and
            to seek
            assistance in removing any roadblocks you may come up against that may impede your progress.<br /><br />
            <h3>INCENTIVE</h3> - Company recognises the previous achievements of the employee, thereby assuring that
            incentive
            allowance shall be paid as per the Company’s internal policy.
            <br /><br />
            Employees Incentive compensation is not the part of Full and Final Settlement (F&F settlement).<br /><br />
            Employees shall be entitled for their due incentive compensation only when the implementation of the event
            is
            successfully executed on the promised date.<br /><br />
            If the Cancellation of the said event is made, the incentive compensation shall be fortified immediately
        </div>
    </div>
    <div class="page-break"></div>
    <div class="content2">
        <h3 style="margin-top: 20px">19. Personnel Files</h3>
        <div style="margin-top: 20px;">
            The company maintains a personnel file for each employee. These files are kept confidential to the extent
            possible, it
            is important that personnel files accurately reflect each employee's personal information. Employees are
            expected to
            inform the company of any change in name, address, home phone number, and home address, and marital status,
            number of dependents or emergency contact information.
        </div>
        <h3 style="margin-top: 30px">20. Transfer/ Assignment</h3>
        <div style="margin-top: 20px;">
            Your services are liable to be transferred or loaned or assigned with/ without transfer, wholly or
            partially, from one
            department to another or to an office/ branch/ any client location and vice-versa or office/ branch to
            another office/
            branch of an associate company or client, existing or to come into existence in the future or any of the
            Company's
            branch offices or locations anywhere in India or abroad or any other concern where this Company has any
            interest.
            <br />
            In such a case, you will abide by responsibilities expressly vested or implied or communicated and shall
            follow rules
            and regulations of the department / office/ client, establishment, jointly or separately, without any
            compensation or
            extra remuneration or provision of accommodation. You thereupon, may be governed by the service conditions
            and
            other terms of the said concern as may be applicable. The aforesaid Clause will not give you any right to
            claim
            employment in any associate, client or sister concern or ask for a common seniority with the employees of
            the client/
            sister/ associate concern.
        </div>
        <h3 style="margin-top: 20px">21. General Expectation</h3>
        <div style="margin-top: 20px;">
            The company expects every employee to act in a professional manner. Satisfactory performance of job duties
            and
            responsibilities is key to this expectation. Employees should attempt to achieve their job objectives, and
            act with
            diligence and consideration at all times. Carelessness and other dodging activities can result in
            disciplinary action,
            up to and including termination.<br /><br />
            <b>Disclaimer: </b> Please note that management possesses the right to make changes to the above-mentioned
            policies in
            its sole discretion without any prior intimation.<br /><br />
            I acknowledge that I have read and understood the above rules and policies and procedures of the employee
            handbook under which they're made, in its entirety and agree to abide by them.
        </div>
    </div>
    <div class="page-break"></div>
    <div class="content2">

    </div>
</body>

</html>
