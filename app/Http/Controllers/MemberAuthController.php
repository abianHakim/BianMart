<?php

namespace App\Http\Controllers;

use App\Models\Member;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MemberAuthController extends Controller
{
    /**
     * Menampilkan formulir login member.
     *
     * Fungsi ini digunakan untuk menampilkan halaman login bagi member. Biasanya digunakan
     * untuk member yang ingin masuk ke dalam sistem.
     *
     * @return \Illuminate\View\View Tampilan halaman login member
     */
    public function showLoginForm()
    {
        return view('loginMember');
    }

    /**
     * Menyimpan data member baru ke dalam sistem.
     *
     * Fungsi ini digunakan untuk mendaftarkan member baru ke dalam sistem setelah melakukan validasi
     * terhadap data yang diterima dari permintaan. Member yang berhasil disimpan akan diarahkan ke
     * daftar member dengan pesan sukses.
     *
     * @param \Illuminate\Http\Request $request Data yang diterima dari form input
     * @return \Illuminate\Http\RedirectResponse Redirect ke halaman daftar member dengan pesan sukses
     */
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

    /**
     * Proses login member.
     *
     * Fungsi ini digunakan untuk memverifikasi kredensial member (nomor telepon dan password)
     * dan melakukan login menggunakan guard 'member'. Jika login berhasil, member akan
     * diarahkan ke halaman dashboard. Jika gagal, pesan kesalahan akan ditampilkan.
     *
     * @param \Illuminate\Http\Request $request Data yang diterima untuk proses login
     * @return \Illuminate\Http\RedirectResponse Redirect ke dashboard jika berhasil login, atau kembali ke halaman login dengan pesan kesalahan
     */
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

    /**
     * Logout member.
     *
     * Fungsi ini digunakan untuk melakukan logout member yang sedang aktif. Setelah logout,
     * member akan diarahkan kembali ke halaman login dengan pesan sukses.
     *
     * @return \Illuminate\Http\RedirectResponse Redirect ke halaman login dengan pesan sukses
     */
    public function logout()
    {
        Auth::guard('member')->logout();
        return redirect()->route('member.login')->with('success', 'Anda telah logout.');
    }
}
