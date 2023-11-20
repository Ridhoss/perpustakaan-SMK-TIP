<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\anggota;
use App\Models\asal;
use App\Models\bahasa;
use App\Models\buku;
use App\Models\detailpinjam;
use App\Models\kategori;
use App\Models\penerbit;
use App\Models\pengarang;
use App\Models\pengembalian;
use App\Models\petugas;
use App\Models\pinjam;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class AdminController extends Controller
{
    // Register
    public function RegisterAdmin(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required|unique:petugas|regex:/^[a-zA-z\s]*$/',
            'name' => 'required|regex:/^[a-zA-z\s]*$/',
            'password' => 'required|min:5',
            'email' => 'required|unique:petugas',
        ]);

        if ($validator->fails()) {
            return redirect('/register')
                ->withErrors($validator);
        }

        // hashing password
        $request['password'] = Hash::make($request['password']);

        petugas::create([
            'username' => $request->username,
            'name' => $request->name,
            'password' => $request['password'],
            'email' => $request->email,
            'photo' => 'profile.png'
        ]);

        return redirect('/login')->with('register', 'Berhasil Menambahkan');
    }

    // login admin
    public function LoginAdmin(Request $request)
    {
        $login = $request->validate([
            'username' => 'required',
            'password' => 'required'
        ]);

        if (Auth::guard('admin')->attempt($login)) {
            $request->session()->regenerate();
            $user = Auth::guard('admin')->user();

            if ($user) {
                // Authentication successful, log the entry
                DB::statement('CALL logEntry(?,?,?,?)', [
                    $user->id,
                    $user->username,
                    'Login Admin',
                    Carbon::now()
                ]);
                return redirect()->intended('/dashboard');
            }
        } else if (Auth::guard('petugas')->attempt($login)) {
            $request->session()->regenerate();
            $user = Auth::guard('petugas')->user();

            if ($user) {
                // Authentication successful, log the entry
                DB::statement('CALL logEntry(?,?,?,?)', [
                    $user->id,
                    $user->username,
                    'Login Petugas',
                    Carbon::now()
                ]);
                return redirect()->intended('/petdashboard');
            }
        }

        return back()->with('gallog', 'Login gagal');
    }


    // logout
    public function Logout(Request $request)
    {
        if (Auth::guard('admin')->check()) {
            $user = Auth::guard('admin')->user();

            DB::statement('CALL logEntry(?,?,?,?)', [
                $user->id,
                $user->username,
                'Logout Admin',
                Carbon::now()
            ]);

            Auth::guard('admin')->logout();
        } elseif (Auth::guard('petugas')->check()) {
            $user = Auth::guard('petugas')->user();

            DB::statement('CALL logEntry(?,?,?,?)', [
                $user->id,
                $user->username,
                'Logout Petugas',
                Carbon::now()
            ]);

            Auth::guard('petugas')->logout();
        }

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect()->intended('/');
    }







    // route & search

    // ke landing page
    public function landing()
    {

        $databuku = buku::select(
            'bukus.isbn',
            'bukus.pengarang',
            'bukus.judul',
            'bukus.thn_inv',
            DB::raw('COUNT(bukus.eks) AS jumlah'),
            'bukus.asl_id',
            'bukus.ktg_id',
            'bukus.bhs_id',
            'asals.name AS asal',
            'kategoris.name AS kategori',
            'bahasas.name AS bahasa',
            'bukus.tahun_terbit',
            'bukus.sinopsis',
            'bukus.photo',
            'bukus.ket'
        )
            ->join('asals', 'bukus.asl_id', '=', 'asals.id')
            ->join('kategoris', 'bukus.ktg_id', '=', 'kategoris.id')
            ->join('bahasas', 'bukus.bhs_id', '=', 'bahasas.id')
            ->groupBy('bukus.tanggal', 'bukus.isbn', 'bukus.pengarang', 'bukus.judul', 'bukus.thn_inv', 'bukus.asl_id', 'bukus.ktg_id', 'bukus.bhs_id', 'asal', 'kategori', 'bahasa', 'bukus.tahun_terbit', 'bukus.sinopsis', 'bukus.photo', 'bukus.ket');

        return view('landing', [
            'bukus' => $databuku->get()
        ]);
    }

    // ke halaman dashboard
    public function dashboard()
    {
        return view('admin.page.dashboard', [
            'user' => Auth::user(),
            'jumlahbuku' => buku::count(),
            'jumlahpeminjaman' => pinjam::count(),
            'jumlahanggota' => anggota::count(),
            'jumlahadmin' => Admin::count()
        ]);
    }

    // ke halaman data kategori
    public function datakat(Request $request)
    {

        if ($request->has('cari')) {
            $datakat = kategori::where('name', 'LIKE', '%' . $request->cari . '%');
        } else {
            $datakat = kategori::select('*');
        }

        return view('admin.page.datakategori', [
            'datakat' => $datakat->get(),
            'lastquery' => $request->cari,
            'user' => Auth::user()
        ]);
    }

    // ke halaman data asal
    public function dataasal(Request $request)
    {
        if ($request->has('cari')) {
            $dataasal = asal::where('name', 'LIKE', '%' . $request->cari . '%');
        } else {
            $dataasal = asal::select('*');
        }

        return view('admin.page.dataasal', [
            'dataasal' => $dataasal->get(),
            'lastquery' => $request->cari,
            'user' => Auth::user()
        ]);
    }

    // ke halaman data bahasa
    public function databahasa(Request $request)
    {
        if ($request->has('cari')) {
            $databahasa = bahasa::where('name', 'LIKE', '%' . $request->cari . '%');
        } else {
            $databahasa = bahasa::select('*');
        }

        return view('admin.page.databahasa', [
            'databahasa' => $databahasa->get(),
            'lastquery' => $request->cari,
            'user' => Auth::user()
        ]);
    }

    // ke halaman data anggota
    public function dataanggota(Request $request)
    {
        if ($request->has('cari')) {
            $dataanggota = anggota::where('name', 'LIKE', '%' . $request->cari . '%')
                ->OrWhere('nisn', 'LIKE', '%' . $request->cari . '%');
        } else {
            $dataanggota = anggota::select('*');
        }

        return view('admin.page.dataanggota', [
            'dataanggota' => $dataanggota->get(),
            'lastquery' => $request->cari,
            'user' => Auth::user()
        ]);
    }

    // ke halaman data admin
    public function dataadmin(Request $request)
    {
        if ($request->has('cari')) {
            $dataadmin = Admin::where('name', 'LIKE', '%' . $request->cari . '%')
                ->OrWhere('username', 'LIKE', '%' . $request->cari . '%');
        } else {
            $dataadmin = Admin::select('*');
        }

        return view('admin.page.dataadmin', [
            'dataadmin' => $dataadmin->get(),
            'lastquery' => $request->cari,
            'user' => Auth::user()
        ]);
    }

    // ke halaman data petugas
    public function datapetugas(Request $request)
    {
        if ($request->has('cari')) {
            $datapetugas = petugas::where('name', 'LIKE', '%' . $request->cari . '%')
                ->OrWhere('username', 'LIKE', '%' . $request->cari . '%');
        } else {
            $datapetugas = petugas::select('*');
        }

        return view('admin.page.datapetugas', [
            'datapetugas' => $datapetugas->get(),
            'lastquery' => $request->cari,
            'user' => Auth::user()
        ]);
    }

    // ke halaman databuku
    public function databuku(Request $request)
    {

        if ($request->has('cari')) {
            $databuku = buku::select('bukus.id', DB::raw("DATE_FORMAT(bukus.tanggal, '%d %M %Y') AS tanggal"), 'bukus.isbn', 'bukus.pengarang', 'bukus.judul', 'bukus.eks', 'bukus.thn_inv', 'bukus.asl_id', 'bukus.ktg_id', 'bukus.bhs_id', 'asals.name AS asal', 'kategoris.name AS kategori', 'bahasas.name AS bahasa', 'bukus.no_inv', 'bukus.tahun_terbit', 'bukus.sinopsis', 'bukus.photo', 'bukus.ket', 'bukus.status')
                ->join('asals', 'asals.id', '=', 'bukus.asl_id')
                ->join('kategoris', 'kategoris.id', '=', 'bukus.ktg_id')
                ->join('bahasas', 'bahasas.id', '=', 'bukus.bhs_id')
                ->where('judul', 'LIKE', '%' . $request->cari . '%')
                ->OrWhere('isbn', 'LIKE', '%' . $request->cari . '%')
                ->OrWhere('pengarang', 'LIKE', '%' . $request->cari . '%')
                ->OrWhere('no_inv', 'LIKE', '%' . $request->cari . '%');
        } else {
            $databuku = buku::select('bukus.id', DB::raw("DATE_FORMAT(bukus.tanggal, '%d %M %Y') AS tanggal"), 'bukus.isbn', 'bukus.pengarang', 'bukus.judul', 'bukus.eks', 'bukus.thn_inv', 'asals.name AS asal', 'kategoris.name AS kategori', 'bahasas.name AS bahasa', 'bukus.no_inv', 'bukus.asl_id', 'bukus.ktg_id', 'bukus.bhs_id', 'bukus.tahun_terbit', 'bukus.sinopsis', 'bukus.photo', 'bukus.ket', 'bukus.status')
                ->join('asals', 'asals.id', '=', 'bukus.asl_id')
                ->join('kategoris', 'kategoris.id', '=', 'bukus.ktg_id')
                ->join('bahasas', 'bahasas.id', '=', 'bukus.bhs_id');
        }

        $datakat = kategori::all();
        $dataasal = asal::all();
        $databahasa = bahasa::all();

        return view('admin.page.databuku', [
            'databuku' => $databuku->get(),
            'lastquery' => $request->cari,
            'dataasal' => $dataasal,
            'databahasa' => $databahasa,
            'datakat' => $datakat,
            'user' => Auth::user()
        ]);
    }

    // ke halaman data group buku
    public function databukus(Request $request)
    {

        if ($request->has('cari')) {
            $databuku = buku::select(
                'bukus.isbn',
                'bukus.pengarang',
                'bukus.judul',
                'bukus.thn_inv',
                DB::raw('COUNT(bukus.eks) AS jumlah'),
                'bukus.asl_id',
                'bukus.ktg_id',
                'bukus.bhs_id',
                'asals.name AS asal',
                'kategoris.name AS kategori',
                'bahasas.name AS bahasa',
                'bukus.tahun_terbit',
                'bukus.sinopsis',
                'bukus.photo',
                'bukus.ket'
            )
                ->join('asals', 'bukus.asl_id', '=', 'asals.id')
                ->join('kategoris', 'bukus.ktg_id', '=', 'kategoris.id')
                ->join('bahasas', 'bukus.bhs_id', '=', 'bahasas.id')
                ->groupBy('bukus.tanggal', 'bukus.isbn', 'bukus.pengarang', 'bukus.judul', 'bukus.thn_inv', 'bukus.asl_id', 'bukus.ktg_id', 'bukus.bhs_id', 'asal', 'kategori', 'bahasa', 'bukus.tahun_terbit', 'bukus.sinopsis', 'bukus.photo', 'bukus.ket')
                ->where('isbn', 'LIKE', '%' . $request->cari . '%')
                ->OrWhere('judul', 'LIKE', '%' . $request->cari . '%')
                ->OrWhere('pengarang', 'LIKE', '%' . $request->cari . '%');
        } else {
            $databuku = buku::select(
                'bukus.isbn',
                'bukus.pengarang',
                'bukus.judul',
                'bukus.thn_inv',
                DB::raw('COUNT(bukus.eks) AS jumlah'),
                'bukus.asl_id',
                'bukus.ktg_id',
                'bukus.bhs_id',
                'asals.name AS asal',
                'kategoris.name AS kategori',
                'bahasas.name AS bahasa',
                'bukus.tahun_terbit',
                'bukus.sinopsis',
                'bukus.photo',
                'bukus.ket'
            )
                ->join('asals', 'bukus.asl_id', '=', 'asals.id')
                ->join('kategoris', 'bukus.ktg_id', '=', 'kategoris.id')
                ->join('bahasas', 'bukus.bhs_id', '=', 'bahasas.id')
                ->groupBy('bukus.tanggal', 'bukus.isbn', 'bukus.pengarang', 'bukus.judul', 'bukus.thn_inv', 'bukus.asl_id', 'bukus.ktg_id', 'bukus.bhs_id', 'asal', 'kategori', 'bahasa', 'bukus.tahun_terbit', 'bukus.sinopsis', 'bukus.photo', 'bukus.ket');
        }

        $datakat = kategori::all();
        $dataasal = asal::all();
        $databahasa = bahasa::all();

        return view('admin.page.databukugroup', [
            'databuku' => $databuku->get(),
            'lastquery' => $request->cari,
            'dataasal' => $dataasal,
            'databahasa' => $databahasa,
            'datakat' => $datakat,
            'user' => Auth::user()
        ]);
    }

    // ke halaman datapeminjaman
    public function datapeminjaman(Request $request)
    {

        if ($request->has('cari')) {
            $datapeminjaman = pinjam::select('pinjams.id', 'pinjams.kode', 'bukus.isbn', 'bukus.judul', 'anggotas.name AS anggota', 'anggotas.id AS agtid', 'anggotas.nisn', 'detailpinjams.tgl_pinjam', 'detailpinjams.tgl_kembali', 'petugas.id AS petid', 'petugas.name AS petugas', 'detailpinjams.qty AS qty', 'pinjams.status')
                ->join('bukus', 'bukus.isbn', '=', 'pinjams.id_buku')
                ->join('anggotas', 'anggotas.id', '=', 'pinjams.id_anggota')
                ->join('detailpinjams', 'detailpinjams.kode', '=', 'pinjams.kode')
                ->join('petugas', 'petugas.id', '=', 'detailpinjams.id_petugas')
                ->groupBy('id', 'pinjams.kode', 'judul', 'isbn', 'id_petugas', 'anggota', 'agtid', 'nisn', 'tgl_pinjam', 'petid', 'tgl_kembali', 'petugas', 'qty', 'pinjams.status')
                ->where('pinjams.kode', 'LIKE', '%' . $request->cari . '%')
                ->OrWhere('anggotas.name', 'LIKE', '%' . $request->cari . '%')
                ->OrWhere('anggotas.nisn', 'LIKE', '%' . $request->cari . '%')
                ->OrWhere('bukus.judul', 'LIKE', '%' . $request->cari . '%');
        } else {
            $datapeminjaman = pinjam::select('pinjams.id', 'pinjams.kode', 'bukus.isbn', 'bukus.judul', 'anggotas.name AS anggota', 'anggotas.id AS agtid', 'detailpinjams.tgl_pinjam', 'detailpinjams.tgl_kembali', 'petugas.id AS petid', 'petugas.name AS petugas', 'detailpinjams.qty AS qty', 'pinjams.status')
                ->join('bukus', 'bukus.isbn', '=', 'pinjams.id_buku')
                ->join('anggotas', 'anggotas.id', '=', 'pinjams.id_anggota')
                ->join('detailpinjams', 'detailpinjams.kode', '=', 'pinjams.kode')
                ->join('petugas', 'petugas.id', '=', 'detailpinjams.id_petugas')
                ->groupBy('id', 'pinjams.kode', 'judul', 'isbn', 'anggota', 'agtid', 'tgl_pinjam', 'tgl_kembali', 'petid', 'petugas', 'qty', 'pinjams.status');
        }


        return view('admin.page.datapeminjaman', [
            'lastquery' => $request->cari,
            'datapeminjaman' => $datapeminjaman->get(),
            'user' => Auth::user()
        ]);
    }

    // ke halaman datapengembalian
    public function datapengembalian(Request $request)
    {

        if ($request->has('cari')) {
            $datapengembalian = pengembalian::select('pengembalians.id', 'bukus.isbn AS isbn', 'pengembalians.kode', 'pengembalians.tgl_kembali', 'pengembalians.denda', 'pengembalians.qty', 'pengembalians.keterangan', 'petugas.name AS petugas', 'detailpinjams.tgl_kembali AS kembaliwajib')
                ->join('petugas', 'petugas.id', '=', 'pengembalians.id_petugas')
                ->join('pinjams', 'pengembalians.kode', '=', 'pinjams.kode')
                ->join('bukus', 'bukus.isbn', '=', 'pinjams.id_buku')
                ->join('detailpinjams', 'detailpinjams.kode', '=', 'pinjams.kode')
                ->groupBy('pengembalians.id', 'isbn', 'pengembalians.kode', 'pengembalians.tgl_kembali', 'pengembalians.denda', 'pengembalians.qty', 'pengembalians.keterangan', 'petugas', 'kembaliwajib')
                ->where('pengembalians.kode', 'LIKE', '%' . $request->cari . '%');
        } else {
            $datapengembalian = pengembalian::select('pengembalians.id', 'bukus.isbn AS isbn', 'pengembalians.kode', 'pengembalians.tgl_kembali', 'pengembalians.denda', 'pengembalians.qty', 'pengembalians.keterangan', 'petugas.name AS petugas', 'detailpinjams.tgl_kembali AS kembaliwajib')
                ->join('petugas', 'petugas.id', '=', 'pengembalians.id_petugas')
                ->join('pinjams', 'pengembalians.kode', '=', 'pinjams.kode')
                ->join('bukus', 'bukus.isbn', '=', 'pinjams.id_buku')
                ->join('detailpinjams', 'detailpinjams.kode', '=', 'pinjams.kode')
                ->groupBy('pengembalians.id', 'isbn', 'pengembalians.kode', 'pengembalians.tgl_kembali', 'pengembalians.denda', 'pengembalians.qty', 'pengembalians.keterangan', 'petugas', 'kembaliwajib');
        }


        return view('admin.page.datapengembalian', [
            'lastquery' => $request->cari,
            'datapengembalian' => $datapengembalian->get(),
            'user' => Auth::user()
        ]);
    }







    // crud

    // kategori

    // tambah kategori
    public function addkategori(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|unique:kategoris|regex:/^[a-zA-z\s]*$/',
        ]);

        if ($validator->fails()) {
            return redirect('/kategori')
                ->withErrors($validator);
        }

        kategori::create([
            'name' => $request->name
        ]);

        return redirect('/kategori')->with('notifadd', 'Berhasil Menambahkan');
    }
    // update kategori
    public function upkategori(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|regex:/^[a-zA-z\s]*$/',
        ]);

        if ($validator->fails()) {
            return redirect('/kategori')
                ->withErrors($validator);
        }

        $data = kategori::find($request->id);

        $data->update([
            'name' => $request->name
        ]);

        return redirect('/kategori')->with('notifupdate', 'Berhasil Menambahkan');
    }
    // hapus kategori
    public function delkategori(Request $request)
    {
        $data = kategori::find($request->id);

        $data->delete();

        return redirect('/kategori')->with('notifhapus', 'Berhasil Menambahkan');
    }


    // asal

    // tambah asal
    public function addasal(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|unique:asals|regex:/^[a-zA-Z0-9\-]+$/',
        ]);

        if ($validator->fails()) {
            return redirect('/asal')
                ->withErrors($validator);
        }

        asal::create([
            'name' => $request->name,
        ]);

        return redirect('/asal')->with('notifadd', 'Berhasil Menambahkan');
    }
    // update asal
    public function upasal(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|regex:/^[a-zA-Z0-9\-]+$/',
        ]);

        if ($validator->fails()) {
            return redirect('/asal')
                ->withErrors($validator);
        }

        $data = asal::find($request->id);

        $data->update([
            'name' => $request->name,
        ]);

        return redirect('/asal')->with('notifupdate', 'Berhasil Menambahkan');
    }
    // hapus asal
    public function delasal(Request $request)
    {
        $data = asal::find($request->id);

        $data->delete();

        return redirect('/asal')->with('notifhapus', 'Berhasil Menambahkan');
    }


    // bahasa

    // tambah bahasa
    public function addbahasa(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|unique:bahasas|regex:/^[a-zA-z\s]*$/',
        ]);

        if ($validator->fails()) {
            return redirect('/bahasa')
                ->withErrors($validator);
        }

        bahasa::create([
            'name' => $request->name
        ]);

        return redirect('/bahasa')->with('notifadd', 'Berhasil Menambahkan');
    }
    // update bahasa
    public function upbahasa(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|regex:/^[a-zA-z\s]*$/',
        ]);

        if ($validator->fails()) {
            return redirect('/bahasa')
                ->withErrors($validator);
        }

        $data = bahasa::find($request->id);

        $data->update([
            'name' => $request->name
        ]);

        return redirect('/bahasa')->with('notifupdate', 'Berhasil Menambahkan');
    }
    // hapus bahasa
    public function delbahasa(Request $request)
    {
        $data = bahasa::find($request->id);

        $data->delete();

        return redirect('/bahasa')->with('notifhapus', 'Berhasil Menambahkan');
    }


    // anggota

    // tambah anggota
    public function addanggota(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nisn' => 'required|unique:anggotas|numeric',
            'name' => 'required|unique:anggotas|regex:/^[a-zA-z\s]*$/',
            'gender' => 'required',
            'date' => 'required',
            'phone' => 'required|regex:/^[\d\-\s\(\)+]+$/',
            'address' => 'required'
        ]);

        if ($validator->fails()) {
            if ($request->role == "admin") {
                return redirect('/anggota')
                    ->withErrors($validator);
            } else {
                return redirect('/petdataanggota')
                    ->withErrors($validator);
            }
        }

        anggota::create([
            'nisn' => $request->nisn,
            'name' => $request->name,
            'gender' => $request->gender,
            'date' => $request->date,
            'phone' => $request->phone,
            'address' => $request->address
        ]);

        if ($request->role == "admin") {
            return redirect('/anggota')->with('notifadd', 'Berhasil Menambahkan');
        } else {
            return redirect('/petdataanggota')->with('notifadd', 'Berhasil Menambahkan');
        }
    }
    // update anggota
    public function upanggota(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nisn' => 'required|numeric',
            'name' => 'required|regex:/^[a-zA-z\s]*$/',
            'gender' => 'required',
            'date' => 'required',
            'phone' => 'required|regex:/^[\d\-\s\(\)+]+$/',
            'address' => 'required'
        ]);

        if ($validator->fails()) {
            return redirect('/anggota')
                ->withErrors($validator);
        }

        $data = anggota::find($request->id);

        $data->update([
            'nisn' => $request->nisn,
            'name' => $request->name,
            'gender' => $request->gender,
            'date' => $request->date,
            'phone' => $request->phone,
            'address' => $request->address
        ]);

        if ($request->role == "admin") {
            return redirect('/anggota')->with('notifupdate', 'Berhasil Menambahkan');
        } else {
            return redirect('/petdataanggota')->with('notifupdate', 'Berhasil Menambahkan');
        }
    }
    // hapus anggota
    public function delanggota(Request $request)
    {
        $data = anggota::find($request->id);

        $data->delete();

        if ($request->role == "admin") {
            return redirect('/anggota')->with('notifhapus', 'Berhasil Menambahkan');
        } else {
            return redirect('/petdataanggota')->with('notifhapus', 'Berhasil Menambahkan');
        }
    }

    // admin

    // tambah admin
    public function addadmin(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required|unique:admins|regex:/^[a-zA-z\s]*$/',
            'name' => 'required|regex:/^[a-zA-z\s]*$/',
            'password' => 'required|min:3',
            'email' => 'required|unique:admins',
            'phone' => 'required|regex:/^[\d\-\s\(\)+]+$/',
            'gender' => 'required',
            'religion' => 'required',
            'date' => 'required',
            'photo' => 'image|mimes:jpg,jpeg,png|max:2048'
        ]);

        if ($validator->fails()) {
            return redirect('/admin')
                ->withErrors($validator);
        }

        // hashing password
        $request['password'] = Hash::make($request['password']);

        // upload gambar
        $photo = $request->file('photo');
        $photo->storeAs('public/admin', $photo->hashName());

        Admin::create([
            'username' => $request->username,
            'name' => $request->name,
            'password' => $request['password'],
            'email' => $request->email,
            'phone' => $request->phone,
            'gender' => $request->gender,
            'religion' => $request->religion,
            'date' => $request->date,
            'photo' => $photo->hashName()
        ]);

        return redirect('/admin')->with('notifadd', 'Berhasil Menambahkan');
    }
    // update admin
    public function upadmin(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required|regex:/^[a-zA-z\s]*$/',
            'name' => 'required|regex:/^[a-zA-z\s]*$/',
            'email' => 'required',
            'phone' => 'required|regex:/^[\d\-\s\(\)+]+$/',
            'gender' => 'required',
            'religion' => 'required',
            'date' => 'required',
            'photo' => 'image|mimes:jpg,jpeg,png|max:2048'
        ]);

        if ($validator->fails()) {
            return redirect('/admin')
                ->withErrors($validator);
        }

        // hashing password
        $request['password'] = Hash::make($request['password']);

        if ($request->hasFile('photo')) {

            // upload gambar
            $photo = $request->file('photo');
            $photo->storeAs('public/admin', $photo->hashName());

            // menghapus gambar lama
            Storage::delete('public/admin/' . $request->oldphoto);

            $admin = Admin::find($request->id);

            // update produk lama dengan produk yang baru
            $admin->update([
                'username' => $request->username,
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'gender' => $request->gender,
                'religion' => $request->religion,
                'date' => $request->date,
                'photo' => $photo->hashName()
            ]);
        } else {
            // update produk tanpa gambar

            $admin = Admin::find($request->id);

            // update produk lama dengan produk yang baru
            $admin->update([
                'username' => $request->username,
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'gender' => $request->gender,
                'religion' => $request->religion,
                'date' => $request->date
            ]);
        }

        return redirect('/admin')->with('notifupdate', 'Berhasil Menambahkan');
    }
    // update pass admin
    public function uppassadmin(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'password' => 'required|min:3',
            'confirmation' => 'required'
        ]);

        if ($validator->fails()) {
            return redirect('/admin')
                ->withErrors($validator);
        }

        if ($request->confirmation == $request->password) {

            $admin = Admin::find($request->id);

            $request['password'] = Hash::make($request['password']);

            $admin->update([
                'password' => $request['password']
            ]);

            return redirect('/admin')->with('uppass', 'Berhasil Menambahkan');
        } else {
            return redirect('/admin')->with('uppassgal', 'Berhasil Menambahkan');
        }
    }
    // hapus admin
    public function deladmin(Request $request)
    {
        $data = Admin::find($request->id);

        $data->delete();

        return redirect('/admin')->with('notifhapus', 'Berhasil Menambahkan');
    }

    // petugas

    // tambah petugas
    public function addpetugas(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required|unique:petugas|regex:/^[a-zA-z\s]*$/',
            'name' => 'required|regex:/^[a-zA-z\s]*$/',
            'password' => 'required|min:5',
            'email' => 'required|unique:petugas',
            'phone' => 'required|regex:/^[\d\-\s\(\)+]+$/',
            'gender' => 'required',
            'religion' => 'required',
            'date' => 'required',
            'photo' => 'image|mimes:jpg,jpeg,png|max:2048'
        ]);

        if ($validator->fails()) {
            return redirect('/petugas')
                ->withErrors($validator);
        }

        // hashing password
        $request['password'] = Hash::make($request['password']);

        // upload gambar
        $photo = $request->file('photo');
        $photo->storeAs('public/petugas', $photo->hashName());

        petugas::create([
            'username' => $request->username,
            'name' => $request->name,
            'password' => $request['password'],
            'email' => $request->email,
            'phone' => $request->phone,
            'gender' => $request->gender,
            'religion' => $request->religion,
            'date' => $request->date,
            'photo' => $photo->hashName()
        ]);

        return redirect('/petugas')->with('notifadd', 'Berhasil Menambahkan');
    }
    // update petugas
    public function uppetugas(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required|regex:/^[a-zA-z\s]*$/',
            'name' => 'required|regex:/^[a-zA-z\s]*$/',
            'email' => 'required',
            'phone' => 'required|regex:/^[\d\-\s\(\)+]+$/',
            'gender' => 'required',
            'religion' => 'required',
            'date' => 'required',
            'photo' => 'image|mimes:jpg,jpeg,png|max:2048'
        ]);

        if ($validator->fails()) {
            return redirect('/petugas')
                ->withErrors($validator);
        }

        // hashing password
        $request['password'] = Hash::make($request['password']);

        if ($request->hasFile('photo')) {

            // upload gambar
            $photo = $request->file('photo');
            $photo->storeAs('public/petugas', $photo->hashName());

            // menghapus gambar lama
            Storage::delete('public/petugas/' . $request->oldphoto);

            $petugas = petugas::find($request->id);

            // update produk lama dengan produk yang baru
            $petugas->update([
                'username' => $request->username,
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'gender' => $request->gender,
                'religion' => $request->religion,
                'date' => $request->date,
                'photo' => $photo->hashName()
            ]);
        } else {
            // update produk tanpa gambar

            $petugas = petugas::find($request->id);

            // update produk lama dengan produk yang baru
            $petugas->update([
                'username' => $request->username,
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'gender' => $request->gender,
                'religion' => $request->religion,
                'date' => $request->date
            ]);
        }

        return redirect('/petugas')->with('notifupdate', 'Berhasil Menambahkan');
    }
    // update pass petugas
    public function uppasspetugas(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'password' => 'required|min:5',
            'confirmation' => 'required'
        ]);

        if ($validator->fails()) {
            return redirect('/petugas')
                ->withErrors($validator);
        }

        if ($request->confirmation == $request->password) {

            $petugas = petugas::find($request->id);

            $request['password'] = Hash::make($request['password']);

            $petugas->update([
                'password' => $request['password']
            ]);

            return redirect('/petugas')->with('uppass', 'Berhasil Menambahkan');
        } else {
            return redirect('/petugas')->with('uppassgal', 'Berhasil Menambahkan');
        }
    }
    // hapus petugas
    public function delpetugas(Request $request)
    {
        $data = petugas::find($request->id);

        $data->delete();

        return redirect('/petugas')->with('notifhapus', 'Berhasil Menambahkan');
    }


    // detail buku

    // tambah buku
    public function addbuku(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'tanggal' => 'required',
            'isbn' => 'required|regex:/^[0-9-]+$/|unique:bukus',
            'pengarang' => 'required|regex:/^[a-zA-z\s]*$/',
            'judul' => 'required|regex:/^[a-zA-Z0-9&\-,.\s]+$/',
            'eks' => 'required|numeric',
            'thn_inv' => 'required|numeric',
            'asl_id' => 'required',
            'ktg_id' => 'required',
            'bhs_id' => 'required',
            'tahun_terbit' => 'required|numeric',
            'sinopsis' => 'regex:/^[a-zA-Z0-9\-]+$/',
            'ket' => 'regex:/^[a-zA-Z0-9\-]+$/',
            'photo' => 'image|mimes:jpg,jpeg,png|max:2048',
            'jumlah' => 'required'
        ]);

        if ($request->kondisi == 'admin') {
            if ($validator->fails()) {
                return redirect('/buku')
                    ->withErrors($validator);
            }
        } else {
            if ($validator->fails()) {
                return redirect('/petdatabuku')
                    ->withErrors($validator);
            }
        }

        if ($request->hasFile('photo')) {

            // upload gambar
            $photo = $request->file('photo');
            $photo->storeAs('public/buku', $photo->hashName());
            $namafoto = $photo->hashName();
        } else {
            $namafoto = "default.png";
        }

        $jml = $request->jumlah;

        $lastBook = buku::latest('no_inv')->first();

        $start = 1;

        if ($lastBook) {
            $noAkhir = (int) substr($lastBook->no_inv, -5);
            $start = $noAkhir + 1;
        }

        for ($i = $start; $i < $start + $jml; $i++) {

            buku::create([
                'tanggal' => $request->tanggal,
                'isbn' => $request->isbn,
                'pengarang' => $request->pengarang,
                'judul' => $request->judul,
                'eks' => $request->eks,
                'thn_inv' => $request->thn_inv,
                'asl_id' => $request->asl_id,
                'ktg_id' => $request->ktg_id,
                'bhs_id' => $request->bhs_id,
                'no_inv' => str_pad($i, 5, '0', STR_PAD_LEFT),
                'tahun_terbit' => $request->tahun_terbit,
                'sinopsis' => $request->sinopsis,
                'photo' => $namafoto,
                'ket' => $request->ket,
                'status' => '1'
            ]);
        }

        if ($request->role == "admin") {
            return redirect('/buku')->with('notifadd', 'Berhasil Menambahkan');
        } else {
            return redirect('/petdatabuku')->with('notifadd', 'Berhasil Menambahkan');
        }
    }
    // update buku
    public function upbuku(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'tanggal' => 'required',
            'isbn' => 'required|regex:/^[0-9-]+$/',
            'pengarang' => 'required|regex:/^[a-zA-z\s]*$/',
            'judul' => 'required|regex:/^[a-zA-Z0-9&\-,.\s]+$/',
            'eks' => 'required|numeric',
            'thn_inv' => 'required|numeric',
            'asl_id' => 'required',
            'ktg_id' => 'required',
            'bhs_id' => 'required',
            'no_inv' => 'required',
            'tahun_terbit' => 'required|numeric',
            'sinopsis' => 'regex:/^[a-zA-Z0-9\-]+$/',
            'ket' => 'regex:/^[a-zA-Z0-9\-]+$/',
            'photo' => 'image|mimes:jpg,jpeg,png|max:2048',
        ]);

        if ($request->role == "admin") {
            if ($validator->fails()) {
                return redirect('/buku')
                    ->withErrors($validator);
            }
        } else {
            if ($validator->fails()) {
                return redirect('/petdatabuku')
                    ->withErrors($validator);
            }
        }


        if ($request->hasFile('photo')) {

            if ($request->oldphoto != "default.png") {

                // upload gambar
                $photo = $request->file('photo');
                $photo->storeAs('public/buku', $photo->hashName());

                // menghapus gambar lama
                Storage::delete('public/buku/' . $request->oldphoto);
            } else {

                // upload gambar
                $photo = $request->file('photo');
                $photo->storeAs('public/buku', $photo->hashName());
            }

            $buku = buku::find($request->id);

            // update buku lama dengan buku yang baru
            $buku->update([

                'tanggal' => $request->tanggal,
                'isbn' => $request->isbn,
                'pengarang' => $request->pengarang,
                'judul' => $request->judul,
                'eks' => $request->eks,
                'thn_inv' => $request->thn_inv,
                'asl_id' => $request->asl_id,
                'ktg_id' => $request->ktg_id,
                'bhs_id' => $request->bhs_id,
                'no_inv' => $request->no_inv,
                'tahun_terbit' => $request->tahun_terbit,
                'sinopsis' => $request->sinopsis,
                'photo' => $photo->hashName(),
                'ket' => $request->ket
            ]);
        } else {
            // update buku tanpa gambar

            $buku = buku::find($request->id);

            // update buku lama dengan buku yang baru
            $buku->update([

                'tanggal' => $request->tanggal,
                'isbn' => $request->isbn,
                'pengarang' => $request->pengarang,
                'judul' => $request->judul,
                'eks' => $request->eks,
                'thn_inv' => $request->thn_inv,
                'asl_id' => $request->asl_id,
                'ktg_id' => $request->ktg_id,
                'bhs_id' => $request->bhs_id,
                'no_inv' => $request->no_inv,
                'tahun_terbit' => $request->tahun_terbit,
                'sinopsis' => $request->sinopsis,
                'ket' => $request->ket

            ]);
        }

        return redirect('/buku')->with('notifupdate', 'Berhasil Menambahkan');
    }
    // hapus buku
    public function delbuku(Request $request)
    {
        $data = buku::find($request->id);

        $data->delete();

        return redirect('/buku')->with('notifhapus', 'Berhasil Menambahkan');
    }

    // buku

    // update buku
    public function upbukus(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'isbn' => 'required|regex:/^[0-9-]+$/',
            'pengarang' => 'required|regex:/^[a-zA-z\s]*$/',
            'judul' => 'required|regex:/^[a-zA-Z0-9&\-,.\s]+$/',
            'thn_inv' => 'required|numeric',
            'asl_id' => 'required',
            'ktg_id' => 'required',
            'bhs_id' => 'required',
            'tahun_terbit' => 'required|numeric',
            'sinopsis' => 'regex:/^[a-zA-Z0-9\-]+$/',
            'ket' => 'regex:/^[a-zA-Z0-9\-]+$/',
            'photo' => 'image|mimes:jpg,jpeg,png|max:2048',
        ]);

        if ($request->role == "admin") {
            if ($validator->fails()) {
                return redirect('/buku')
                    ->withErrors($validator);
            }
        } else {
            if ($validator->fails()) {
                return redirect('/petdatabuku')
                    ->withErrors($validator);
            }
        }


        if ($request->hasFile('photo')) {

            if ($request->oldphoto != "default.png") {

                // upload gambar
                $photo = $request->file('photo');
                $photo->storeAs('public/buku', $photo->hashName());

                // menghapus gambar lama
                Storage::delete('public/buku/' . $request->oldphoto);
            } else {

                // upload gambar
                $photo = $request->file('photo');
                $photo->storeAs('public/buku', $photo->hashName());
            }

            $buku = buku::select('*')
                ->where('isbn', '=', $request->isbn);

            // update buku lama dengan buku yang baru
            $buku->update([
                'isbn' => $request->isbn,
                'pengarang' => $request->pengarang,
                'judul' => $request->judul,
                'thn_inv' => $request->thn_inv,
                'asl_id' => $request->asl_id,
                'ktg_id' => $request->ktg_id,
                'bhs_id' => $request->bhs_id,
                'tahun_terbit' => $request->tahun_terbit,
                'sinopsis' => $request->sinopsis,
                'photo' => $photo->hashName(),
                'ket' => $request->ket
            ]);
        } else {
            // update buku tanpa gambar

            $buku = buku::select('*')
                ->where('isbn', '=', $request->isbn);

            // update buku lama dengan buku yang baru
            $buku->update([

                'isbn' => $request->isbn,
                'pengarang' => $request->pengarang,
                'judul' => $request->judul,
                'thn_inv' => $request->thn_inv,
                'asl_id' => $request->asl_id,
                'ktg_id' => $request->ktg_id,
                'bhs_id' => $request->bhs_id,
                'tahun_terbit' => $request->tahun_terbit,
                'sinopsis' => $request->sinopsis,
                'ket' => $request->ket
            ]);
        }

        if ($request->role == "admin") {
            return redirect('/bukus')->with('notifupdate', 'Berhasil Menambahkan');
        } else {
            return redirect('/petdatabuku')->with('notifupdate', 'Berhasil Menambahkan');
        }
    }
    // hapus buku
    public function delbukus(Request $request)
    {
        $data = buku::select('*')
            ->where('isbn', '=', $request->id);

        $data->delete();

        if ($request->role == "admin") {
            return redirect('/bukus')->with('notifhapus', 'Berhasil Menambahkan');
        } else {
            return redirect('/petdatabuku')->with('notifhapus', 'Berhasil Menambahkan');
        }
    }


    // peminjaman & pengembalian

    // ubah status
    public function ubahpeminjaman(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'kode' => 'required',
            'qtypinjam' => 'required',
            'id_petugas' => 'required',
            'tgl_kembali' => 'required',
            'tgl_pengembalian' => 'required',
            'qtykembali' => 'required',
            'isbn' => 'required',
            'keterangan' => 'required'
        ]);

        if ($request->kondisi == 'admin') {
            if ($validator->fails()) {
                return redirect('/peminjaman')
                    ->withErrors($validator);
            }
        } else {
            if ($validator->fails()) {
                return redirect('/petdatapeminjaman')
                    ->withErrors($validator);
            }
        }



        $tglkembali = Carbon::parse($request->tgl_kembali);
        $tglhariini = Carbon::parse(date('Y-m-d'));

        $denda_hilang = 100000;
        $denda_telat = 5000;

        if ($tglhariini->isAfter($tglkembali)) {
            $jarak = $tglkembali->diff($tglhariini);
            $jarakhari = $jarak->d;
        } else {
            $jarakhari = 0;
        }

        $qty = $request->qtypinjam - $request->qtykembali;

        $dendahilang = $qty * $denda_hilang;
        $dendatelat = $denda_telat * $jarakhari;

        $denda = $dendahilang + $dendatelat;

        pengembalian::create([
            'kode' => $request->kode,
            'tgl_kembali' => $request->tgl_pengembalian,
            'denda' => $denda,
            'qty' => $request->qtykembali,
            'keterangan' => $request->keterangan,
            'id_petugas' => $request->id_petugas
        ]);

        $data = pinjam::select('*')
            ->where('kode', '=', $request->kode)
            ->first();

        $datadetail = buku::select('*')
            ->where('isbn', '=', $request->isbn)
            ->where('status', '=', '0')
            ->take($request->qtykembali);

        $data->update([
            'status' => 'dikembalikan'
        ]);

        $datadetail->update([
            'status' => '1'
        ]);


        if ($request->kondisi == 'admin') {
            return redirect('/peminjaman')->with('notifubah', 'Berhasil Menambahkan');
        } else {
            return redirect('/petdatapeminjaman')->with('notifubah', 'Berhasil Menambahkan');
        }
    }
    // hapus peminjaman
    public function delpeminjaman(Request $request)
    {
        $data = pinjam::select('*')
            ->where('kode', '=', $request->id)
            ->first();
        $datadetail = detailpinjam::select('*')
            ->where('kode', '=', $request->id)
            ->first();
        $datapengembalian = pengembalian::select('*')
            ->where('kode', '=', $request->id)
            ->first();


        $data->delete();
        $datadetail->delete();
        if ($datapengembalian != null) {
            $datapengembalian->delete();
        }

        if ($request->kondisi == 'admin') {
            return redirect('/peminjaman')->with('notifubah', 'Berhasil Menambahkan');
        } else {
            return redirect('/petdatapeminjaman')->with('notifubah', 'Berhasil Menambahkan');
        }
    }
    // hapus pengembalian
    public function delpengembalian(Request $request)
    {
        $data = pengembalian::select('*')
            ->where('kode', '=', $request->id)
            ->first();

        $data2 = pinjam::select('*')
            ->where('kode', '=', $request->id)
            ->first();

        $datadetail = buku::select('*')
            ->where('isbn', '=', $request->isbn)
            ->where('status', '=', '1')
            ->take($request->qtykembali);

        $datadetail->update([
            'status' => '0'
        ]);

        $data->delete();

        $data2->update([
            'status' => 'dihapus'
        ]);

        if ($request->kondisi == 'admin') {
            return redirect('/pengembalian')->with('notifhapus', 'Berhasil Menambahkan');
        } else {
            return redirect('/petdatapengembalian')->with('notifhapus', 'Berhasil Menambahkan');
        }
    }
}
