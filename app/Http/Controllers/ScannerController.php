<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\UsageRecord;
use Illuminate\Http\Request;

class ScannerController extends Controller
{
    public function scan(Request $request)
    {
        $request->validate(['barcode' => 'required|string']);

        $student = Student::query()->where('barcode', $request->barcode)->first();

        if (! $student) {
            return response()->json(['action' => 'error', 'message' => 'Student not found'], 404);
        }

        if (! $student->is_logged_in) {
            return $this->checkIn($student);
        } else {
            return $this->checkOut($student);
        }
    }

    private function checkIn(Student $student)
    {
        if ($student->remaining_hours <= 0) {
            return response()->json(['action' => 'error', 'message' => 'No remaining hours'], 422);
        }

        $student->query()->update([
            'is_logged_in' => true,
            'login_time' => now(),
        ]);

        return response()->json([
            'action' => 'check-in',
            'message' => "Welcome, {$student->first_name}! Check-in successful.",
            'student' => $student->fresh(),
        ]);
    }

    private function checkOut(Student $student)
    {
        $loginTime = $student->login_time;
        $logoutTime = now();
        $minutesUsed = $logoutTime->diffInMinutes($loginTime);
        $hoursUsed = round($minutesUsed / 60, 2);
        $actualHours = min($hoursUsed, $student->remaining_hours);
        $newUsed = min($student->used_hours + $actualHours, 100);
        $newRemaining = max($student->remaining_hours - $actualHours, 0);

        UsageRecord::create([
            'student_id' => $student->id,
            'student_barcode' => $student->barcode,
            'student_name' => "{$student->last_name}, {$student->first_name} {$student->middle_name}",
            'course' => $student->course,
            'institute' => $student->institute,
            'login_time' => $loginTime,
            'logout_time' => $logoutTime,
            'hours_used' => $actualHours,
            'remaining_hours' => $newRemaining,
            'date' => now()->toDateString(),
        ]);

        $student->query()->update([
            'is_logged_in' => false,
            'login_time' => null,
            'used_hours' => $newUsed,
            'remaining_hours' => $newRemaining,
        ]);

        return response()->json([
            'action' => 'check-out',
            'message' => "Goodbye, {$student->first_name}! Used {$actualHours}h. Remaining: {$newRemaining}h.",
            'student' => $student->fresh(),
        ]);
    }
}
