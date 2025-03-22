<?php

namespace App\Http\Controllers;

use App\Models\Member;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MemberAuthController extends Controller
{
    public function showLoginForm()
    {
        return view('loginMember');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required',
            'no_telp' => 'required|unique:members,no_telp',
            'alamat' => 'nullable',
            'email' => 'nullable|email'
        ]);

        Member::create([
            'nama' => $request->nama,
            'no_telp' => $request->no_telp,
            'email' => $request->email,
            'alamat' => $request->alamat,
            'password' => bcrypt($request->no_telp),
            'role' => 'member',
            'loyalty_points' => 0,
            'tgl_bergabung' => now()->format('Y-m-d'),
        ]);

        return redirect()->route('member.index')->with('success', 'Member berhasil ditambahkan.');
    }

    public function login(Request $request)
    {
        $request->validate([
            'no_telp' => 'required',
            'password' => 'required',
        ]);

        $credentials = $request->only('no_telp', 'password');

        if (Auth::guard('member')->attempt($credentials)) {
            return redirect()->route('dashboard');
        }

        return back()->withErrors(['no_telp' => 'Nomor HP atau password salah.']);
    }


    public function logout()
    {
        Auth::guard('member')->logout();
        return redirect()->route('member.login')->with('success', 'Anda telah logout.');
    }
}
