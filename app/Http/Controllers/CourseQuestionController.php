<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\CourseQuestion;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CourseQuestionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Course $course)
    {
        //
        // dd($course);
        $students = $course->students()->orderBy('id', 'desc')->get();
        return view('admin.questions.create', [
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
        {
            // Validasi request
            $validate = $request->validate([
                'question' => 'required|string|max:255',
                'answer' => 'required|array',
                'answer.*' => 'required|string',
                'correct_answer' => 'required|integer',
            ]);
        
            DB::beginTransaction();
        
            try {

                $question = $course->questions()->create([
                    'question' => $request->question,
                ]);

                foreach($request->answer as $index => $answerText) {
                    $isCorrect =  ($request->correct_answer == $index);
                    $question->answers()->create([
                        'answer' => $answerText,
                        'is_correct' => $isCorrect,
                    ]);
                }
        
                // Commit transaksi
                DB::commit();
        
                // Redirect ke halaman index
                return redirect()->route('dashboard.courses.show', $course->id);
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
    }

    /**
     * Display the specified resource.
     */
    public function show(CourseQuestion $courseQuestion)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(CourseQuestion $courseQuestion)
    {
        //
        $course = $courseQuestion->course;
        $students = $course->students()->orderBy('id', 'desc')->get();
        return view('admin.questions.edit', [
            'courseQuestion' => $courseQuestion,
            'course' => $course,
            'students' => $students,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, CourseQuestion $courseQuestion)
    {
        //
        {
            // Validasi request
            $validate = $request->validate([
                'question' => 'required|string|max:255',
                'answer' => 'required|array',
                'answer.*' => 'required|string',
                'correct_answer' => 'required|integer',
            ]);
        
            DB::beginTransaction();
        
            try {

                $courseQuestion->update([
                    'question' => $request->question,  
                ]);

                $courseQuestion->answers()->delete();

                foreach($request->answer as $index => $answerText) {
                    $isCorrect =  ($request->correct_answer == $index);
                    $courseQuestion->answers()->create([
                        'answer' => $answerText,
                        'is_correct' => $isCorrect,
                    ]);
                }
        
                // Commit transaksi
                DB::commit();
        
                // Redirect ke halaman index
                return redirect()->route('dashboard.courses.show', $courseQuestion->course_id);
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

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(CourseQuestion $courseQuestion)
    {
        //
        try {
            $courseQuestion->delete();
            return redirect()->route('dashboard.courses.show', $courseQuestion->course_id);
        }
        catch (\Exception $e) {
            // Rollback transaksi jika terjadi error
            DB::rollBack();
    
            // Lempar exception dengan pesan kesalahan
            $error = ValidationException::withMessages([
                'system_error' => ['System error: ' . $e->getMessage()]
            ]);
    
            throw $error;
        }

    }
}
