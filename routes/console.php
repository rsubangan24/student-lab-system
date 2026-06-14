<?php

use App\Models\Student;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::call(function () {
    // Check if semester ended and reset
    $month = now()->month;
    // End of May (semester 1) or end of December (semester 2)
    if ($month == 6 || $month == 1) {
        Student::query()->update([
            'used_hours' => 0,
            'remaining_hours' => 100,
            'semester' => Student::getCurrentSemester(),
            'semester_year' => Student::getCurrentSemesterKey(),
        ]);
    }
})->monthly();
