<?php

namespace App\Http\Controllers;

use App\Models\Member;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class memberController extends Controller
{

    /**
     * Menampilkan halaman dashboard member.
     *
     * Fungsi ini digunakan untuk menampilkan tampilan halaman dashboard bagi member.
     * Biasanya digunakan untuk menampilkan informasi atau navigasi untuk member.
     *
     * @return \Illuminate\View\View Tampilan halaman dashboard member
     */
    public function dashboard()
    {
        return view('member.home.member');
    }

    /**
     * Menampilkan daftar semua member.
     *
     * Fungsi ini digunakan untuk menampilkan daftar member yang terdaftar dalam sistem.
     * Data member akan diambil dari tabel 'member' dan ditampilkan dalam tampilan
     * yang telah disediakan.
     *
     * @return \Illuminate\View\View Tampilan daftar member
     */
    public function index()
    {
        // Mengambil semua member dengan jumlah total loyalty points yang dimiliki
        $members = Member::with('loyaltyPoints')->get();

        // Menambahkan total loyalty points per member ke setiap member
        foreach ($members as $member) {
            $member->total_loyalty_points = $member->loyaltyPoints->sum('point_didapat');
        }

        return view('admin.member.member', compact('members'));
    }


    /**
     * Menyimpan data member baru ke dalam sistem.
     *
     * Fungsi ini digunakan untuk menyimpan data member baru ke dalam database setelah
     * validasi data yang diterima dari permintaan. Member yang berhasil disimpan akan
     * diberikan pesan sukses dan diarahkan ke daftar member.
     *
     * @param \Illuminate\Http\Request $request Data yang diterima dari form input
     * @return \Illuminate\Http\RedirectResponse Redirect ke halaman daftar member dengan pesan sukses
     */
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

    /**
     * Memperbarui data member yang sudah ada.
     *
     * Fungsi ini digunakan untuk memperbarui data member yang sudah ada berdasarkan ID
     * member yang diterima. Data yang diterima akan divalidasi, dan member yang berhasil
     * diperbarui akan diarahkan kembali ke halaman daftar member dengan pesan sukses.
     *
     * @param \Illuminate\Http\Request $request Data yang diterima untuk pembaruan
     * @param int $id ID member yang akan diperbarui
     * @return \Illuminate\Http\RedirectResponse Redirect ke halaman daftar member dengan pesan sukses
     */
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

    /**
     * Menghapus data member dari sistem.
     *
     * Fungsi ini digunakan untuk menghapus data member berdasarkan ID yang diberikan.
     * Setelah member dihapus, sistem akan mengarahkan ke halaman daftar member dengan
     * pesan sukses.
     *
     * @param int $id ID member yang akan dihapus
     * @return \Illuminate\Http\RedirectResponse Redirect ke halaman daftar member dengan pesan sukses
     */
    public function destroy($id)
    {
        $member = Member::findOrFail($id);
        $member->delete();

        return redirect()->route('member.index')->with('success', 'Member berhasil dihapus.');
    }
}
