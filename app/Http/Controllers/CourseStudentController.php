<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\CourseStudent;
use App\Models\StudentAnswer;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CourseStudentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Course $course)
    {
        $students = $course->students()->orderBy('id', 'desc')->get();
        $questions = $course->questions()->orderBy('id', 'desc')->get();
        $totalQuestion = $questions->count();
    
        foreach ($students as $student) {
            $studentAnswers = StudentAnswer::whereHas('question', function ($query) use ($course) {
                $query->where('course_id', $course->id);
            })->where('user_id', $student->id)->get();
            
            $answerCount = $studentAnswers->count();
            $correctAnswerCount = $studentAnswers->where('answer', 'correct')->count();
    
            if ($answerCount == 0) {
                $student->status = 'Not Started';
            } else if ($correctAnswerCount < $totalQuestion) {
                $student->status = 'Not Passed';
            } else if ($correctAnswerCount == $totalQuestion) {  // Kondisi diubah
                $student->status = 'Passed';
            }
        }
    
        return view('admin.students.index', [
            'course' => $course,
            'questions' => $questions,
            'students' => $students,      
        ]);
    }
    
    /**
     * Show the form for creating a new resource.
     */
    public function create(Course $course)
    {
        //
        // dd($course);
        $students = $course->students()->orderBy('id', 'desc')->get();
        return view('admin.students.add_students', [
            'course' => $course,
            'students' => $students,      
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Course $course)
    {
        //
        $request->validate([
            'email' => 'required|string',
        ]);

        $user = User::where('email', $request->email)->first();
        if(!$user){
            $error = ValidationException::withMessages([
                'system_error' => ['Student email unavailable']
            ]);
            throw $error;
        } 
        $isEnrolled = $course->students()->where('user_id', $user->id)->exists();

        if($isEnrolled){
            $error = ValidationException::withMessages([
                'system_error' => ['Student is already enrolled']
            ]);
            throw $error;
        };

        DB::beginTransaction();

        try {
            $course->students()->attach($user->id);
            DB::commit();
            // 
            return redirect()->route('dashboard.course.course_student.index', $course);
            // 
             } catch (\Exception $e) {
                // Rollback transaksi jika terjadi error
                DB::rollBack();
        
                // Lempar exception dengan pesan kesalahan
                $error = ValidationException::withMessages([
                    'system_error' => ['System error: ' . $e->getMessage()]
                ]);
        
                throw $error;
            }
        }

    /**
     * Display the specified resource.
     */
    public function show(CourseStudent $courseStudent)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(CourseStudent $courseStudent)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, CourseStudent $courseStudent)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(CourseStudent $courseStudent)
    {
        //
    }
}
