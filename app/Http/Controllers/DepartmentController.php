<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DepartmentController extends Controller
{
    public function index()
    {
        return response()->json(
            DB::table('departments')
                ->leftJoin('employee', 'departments.dept_id', '=', 'employee.dept_id')
                ->select('departments.*', DB::raw('COUNT(employee.emp_id) as emp_count'))
                ->groupBy('departments.dept_id', 'departments.dept_name')
                ->orderBy('departments.dept_name')
                ->get()
        );
    }

        public function store(Request $request)
    {
        $request->validate(['dept_name' => 'required|string|max:100|unique:departments,dept_name']);
        DB::table('departments')->insert(['dept_name' => $request->dept_name]);
        $dept = DB::table('departments')->where('dept_name', $request->dept_name)->first();
        $dept->emp_count = 0;
        return response()->json($dept, 201);
    }

    public function update(Request $request, $id)
    {
        $request->validate(['dept_name' => 'required|string|max:100']);
        DB::table('departments')->where('dept_id', $id)->update(['dept_name' => $request->dept_name]);
        $dept = DB::table('departments')->find($id);
        $dept->emp_count = DB::table('employee')->where('dept_id', $id)->count();
        return response()->json($dept);
    }

    public function destroy($id)
    {
        DB::table('departments')->where('dept_id', $id)->delete();
        return response()->json(['message' => 'លុបបានជោគជ័យ']);
    }
}