<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class CourseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        // $courses = Course::orderBy('id', 'DESC')->paginate(10);_get()
        $courses = Course::orderBy('id', 'desc')->paginate(10);
        return view('admin.courses.index', [
            'courses' => $courses
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
        $categories = Category::all();
        return view('admin.courses.create', [
            'categories' => $categories
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    // public function store(Request $request)
    // {
    //     //
    //     $validate = $request->validate([
    //         'name' => 'required|string|max:255',
    //         'category_id' => 'required|integer',
    //         'cover' => 'required|iamge|mimes:jpeg,png,jpg,svg',
    //     ]);

    //     DB::beginTransaction();

    //     try {
    //         if($request->hasFile('cover')) {
    //             $coverPath = $request->file('cover')->store('product_covers', 'public');
    //             $validate('cover') = $coverPath;
    //     }
    //     $validate['slug'] = Str::slug($request->name);
    //     $newCourse = Course::create($validate);

    //     DB::commit();

    //     return redirect()->route('dashboard.courses.index');
    //     }

    //     catch(\Exception $e) {
    //         DB::rollBack();
    //         $error = ValidateExeption::withMessages({
    //             'system_error' => ['System error'. $e->getMessage()]
    //         })
    //         throw $error;
    //     }
    // }

    public function store(Request $request)
{
    // Validasi request
    $validate = $request->validate([
        'name' => 'required|string|max:255',
        'category_id' => 'required|integer',
        'cover' => 'required|image|mimes:jpeg,png,jpg,svg',
    ]);

    DB::beginTransaction();

    try {
        // Cek jika file 'cover' ada
        if ($request->hasFile('cover')) {
            // Simpan file cover dan masukkan ke dalam validasi
            $coverPath = $request->file('cover')->store('product_covers', 'public');
            $validate['cover'] = $coverPath;  // Menggunakan array notation
        }

        // Tambahkan slug ke dalam data validasi
        $validate['slug'] = Str::slug($request->name);

        // Buat entitas Course baru
        $newCourse = Course::create($validate);

        // Commit transaksi
        DB::commit();

        // Redirect ke halaman index
        return redirect()->route('dashboard.courses.index');
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
    public function show(Course $course)
    {
        //
        // $students = Course::find($course->id)->students;
        $students = $course->students()->orderBy('id', 'desc')->get();
        $questions = $course->questions()->orderBy('id', 'desc')->get();
        return view('admin.courses.manage', [
            'course' => $course,
            'students' => $students,
            'questions' => $questions
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Course $course)
    {
        //
        $categories = Category::all(); 
        return view('admin.courses.edit', [
            'course' => $course,
            'categories' => $categories,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Course $course)
    {
        //
        {
            // Validasi request
            $validate = $request->validate([
                'name' => 'required|string|max:255',
                'category_id' => 'required|integer',
                'cover' => 'sometimes|image|mimes:jpeg,png,jpg,svg',
            ]);
        
            DB::beginTransaction();
        
            try {
                // Cek jika file 'cover' ada
                if ($request->hasFile('cover')) {
                    // Simpan file cover dan masukkan ke dalam validasi
                    $coverPath = $request->file('cover')->store('product_covers', 'public');
                    $validate['cover'] = $coverPath;  // Menggunakan array notation
                }
        
                // Tambahkan slug ke dalam data validasi
                $validate['slug'] = Str::slug($request->name);
        
                // Update entitas Course
                $course->update($validate);
        
                // Commit transaksi
                DB::commit();
        
                // Redirect ke halaman index
                return redirect()->route('dashboard.courses.index');
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
    public function destroy(Course $course)
    {
        //
        try {
            $course->delete();
            return redirect()->route('dashboard.courses.index');
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
