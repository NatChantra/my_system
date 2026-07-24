<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LeaveRequestController extends Controller
{
    // ===== Leave Requests =====
    public function index(Request $request)
    {
        $query = DB::table('leave_requests')
            ->join('employee', 'leave_requests.emp_id', '=', 'employee.emp_id')
            ->join('leave_types', 'leave_requests.leave_type_id', '=', 'leave_types.leave_type_id')
            ->select('leave_requests.*', 'employee.emp_name', 'leave_types.type_name');

        if ($request->emp_id) $query->where('leave_requests.emp_id', $request->emp_id);
        if ($request->status) $query->where('leave_requests.status', $request->status);

        return response()->json($query->orderByDesc('leave_requests.leave_id')->get());
    }

    public function store(Request $request)
    {
        $id = DB::table('leave_requests')->insertGetId([
        'emp_id'        => $request->emp_id,
        'leave_type_id' => $request->leave_type_id ?? 1,
        'start_date'    => $request->start_date,
        'end_date'      => $request->end_date,
        'reason'        => $request->reason ?? null,
        'status'        => 'Pending',
    ], 'leave_id');

        $record = DB::table('leave_requests')
            ->join('employee', 'leave_requests.emp_id', '=', 'employee.emp_id')
            ->join('leave_types', 'leave_requests.leave_type_id', '=', 'leave_types.leave_type_id')
            ->select('leave_requests.*', 'employee.emp_name', 'leave_types.type_name')
            ->where('leave_requests.leave_id', $id)
            ->first();

        return response()->json($record, 201);
    }

    public function updateStatus(Request $request, $id)
    {
        DB::table('leave_requests')->where('leave_id', $id)
            ->update(['status' => $request->status]);

        $record = DB::table('leave_requests')
            ->join('employee', 'leave_requests.emp_id', '=', 'employee.emp_id')
            ->join('leave_types', 'leave_requests.leave_type_id', '=', 'leave_types.leave_type_id')
            ->select('leave_requests.*', 'employee.emp_name', 'leave_types.type_name')
            ->where('leave_requests.leave_id', $id)
            ->first();

        return response()->json($record);
    }

    public function destroy($id)
    {
        DB::table('leave_requests')->where('leave_id', $id)->delete();
        return response()->json(['message' => 'លុបបានជោគជ័យ']);
    }

    // ===== Leave Types =====
    public function leaveTypes()
    {
        return response()->json(
            DB::table('leave_types')->orderBy('leave_type_id')->get()
        );
    }
}