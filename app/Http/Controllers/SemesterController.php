<?php

namespace App\Http\Controllers;

use App\Models\Student;

class SemesterController extends Controller
{
    public function refresh()
    {
        Student::query()->update([
            'used_hours' => 0,
            'remaining_hours' => 100,
            'semester' => Student::getCurrentSemester(),
            'semester_year' => Student::getCurrentSemesterKey(),
            'is_logged_in' => false,
            'login_time' => null,
        ]);

        return response()->json(['message' => 'Semester refreshed! All students reset to 100 hours.']);
    }
}
