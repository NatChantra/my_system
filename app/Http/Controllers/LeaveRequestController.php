<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LeaveRequestController extends Controller
{
    public function index(Request $request)
    {
        $query = DB::table('leave_requests')
            ->join('employee', 'leave_requests.emp_id', '=', 'employee.emp_id')
            ->select('leave_requests.*', 'employee.emp_name');

        if ($request->emp_id) $query->where('leave_requests.emp_id', $request->emp_id);
        if ($request->status) $query->where('leave_requests.status', $request->status);

        return response()->json($query->orderByDesc('leave_requests.leave_id')->get());
    }

    public function store(Request $request)
    {
        $id = DB::table('leave_requests')->insertGetId([
            'emp_id'     => $request->emp_id,
            'leave_type' => $request->leave_type,
            'start_date' => $request->start_date,
            'end_date'   => $request->end_date,
            'reason'     => $request->reason ?? null,
            'status'     => 'Pending',
        ]);
        return response()->json(DB::table('leave_requests')->where('leave_id', $id)->first(), 201);
    }

    public function updateStatus(Request $request, $id)
    {
        DB::table('leave_requests')->where('leave_id', $id)
            ->update(['status' => $request->status]);
        return response()->json(DB::table('leave_requests')->where('leave_id', $id)->first());
    }

    public function destroy($id)
    {
        DB::table('leave_requests')->where('leave_id', $id)->delete();
        return response()->json(['message' => 'លុបបានជោគជ័យ']);
    }
}