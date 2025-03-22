<?php

namespace App\Http\Controllers;

use App\Models\Member;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class memberController extends Controller
{

    public function dashboard()
    {
        return view('member.home.member');
    }


    public function index()
    {
        $member = Member::all();
        return view('admin.member.member', compact('member'));
    }

    public function store(Request $request)
    {
        // dd($request->all());
        $validatedData = $request->validate([
            'nama' => 'required|string|max:255',
            'no_telp' => 'required|string|max:15|unique:member,no_telp',
            'email' => 'nullable|email|unique:member,email',
            'alamat' => 'nullable|string|max:255',
            'password' => 'required|string|min:6',
        ]);

        Member::create([
            'nama' => $request->nama,
            'no_telp' => $request->no_telp,
            'email' => $request->email,
            'alamat' => $request->alamat,
            'password' => $request->password, // Tidak perlu bcrypt lagi
            'loyalty_points' => 0,
            'tgl_bergabung' => now()->format('Y-m-d'),
        ]);

        return redirect()->route('member.index')->with('success', 'Member berhasil ditambahkan.');
    }


    public function update(Request $request, $id)
    {
        $member = Member::findOrFail($id);

        $request->validate([
            'nama' => 'required',
            'no_telp' => 'required|unique:member,no_telp,' . $id,
            'alamat' => 'nullable',
            'email' => 'nullable|email',
            'password' => 'nullable|min:6',
        ]);

        $updateData = [
            'nama' => $request->nama,
            'no_telp' => $request->no_telp,
            'alamat' => $request->alamat,
            'email' => $request->email,
        ];

        if ($request->filled('password')) {
            $updateData['password'] = bcrypt($request->password);
        }

        $member->update($updateData);

        return redirect()->route('member.index')->with('success', 'Member berhasil diperbarui.');
    }


    public function destroy($id)
    {
        $member = Member::findOrFail($id);
        $member->delete();

        return redirect()->route('member.index')->with('success', 'Member berhasil dihapus.');
    }
}
