<?php

use App\Http\Controllers\CourseController;
use App\Http\Controllers\CourseQuestionController;
use App\Http\Controllers\CourseStudentController;
use App\Http\Controllers\LearningController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\StudentAnswerController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::prefix('dashboard')->name('dashboard.')->group(function () {

        Route::resource('courses', CourseController::class)
        ->middleware('role:teacher');

        // menampilkan pertanyaan -> teacher
        Route::get('/course/student/show/{course}', [CourseStudentController::class, 'index'])
        ->middleware('role:teacher')
        ->name('course.course_student.index');
        
        // membuat pertanyaan -> teacher
        Route::get('/course/question/create/{course}', [CourseQuestionController::class, 'create'])
        ->middleware('role:teacher')
        ->name('course.create.question');

        // meniyimpan pertanyaan -> teacher
        Route::post('/course/question/save/{course}', [CourseQuestionController::class, 'store'])
        ->middleware('role:teacher')
        ->name('course.create.question.store');

        Route::resource('courses_questions', CourseQuestionController::class)
        ->middleware('role:teacher');

        // membuat soal untuk student -> teacher
        Route::get('/course/student/create/{course}', [CourseStudentController::class, 'create'])
        ->middleware('role:teacher')
        ->name('course.course_student.create');

        // menyimpan soal student -> teacher
        Route::post('/course/student/save/{course}', [CourseStudentController::class, 'store'])
        ->middleware('role:teacher')
        ->name('course.course_student.store');

        // menampilkan soal yang telah dijawab -> student
        Route::get('learning/finished/{course}', [LearningController::class, 'learning_finished'])
        ->middleware('role:student')
        ->name('learning.report_course');

        // menampilkan laporan nilai dan soal di course -> student
        Route::get('learning/report/{course}', [LearningController::class, 'learning_report'])
        ->middleware('role:student')
        ->name('learning.finished_course');

        // menampilkan semua course yang telah diikuti -> student
        Route::get('/learning', [LearningController::class, 'index'])
        ->middleware('role:student')
        ->name('learning.index');

        // menampilkan pertanyaan -> student
        Route::get('/learning/{course}/{question}', [LearningController::class, 'learning'])
        ->middleware('role:student')
        ->name('learning.course');

        // menyimpan jawaban student -> student
        Route::post('/learning/{course}/{question}', [StudentAnswerController::class, 'store'])
        ->middleware('role:student')
        ->name('learning.course.answer.store');
    });
});

require __DIR__.'/auth.php';
