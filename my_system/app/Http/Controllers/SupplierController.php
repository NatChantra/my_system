<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SupplierController extends Controller
{
    public function index()
    {
        return response()->json(DB::table('suppliers')->orderByDesc('sup_id')->get());
    }

    public function store(Request $request)
    {
        $request->validate([
            'sup_name' => 'required|string|max:255',
        ]);

        $id = DB::table('suppliers')->insertGetId([
            'sup_name'    => $request->sup_name,
            'sup_phone'   => $request->sup_phone,
            'sup_email'   => $request->sup_email,
            'sup_address' => $request->sup_address,
            'created_at'  => now(),
            'updated_at'  => now(),
        ]);

        return response()->json(DB::table('suppliers')->find($id), 201);
    }

    public function update(Request $request, $id)
    {
        DB::table('suppliers')->where('sup_id', $id)->update([
            'sup_name'    => $request->sup_name,
            'sup_phone'   => $request->sup_phone,
            'sup_email'   => $request->sup_email,
            'sup_address' => $request->sup_address,
            'updated_at'  => now(),
        ]);

        return response()->json(DB::table('suppliers')->find($id));
    }

    public function destroy($id)
    {
        DB::table('suppliers')->where('sup_id', $id)->delete();
        return response()->json(['message' => 'លុបបានជោគជ័យ']);
    }
}