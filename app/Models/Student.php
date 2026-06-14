<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    protected $fillable = [
        'barcode', 'first_name', 'last_name', 'middle_name',
        'course', 'institute', 'total_hours', 'used_hours',
        'remaining_hours', 'semester', 'semester_year',
        'is_logged_in', 'login_time',
    ];

    protected $casts = [
        'is_logged_in' => 'boolean',
        'login_time' => 'datetime',
    ];

    public function usageRecords()
    {
        return $this->hasMany(UsageRecord::class);
    }

    public static function getCurrentSemester(): string
    {
        $month = now()->month;

        return ($month >= 1 && $month <= 5)
            ? '1st Semester (January - May)'
            : '2nd Semester (August - December)';
    }

    public static function getCurrentSemesterKey(): string
    {
        $month = now()->month;
        $year = now()->year;

        return ($month >= 1 && $month <= 5) ? "1st-{$year}" : "2nd-{$year}";
    }
}
