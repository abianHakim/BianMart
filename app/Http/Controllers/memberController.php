<?php

namespace App\Http\Controllers;

use App\Models\Member;
use Illuminate\Http\Request;

class memberController extends Controller
{
    public function index()
    {
        $member = Member::all();
        return view('admin.member.member', compact('member'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required',
            'no_telp' => 'required',
            'alamat' => 'nullable',
            'email' => 'nullable|email'
        ]);

        // dd($request->all());

        Member::create([
            'nama' => $request->nama,
            'no_telp' => $request->no_telp,
            'email' => $request->email,
            'alamat' => $request->alamat,
            'loyalty_points' => 0,
            'tgl_bergabung' => now()->format('Y-m-d'),
        ]);

        return redirect()->route('member.index')->with('success', 'Member berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        // dd($id);
        $member = Member::findOrFail($id);

        $request->validate([
            'nama' => 'required',
            'no_telp' => 'required|unique:member,no_telp,' . $id,
            'alamat' => 'nullable',
            'email' => 'nullable|email'
        ]);


        $member->update([
            'nama' => $request->nama,
            'no_telp' => $request->no_telp,
            'alamat' => $request->alamat,
            'email' => $request->email,
        ]);

        return redirect()->route('member.index')->with('success', 'Member berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $member = Member::findOrFail($id);
        $member->delete();

        return redirect()->route('member.index')->with('success', 'Member berhasil dihapus.');
    }
}
