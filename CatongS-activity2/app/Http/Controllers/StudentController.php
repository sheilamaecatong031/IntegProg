<?php

namespace App\Http\Controllers;

use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class StudentController extends Controller
{
    // GET /api/students
    public function index()
    {
        return response()->json([
            'success' => true,
            'data'    => Student::all(),
        ]);
    }

    // POST /api/students
    public function store(Request $request)
    {
        $validated = $request->validate([
            'student_number' => 'required|string|max:50|unique:students,student_number',
            'first_name'     => 'required|string|max:255',
            'last_name'      => 'required|string|max:255',
            'email'          => 'required|email|unique:students,email',
            'course'         => 'required|string|max:255',
            'year_level'     => 'required|integer|min:1|max:6',
        ]);

        $student = Student::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Student created successfully',
            'data'    => $student,
        ], 201);
    }

    // GET /api/students/{student}
    public function show(Student $student)
    {
        return response()->json([
            'success' => true,
            'data'    => $student,
        ]);
    }

    // PUT/PATCH /api/students/{student}
    public function update(Request $request, Student $student)
    {
        $validated = $request->validate([
            'student_number' => ['sometimes', 'required', 'string', 'max:50',
                                Rule::unique('students', 'student_number')->ignore($student->id)],
            'first_name'     => 'sometimes|required|string|max:255',
            'last_name'      => 'sometimes|required|string|max:255',
            'email'          => ['sometimes', 'required', 'email',
                                Rule::unique('students', 'email')->ignore($student->id)],
            'course'         => 'sometimes|required|string|max:255',
            'year_level'     => 'sometimes|required|integer|min:1|max:6',
        ]);

        $student->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Student updated successfully',
            'data'    => $student,
        ]);
    }

    // DELETE /api/students/{student}
    public function destroy(Student $student)
    {
        $student->delete();

        return response()->json([
            'success' => true,
            'message' => 'Student deleted successfully',
        ]);
    }
}