<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class authController extends Controller
{
    /**
     * Menampilkan halaman form login.
     * 
     * @return \Illuminate\View\View Halaman login.
     */
    public function showLoginForm()
    {
        return view('login');
    }

    /**
     * Menangani proses login pengguna.
     * 
     * Fungsi ini memvalidasi input dari form login, menentukan apakah pengguna
     * login dengan email atau username, lalu mencoba otentikasi menggunakan data tersebut.
     * Jika berhasil, pengguna diarahkan ke dashboard. Jika gagal, akan kembali
     * ke halaman login dengan pesan kesalahan.
     * 
     * @param \Illuminate\Http\Request $request Objek request yang berisi data login.
     * @return \Illuminate\Http\RedirectResponse Redirect ke dashboard atau kembali dengan error.
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'login' => 'required',
            'password' => 'required'
        ]);

        $loginField = filter_var($request->login, FILTER_VALIDATE_EMAIL) ? 'email' : 'name';

        $credentials = [
            $loginField => $request->login,
            'password' => $request->password
        ];

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            return redirect()->route('dashboard');
        }

        return back()->withErrors([
            'login' => 'The provided credentials do not match our records.',
        ]);
    }

    /**
     * Menangani proses registrasi pengguna baru.
     * 
     * Fungsi ini memvalidasi data input registrasi, lalu menyimpan
     * pengguna baru ke dalam database dengan password yang dienkripsi.
     * Role default diset `null`.
     * 
     * @param \Illuminate\Http\Request $request Objek request yang berisi data registrasi.
     * @return \Illuminate\Http\RedirectResponse Redirect ke halaman login dengan pesan sukses.
     */
    public function register(Request $request)
    {
        // dd($request->all());

        $request->validate([
            'name' => 'required',
            'email' => 'required',
            'password' => 'required',
            'role' => 'required',
        ]);

        // Menyimpan data ke database
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => null,
        ]);

        return redirect('login')->with('success', 'Account created successfully. Welcome!');
    }

    /**
     * Menangani proses logout pengguna.
     * 
     * Fungsi ini menghapus session login pengguna saat ini, meregenerasi token CSRF,
     * dan mengarahkan kembali ke halaman login.
     * 
     * @param \Illuminate\Http\Request $request Objek request.
     * @return \Illuminate\Http\RedirectResponse Redirect ke halaman login setelah logout.
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login')->with('success', 'Logout berhasil!');
    }
}
