<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function index()
    {
        return view('employee.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        $employee = Employee::where('email', $request->email)->first();

        if (!$employee || !Hash::check($request->password, $employee->password)) {
            return back()->withErrors([
                'email' => 'Email atau password salah.',
            ])->withInput();
        }

        $request->session()->put('employee', [
            'id'       => $employee->id,
            'name'     => $employee->name,
            'email'    => $employee->email,
            'position' => $employee->position,
        ]);

        $request->session()->save(); // ← ini yang penting

        return redirect('/karyawan/dashboard');
    }

    public function logout(Request $request)
    {
        $request->session()->forget('employee');
        $request->session()->save();
        return redirect('/karyawan');
    }
}