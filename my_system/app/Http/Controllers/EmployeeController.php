<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EmployeeController extends Controller
{
    // GET /api/employees
    public function index()
    {
        $employees = DB::table('employee')
            ->leftJoin('departments', 'employee.dept_id', '=', 'departments.dept_id')
            ->select('employee.*', 'departments.dept_name')
            ->orderBy('employee.emp_id')
            ->get();
        return response()->json($employees);
    }

    // POST /api/employees
    public function store(Request $request)
    {
        $id = DB::table('employee')->insertGetId([
            'emp_name' => $request->emp_name,
            'position' => $request->position ?? null,
            'phone'    => $request->phone    ?? null,
            'dept_id'  => $request->dept_id  ?? null,
        ]);

        $employee = DB::table('employee')
            ->leftJoin('departments', 'employee.dept_id', '=', 'departments.dept_id')
            ->select('employee.*', 'departments.dept_name')
            ->where('employee.emp_id', $id)
            ->first();

        return response()->json($employee, 201);
    }

    // PUT /api/employees/{id}
    public function update(Request $request, $id)
    {
        DB::table('employee')->where('emp_id', $id)->update([
            'emp_name' => $request->emp_name,
            'position' => $request->position,
            'phone'    => $request->phone,
            'dept_id'  => $request->dept_id ?? null,
        ]);

        $employee = DB::table('employee')
            ->leftJoin('departments', 'employee.dept_id', '=', 'departments.dept_id')
            ->select('employee.*', 'departments.dept_name')
            ->where('employee.emp_id', $id)
            ->first();

        return response()->json($employee);
    }

    // DELETE /api/employees/{id}
    public function destroy($id)
    {
        DB::table('employee')->where('emp_id', $id)->delete();
        return response()->json(['message' => 'លុបបានជោគជ័យ']);
    }
}