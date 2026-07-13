<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AttendanceController extends Controller
{
    public function index(Request $request)
    {
        $query = DB::table('attendance')
            ->join('employee', 'attendance.emp_id', '=', 'employee.emp_id')
            ->select('attendance.*', 'employee.emp_name');

        if ($request->emp_id) $query->where('attendance.emp_id', $request->emp_id);
        if ($request->date)   $query->where('attendance.date',   $request->date);

        return response()->json($query->orderByDesc('attendance.date')->get());
    }

    public function scan(Request $request)
    {
        $now    = now('Asia/Phnom_Penh');
        $today  = $now->toDateString();
        $time   = $now->toTimeString();
        $h      = (int)$now->format('H');
        $m      = (int)$now->format('i');

        $checkin_status  = ($h < 8 || ($h === 8 && $m <= 15)) ? 'On Time' : 'Late';
        $checkout_status = ($h >= 17) ? 'On Time' : 'Early Leave';

        $existing = DB::table('attendance')
            ->where('emp_id', $request->emp_id)
            ->where('date', $today)
            ->first();

        if ($existing) {
            // Check-out
            if (!$existing->time_out) {
                DB::table('attendance')
                    ->where('att_id', $existing->att_id)
                    ->update([
                        'time_out'        => $time,
                        'checkout_status' => $checkout_status,
                    ]);

                $record = DB::table('attendance')
                    ->join('employee', 'attendance.emp_id', '=', 'employee.emp_id')
                    ->select('attendance.*', 'employee.emp_name')
                    ->where('attendance.att_id', $existing->att_id)
                    ->first();

                return response()->json([
                    'type'    => 'checkout',
                    'message' => '🏃 កត់ចេញជោគជ័យ!',
                    'record'  => $record,
                ], 200);
            }

            // ✅ បានកត់ទាំង checkin និង checkout រួចហើយ
            return response()->json([
                'status'  => 'already_done',
                'message' => '✅ បានកត់ចូល និងចេញរួចហើយថ្ងៃនេះ!'
            ], 422);
        }

        // Check-in
        $id = DB::table('attendance')->insertGetId([
            'emp_id'       => $request->emp_id,
            'date'         => $today,
            'time_in'      => $time,
            'time_out'     => null,
            'gps_location' => $request->gps_location,
            'device_id'    => $request->device_id,
            'scan_token'   => $request->scan_token,
            'status'       => $checkin_status,
        ]);

        $record = DB::table('attendance')
            ->join('employee', 'attendance.emp_id', '=', 'employee.emp_id')
            ->select('attendance.*', 'employee.emp_name')
            ->where('attendance.att_id', $id)
            ->first();

        return response()->json([
            'type'    => 'checkin',
            'message' => '✅ កត់ចូលជោគជ័យ!',
            'record'  => $record,
        ], 201);
    }
}