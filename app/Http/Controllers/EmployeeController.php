<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class EmployeeController extends Controller
{
    private function withAvatarUrl($employee)
    {
        if ($employee) {
            $employee->avatar_url = $employee->avatar ? asset('storage/' . $employee->avatar) : null;
        }
        return $employee;
    }

    // GET /api/employees
    public function index()
    {
        $employees = DB::table('employee')
            ->leftJoin('departments', 'employee.dept_id', '=', 'departments.dept_id')
            ->select('employee.*', 'departments.dept_name')
            ->orderBy('employee.emp_id')
            ->get()
            ->map(fn ($e) => $this->withAvatarUrl($e));

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

        return response()->json($this->withAvatarUrl($employee), 201);
    }

    // POST /api/employees/{id}
    // ✅ Uses POST (not PUT) so the browser can send multipart/form-data,
    //    which is required for file/avatar uploads.
    public function update(Request $request, $id)
    {
        $employee = DB::table('employee')->where('emp_id', $id)->first();
        if (!$employee) {
            return response()->json(['message' => 'រកមិនឃើញបុគ្គលិក'], 404);
        }

        $request->validate([
            'emp_name' => 'required|string|max:255',
            'avatar'   => 'nullable|image|max:4096', // 4MB max
        ]);

        $updateData = [
            'emp_name' => $request->emp_name,
            'position' => $request->position,
            'phone'    => $request->phone,
            'dept_id'  => $request->dept_id ?? null,
        ];

        // Handle avatar upload
        if ($request->hasFile('avatar')) {
            if ($employee->avatar && Storage::disk('public')->exists($employee->avatar)) {
                Storage::disk('public')->delete($employee->avatar);
            }
            $updateData['avatar'] = $request->file('avatar')->store('employee_avatars', 'public');
        }

        DB::table('employee')->where('emp_id', $id)->update($updateData);

        $updated = DB::table('employee')
            ->leftJoin('departments', 'employee.dept_id', '=', 'departments.dept_id')
            ->select('employee.*', 'departments.dept_name')
            ->where('employee.emp_id', $id)
            ->first();

        return response()->json($this->withAvatarUrl($updated));
    }

    // DELETE /api/employees/{id}
    public function destroy($id)
    {
        $employee = DB::table('employee')->where('emp_id', $id)->first();
        if ($employee && $employee->avatar && Storage::disk('public')->exists($employee->avatar)) {
            Storage::disk('public')->delete($employee->avatar);
        }

        DB::table('employee')->where('emp_id', $id)->delete();
        return response()->json(['message' => 'លុបបានជោគជ័យ']);
    }
}