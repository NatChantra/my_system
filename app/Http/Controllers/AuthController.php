<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        $user = DB::table('users')
            ->where('username', $request->username)
            ->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'ឈ្មោះអ្នកប្រើ ឬ លេខសម្ងាត់មិនត្រឹមត្រូវ'
            ], 401);
        }

        // Get employee info
        $emp = null;
        if ($user->emp_id) {
            $emp = DB::table('employee')->where('emp_id', $user->emp_id)->first();
        }

        return response()->json([
            'user_id'  => $user->id,  // ✅ ត្រឹមត្រូវ
            'username' => $user->username,
            'role'     => $user->role,
            'emp_id'   => $user->emp_id,
            'emp_name' => $emp?->emp_name ?? $user->name,
            'position' => $emp?->position ?? null,
            'phone'    => $emp?->phone ?? null,
        ]);
    }

    public function register(Request $request)
    {
        $request->validate([
            'username' => 'required|string|unique:users,username',
            'password' => 'required|string|min:4',
            'name'     => 'required|string',
            'role'     => 'required|in:Admin,Staff',
            'emp_id'   => 'nullable|exists:employee,emp_id',
        ]);

        $id = DB::table('users')->insertGetId([
            'name'       => $request->name,
            'username'   => $request->username,
            'email'      => $request->username . '@venol.com',
            'password'   => Hash::make($request->password),
            'role'       => $request->role,
            'emp_id'     => $request->emp_id ?? null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return response()->json(['message' => 'បង្កើតគណនីជោគជ័យ', 'user_id' => $id], 201);
    }
}