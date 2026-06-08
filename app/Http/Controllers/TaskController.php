<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TaskController extends Controller
{
    public function index(Request $request)
    {
        $query = DB::table('tasks')
            ->join('employee', 'tasks.emp_id', '=', 'employee.emp_id')
            ->select('tasks.*', 'employee.emp_name');

        if ($request->emp_id) $query->where('tasks.emp_id', $request->emp_id);
        if ($request->status) $query->where('tasks.status',  $request->status);

        return response()->json($query->orderByDesc('tasks.task_id')->get());
    }

    public function store(Request $request)
    {
        $id = DB::table('tasks')->insertGetId([
            'emp_id'      => $request->emp_id,
            'task_name'   => $request->task_name,
            'description' => $request->description ?? null,
            'deadline'    => $request->deadline    ?? null,
            'status'      => $request->status      ?? 'To Do',
        ]);
        return response()->json(DB::table('tasks')->where('task_id', $id)->first(), 201);
    }

    public function update(Request $request, $id)
    {
        DB::table('tasks')->where('task_id', $id)->update([
            'task_name'   => $request->task_name,
            'description' => $request->description,
            'deadline'    => $request->deadline,
            'status'      => $request->status,
        ]);
        return response()->json(DB::table('tasks')->where('task_id', $id)->first());
    }

    public function destroy($id)
    {
        DB::table('tasks')->where('task_id', $id)->delete();
        return response()->json(['message' => 'លុបបានជោគជ័យ']);
    }
}