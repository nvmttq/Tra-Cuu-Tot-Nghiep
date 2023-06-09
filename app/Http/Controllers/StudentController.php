<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Student;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\StudentImport;
use App\Imports\StudentTollectionImport;
use Illuminate\Support\Facades\Redirect;
use SplFileObject;

class StudentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $students = DB::select("SELECT * FROM students");
        return view('student.index', ['students' => $students]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('student.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $student_id = $request->input('student_id');
        $student = DB::table('students')->where('student_id', $student_id)->first();

        //check exists student
        if($student){
            return redirect()->route('student.create')->with('message', 'Sinh viên đã tồn tại, vui lòng kiểm tra lại MSSV/MSHV');
        }

        Student::create($request->all());
        return redirect()->route('student.index')->with('message', 'Thêm thông tin thành công.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request)
    {
        $id = $request->input('student-id');

        if ($id == "") {
            return Redirect::to('/')->with('message', 'Vui lòng nhập MSSV/MSHV');
        }

        if ($id) {
            $id = str_replace(' ', '', $id);
            $id = strtoupper($id);
            $students = DB::select("SELECT * FROM students WHERE student_id='$id'");

            if ($students) {
                return view('desktop-details', ['student' => $students[0]]);
            }
        }

        return Redirect::to('/')->with('message', 'Tra cứu thất bại, không tìm thấy trong CSDL');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $student = Student::find($id);

        if (!$student) {
            return redirect()->route('student.index');
        }

        return view('student.edit', compact('student'));
    }

    public function find(Request $request){
        $id = $request->input('student_id');

        if(!$id){
            return response()->json(['error' => 'Vui lòng nhập mã'], 404);
        }
        $id = str_replace(' ', '', $id);
        $id = strtoupper($id);
        $student = DB::select("SELECT * FROM students WHERE student_id='$id'");

        if(!$student){
            return response()->json(['error' => 'Không tìm thấy thông tin của mã này'], 404);
        }

        return response()->json(['student' => $student], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $student = Student::find($id);

        if (!$student) {
            return redirect()->route('student.index')->with('error', 'Thông tin không tồn tại.');
        }

        $student->student_id = $request->input('student_id');
        $student->name = $request->input('name');
        $student->image = $request->input('image');
        $student->degree = $request->input('degree');
        $student->majour = $request->input('majour');
        $student->participation_time = $request->input('participation_time');
        $student->location = $request->input('location');
        $student->seat = $request->input('seat');
        $student->save();

        return redirect()->route('student.index')->with('message', 'Cập nhật thông tin thành công.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $student = Student::find($id);

        if (!$student) {
            return redirect()->route('student.index');
        }

        $student->delete();

        return redirect()->route('student.index')->with('message', 'Xoá thông tin thành công.');
    }

    public function upload(Request $request)
    {
        $file = $request->file('excelFile');

        if ($file === null) {
            return redirect()->back()->with('message', 'Chưa đính kèm tệp.');
        }

        if ($file->getClientOriginalExtension() !== 'csv') {
            return redirect()->back()->with('message', 'Vui lòng đính kèm tệp có định dạng csv');
        }

        $path = $file->getRealPath();
        $csv = new SplFileObject($path, 'r');
        $csv->setFlags(SplFileObject::READ_CSV);

        $temp = 0;
        $exists_students = array();

        foreach ($csv as $row) {
            if (count($row) >= 8) {
                $student = [
                    'student_id' => $row[0],
                    'name' => $row[1],
                    'image' => $row[2],
                    'degree' => $row[3],
                    'majour' => $row[4],
                    'participation_time' => $row[5],
                    'location' => $row[6],
                    'seat' => $row[7]
                ];

                if ($temp > 0) {
                    $exists_student = DB::table('students')->where('student_id', $student['student_id'])->first();
                    if($exists_student){
                        array_push($exists_students, $student['student_id']);
                    }
                    else
                    {
                        Student::create($student);
                    }
                }
                $temp = 1;
            }
        }
        if($exists_students){
            return redirect()->back()->with('message', 'Tệp đã được tải lên và dữ liệu đã được lưu vào cơ sở dữ liệu.')->with('exists_students', $exists_students);
        }
        return redirect()->back()->with('message', 'Tệp đã được tải lên và dữ liệu đã được lưu vào cơ sở dữ liệu.');
    }

    public function destroyAll()
    {
        DB::table('students')->truncate();
        return redirect()->back()->with('message', 'Đã xóa toàn bộ dữ liệu.');
    }

    public function downloadSampleExcel()
    {
        $file = public_path('Files/mau_them_danh_sach_svhv_tot_nghiep.csv');
        $headers = ['Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'];

        return response()->download($file, 'file.csv', $headers);
    }
}
