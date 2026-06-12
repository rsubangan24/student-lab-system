<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Student;


class StudentController extends Controller {

    public function index() {

        // $students = Student::orderBy('last_name', 'asc')->get()->map(function($student) {
        //     return [
        //         'id'              => $student->id,
        //         'barcode'         => $student->barcode,
        //         'firstName'       => $student->first_name,       // <-- Convert to frontend key
        //         'lastName'        => $student->last_name,        // <-- Convert to frontend key
        //         'middleName'      => $student->middle_name,      // <-- Convert to frontend key
        //         'course'          => $student->course,
        //         'institute'       => $student->institute,
        //         'totalHours'      => (int) $student->total_hours,
        //         'usedHours'       => (int) $student->used_hours,
        //         'remainingHours'  => (int) $student->remaining_hours,
        //         'semester'        => $student->semester,
        //         'semesterYear'    => $student->semester_year,
        //         'isLoggedIn'      => (bool) $student->is_logged_in,
        //         'loginTime'       => $student->login_time,
        //         'createdAt'       => $student->created_at ? $student->created_at->toISOString() : null,
        //     ];
        // });

        $students = Student::all();

        // 2. Map the database snake_case columns to your frontend camelCase properties
        $transformedStudents = $students->map(function ($student) {
            return [
                'id'             => $student->id,
                'barcode'        => $student->barcode,
                'firstName'      => $student->first_name,  // <-- Vital for Vue frontend display mapping
                'lastName'       => $student->last_name,   // <-- Vital for Vue frontend display mapping
                'middleName'     => $student->middle_name, // <-- Vital for Vue frontend display mapping
                'course'         => $student->course,
                'institute'      => $student->institute,
                'totalHours'     => (float) $student->total_hours,
                'usedHours'      => (float) $student->used_hours,
                'remainingHours' => (float) $student->remaining_hours,
                'semester'       => $student->semester,
                'semesterYear'   => $student->semester_year,
                'isLoggedIn'     => (bool) $student->is_logged_in,
                'loginTime'      => $student->login_time,
            ];
        });

        // 3. Securely return the array payload as a clean JSON response
        return response()->json($transformedStudents, 200);
        
        // return Student::orderBy('last_name', 'asc')->get();
    }   

    public function store(Request $request) {
        $request->validate([
            'barcode'     => 'required|unique:students,barcode',
            'first_name'  => 'required|string',
            'last_name'   => 'required|string',
            'middle_name' => 'nullable|string',
            'course'      => 'required|string',
            'institute'   => 'required|string',
        ]);

        // Check duplicate name
        $duplicate = Student::query()->where('first_name', $request->first_name)
            ->where('last_name', $request->last_name)
            ->where('middle_name', $request->middle_name)
            ->first();

        if ($duplicate) {
            return response()->json([
                'message' => "Student {$request->first_name} {$request->last_name} already exists with barcode {$duplicate->barcode}"
            ], 422);
        }

        $student = Student::create([
            'barcode'         => $request->barcode,
            'first_name'      => $request->first_name,
            'last_name'       => $request->last_name,
            'middle_name'     => $request->middle_name,
            'course'          => $request->course,
            'institute'       => $request->institute,
            'total_hours'     => 100,
            'used_hours'      => 0,
            'remaining_hours' => 100,
            'semester'        => Student::getCurrentSemester(),
            'semester_year'   => Student::getCurrentSemesterKey(),
        ]);

        return response()->json($student, 201);
    }   

    public function update(Request $request, Student $student) {
        $request->validate([
            'barcode' => "required|unique:students,barcode,{$student->id}",
            'first_name' => 'required|string',
            'last_name' => 'required|string',
        ]);

        $student->update($request->all());
        return response()->json($student);
    }

    public function destroy(Student $student) {
        if ($student->is_logged_in) {
            return response()->json(['message' => 'Cannot delete a logged-in student'], 422);
        }
        $student->query()->delete();
        return response()->json(['message' => 'Deleted']);
    }
}