<?php

namespace App\Http\Controllers;

use App\Charts\PeminjamanChart;
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

use function Laravel\Prompts\table;

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

        if (Auth::guard('admin')->check()) {
            return redirect('/dashboard');
        } else if (Auth::guard('petugas')->check()) {
            return redirect('/petdashboard');
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

            return view('landing', [
                'bukus' => $databuku->get()
            ]);
        }
    }

    // ke halaman dashboard
    public function dashboard(PeminjamanChart $chart)
    {

        $subquery = detailpinjam::select('isbn', DB::raw('COUNT(isbn) AS jumlah_sebelum_group'))
        ->groupBy('isbn');

        $bukularis = detailpinjam::select('detailpinjams.isbn', 'bukus.judul', DB::raw('COALESCE(sub.jumlah_sebelum_group, 0) AS jumlah'))
            ->leftJoinSub($subquery, 'sub', function ($join) {
                $join->on('detailpinjams.isbn', '=', 'sub.isbn');
            })
            ->join('bukus', 'detailpinjams.isbn', '=', 'bukus.isbn')
            ->groupBy('isbn', 'judul', 'jumlah_sebelum_group')
            ->orderBy('jumlah', 'desc')
            ->take(5);

        $anggotafav = pinjam::select('pinjams.id_anggota', 'anggotas.name', DB::raw('COUNT(pinjams.id) AS jumlah'))
            ->join('anggotas', 'pinjams.id_anggota', '=', 'anggotas.nisn')
            ->groupBy('pinjams.id_anggota', 'anggotas.name')
            ->orderBy('jumlah', 'desc')
            ->take(5);

        $history = DB::table('logpeminjaman')
            ->select('*')
            ->orderBy('log_time', 'desc')
            ->take(5);


        return view('admin.page.dashboard', [
            'chart' => $chart->build(),
            'user' => Auth::user(),
            'jumlahbuku' => buku::count(),
            'jumlahpeminjaman' => pinjam::count(),
            'jumlahanggota' => anggota::count(),
            'jumlahadmin' => Admin::count(),
            'datalaris' => $bukularis->get(),
            'datalog' => $history->get(),
            'anggotafav' => $anggotafav->get()
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
            $databuku = buku::select('bukus.id', DB::raw("DATE_FORMAT(bukus.tanggal, '%d %M %Y') AS tanggal"), 'bukus.tanggal AS tgls', 'bukus.isbn', 'bukus.pengarang', 'bukus.judul', 'bukus.eks', 'bukus.thn_inv', 'bukus.asl_id', 'bukus.ktg_id', 'bukus.bhs_id', 'asals.name AS asal', 'kategoris.name AS kategori', 'bahasas.name AS bahasa', 'bukus.no_inv', 'bukus.tahun_terbit', 'bukus.sinopsis', 'bukus.photo', 'bukus.ket', 'bukus.status')
                ->join('asals', 'asals.id', '=', 'bukus.asl_id')
                ->join('kategoris', 'kategoris.id', '=', 'bukus.ktg_id')
                ->join('bahasas', 'bahasas.id', '=', 'bukus.bhs_id')
                ->where('judul', 'LIKE', '%' . $request->cari . '%')
                ->OrWhere('isbn', 'LIKE', '%' . $request->cari . '%')
                ->OrWhere('pengarang', 'LIKE', '%' . $request->cari . '%')
                ->OrWhere('no_inv', 'LIKE', '%' . $request->cari . '%');
        } else {
            $databuku = buku::select('bukus.id', DB::raw("DATE_FORMAT(bukus.tanggal, '%d %M %Y') AS tanggal"), 'bukus.tanggal AS tgls', 'bukus.isbn', 'bukus.pengarang', 'bukus.judul', 'bukus.eks', 'bukus.thn_inv', 'asals.name AS asal', 'kategoris.name AS kategori', 'bahasas.name AS bahasa', 'bukus.no_inv', 'bukus.asl_id', 'bukus.ktg_id', 'bukus.bhs_id', 'bukus.tahun_terbit', 'bukus.sinopsis', 'bukus.photo', 'bukus.ket', 'bukus.status')
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
            $datapeminjaman = pinjam::select(
                'pinjams.kode',
                'pinjams.id_anggota',
                'pinjams.guru',
                'pinjams.tgl_pinjam',
                'pinjams.tgl_kembali',
                'pinjams.id_petugas',
                'pinjams.status',
                'anggotas.name AS anggota',
                'petugas.name AS petugas',
                DB::raw('GROUP_CONCAT(detailpinjams.id_buku) AS id_buku_array'),
                DB::raw('LENGTH(GROUP_CONCAT(detailpinjams.id_buku)) - LENGTH(REPLACE(GROUP_CONCAT(detailpinjams.id_buku), ",", "")) + 1 AS jumlah')
            )
                ->join('anggotas', 'anggotas.nisn', '=', 'pinjams.id_anggota')
                ->join('petugas', 'petugas.id', '=', 'pinjams.id_petugas')
                ->leftJoin('detailpinjams', 'detailpinjams.kode', '=', 'pinjams.kode')
                ->groupBy('pinjams.kode', 'pinjams.id_anggota', 'pinjams.guru', 'pinjams.tgl_pinjam', 'pinjams.tgl_kembali', 'pinjams.id_petugas', 'pinjams.status', 'anggota', 'petugas')
                ->where('pinjams.kode', 'LIKE', '%' . $request->cari . '%')
                ->OrWhere('anggotas.name', 'LIKE', '%' . $request->cari . '%')
                ->OrWhere('anggotas.nisn', 'LIKE', '%' . $request->cari . '%');
        } else {
            $datapeminjaman = pinjam::select(
                'pinjams.kode',
                'pinjams.id_anggota',
                'pinjams.guru',
                'pinjams.tgl_pinjam',
                'pinjams.tgl_kembali',
                'pinjams.id_petugas',
                'pinjams.status',
                'anggotas.name AS anggota',
                'petugas.name AS petugas',
                DB::raw('GROUP_CONCAT(detailpinjams.id_buku) AS id_buku_array'),
                DB::raw('LENGTH(GROUP_CONCAT(detailpinjams.id_buku)) - LENGTH(REPLACE(GROUP_CONCAT(detailpinjams.id_buku), ",", "")) + 1 AS jumlah')
            )
                ->join('anggotas', 'anggotas.nisn', '=', 'pinjams.id_anggota')
                ->join('petugas', 'petugas.id', '=', 'pinjams.id_petugas')
                ->leftJoin('detailpinjams', 'detailpinjams.kode', '=', 'pinjams.kode')
                ->groupBy('pinjams.kode', 'pinjams.id_anggota', 'pinjams.guru', 'pinjams.tgl_pinjam', 'pinjams.tgl_kembali', 'pinjams.id_petugas', 'pinjams.status', 'anggota', 'petugas');
        }


        return view('admin.page.datapeminjaman', [
            'lastquery' => $request->cari,
            'datapeminjaman' => $datapeminjaman->get(),
            'anggotas' => anggota::all(),
            'user' => Auth::user()
        ]);
    }

    // ke halaman datapengembalian
    public function datapengembalian(Request $request)
    {

        if ($request->has('cari')) {
            $datapengembalian = pengembalian::select('pengembalians.*', 'petugas.name AS petugas', 'anggotas.name AS anggota', 'pinjams.tgl_kembali AS tglwajib')
                ->join('petugas', 'pengembalians.id_petugas', '=', 'petugas.id')
                ->join('pinjams', 'pinjams.kode', '=', 'pengembalians.kode')
                ->join('anggotas', 'anggotas.nisn', '=', 'pinjams.id_anggota')
                ->where('pengembalians.kode', 'LIKE', '%' . $request->cari . '%')
                ->OrWhere('anggotas.name', 'LIKE', '%' . $request->cari . '%');
        } else {
            $datapengembalian = pengembalian::select('pengembalians.*', 'petugas.name AS petugas', 'anggotas.name AS anggota', 'pinjams.tgl_kembali AS tglwajib')
                ->join('petugas', 'pengembalians.id_petugas', '=', 'petugas.id')
                ->join('pinjams', 'pinjams.kode', '=', 'pengembalians.kode')
                ->join('anggotas', 'anggotas.nisn', '=', 'pinjams.id_anggota');
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
            'address' => 'required',
            'photo' => 'image|mimes:jpg,jpeg,png|max:2048'
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


        if ($request->hasFile('photo')) {

            $photo = $request->file('photo');
            $photo->storeAs('public/anggota', $photo->hashName());

            anggota::create([
                'nisn' => $request->nisn,
                'name' => $request->name,
                'gender' => $request->gender,
                'date' => $request->date,
                'phone' => $request->phone,
                'address' => $request->address,
                'photo' => $photo->hashName(),
                'status' => '1'
            ]);
        } else {
            anggota::create([
                'nisn' => $request->nisn,
                'name' => $request->name,
                'gender' => $request->gender,
                'date' => $request->date,
                'phone' => $request->phone,
                'address' => $request->address,
                'photo' => 'profile.png',
                'status' => '1'
            ]);
        }

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
            'address' => 'required',
            'photo' => 'image|mimes:jpg,jpeg,png|max:2048',
            'status' => 'required'
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

        if ($request->hasFile('photo')) {

            if ($request->oldphoto != "default.png") {

                // upload gambar
                $photo = $request->file('photo');
                $photo->storeAs('public/anggota', $photo->hashName());

                // menghapus gambar lama
                Storage::delete('public/anggota/' . $request->oldphoto);
            } else {

                // upload gambar
                $photo = $request->file('photo');
                $photo->storeAs('public/anggota', $photo->hashName());
            }

            $data = anggota::find($request->id);

            $data->update([
                'nisn' => $request->nisn,
                'name' => $request->name,
                'gender' => $request->gender,
                'date' => $request->date,
                'phone' => $request->phone,
                'address' => $request->address,
                'photo' => $photo->hashName(),
                'status' => $request->status
            ]);
        } else {

            // update anggota tanpa gambar

            $data = anggota::find($request->id);

            $data->update([
                'nisn' => $request->nisn,
                'name' => $request->name,
                'gender' => $request->gender,
                'date' => $request->date,
                'phone' => $request->phone,
                'address' => $request->address,
                'status' => $request->status
            ]);
        }

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

        if ($request->hasFile('photo')) {

            Admin::create([
                'username' => $request->username,
                'name' => $request->name,
                'password' => $request['password'],
                'email' => $request->email,
                'phone' => $request->phone,
                'gender' => $request->gender,
                'religion' => $request->religion,
                'date' => $request->date,
                'photo' => $photo->hashName(),
                'status' => 'admin'
            ]);
        } else {
            Admin::create([
                'username' => $request->username,
                'name' => $request->name,
                'password' => $request['password'],
                'email' => $request->email,
                'phone' => $request->phone,
                'gender' => $request->gender,
                'religion' => $request->religion,
                'date' => $request->date,
                'photo' => 'profile.png',
                'status' => 'admin'
            ]);
        }

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

        if ($request->hasFile('photo')) {

            if ($request->oldphoto != "default.png") {

                // upload gambar
                $photo = $request->file('photo');
                $photo->storeAs('public/admin', $photo->hashName());
                // menghapus gambar lama
                Storage::delete('public/admin/' . $request->oldphoto);
            } else {

                // upload gambar
                $photo = $request->file('photo');
                $photo->storeAs('public/admin', $photo->hashName());
            }


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
    // update buku individu
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
            'status' => 'required'
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
                'ket' => $request->ket,
                'status' => $request->status
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
                'ket' => $request->ket,
                'status' => $request->status

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

    // update buku group
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

    // hapus peminjaman
    public function delpeminjaman(Request $request)
    {
        $data = pinjam::select('*')
            ->where('kode', '=', $request->id)
            ->first();
        $datadetail = detailpinjam::select('*')
            ->where('kode', '=', $request->id)
            ->get();
        $datapengembalian = pengembalian::select('*')
            ->where('kode', '=', $request->id)
            ->first();



        $data->delete();

        foreach ($datadetail as $detail) {
            $detail->delete();
        }

        if ($datapengembalian != null) {
            $datapengembalian->delete();
        }

        if ($request->kondisi == 'admin') {
            return redirect('/peminjaman')->with('notifhapus', 'Berhasil Menambahkan');
        } else {
            return redirect('/petdatapeminjaman')->with('notifhapus', 'Berhasil Menambahkan');
        }
    }

    // print

    // print laporan
    public function printlaporan(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'Start' => 'required',
            'End' => 'required',
        ]);

        $validatorname = Validator::make($request->all(), [
            'username' => 'required'
        ]);

        if ($validator->fails() && $validatorname->fails()) {

            $data = pinjam::select(
                'pinjams.kode',
                'pinjams.id_anggota',
                'pinjams.guru',
                'pinjams.tgl_pinjam',
                'pinjams.tgl_kembali',
                'pinjams.id_petugas',
                'pinjams.status',
                'anggotas.name AS anggota',
                'petugas.name AS petugas',
                'bukus.judul AS buku'
            )
                ->join('anggotas', 'anggotas.nisn', '=', 'pinjams.id_anggota')
                ->join('petugas', 'petugas.id', '=', 'pinjams.id_petugas')
                ->leftJoin('detailpinjams', 'detailpinjams.kode', '=', 'pinjams.kode')
                ->join('bukus', 'bukus.id', '=', 'detailpinjams.id_buku')
                ->orderBy('pinjams.created_at', 'asc');

            $st = pinjam::select('pinjams.tgl_pinjam')
                ->orderBy('tgl_pinjam', 'asc')
                ->first();

            $en = pinjam::select('pinjams.tgl_pinjam')
                ->orderBy('tgl_pinjam', 'desc')
                ->first();

            $start = $st->tgl_pinjam;
            $end = $en->tgl_pinjam;
        } elseif ($validatorname->fails()) {

            $start = $request->Start;
            $end = $request->End;

            $data = pinjam::select(
                'pinjams.kode',
                'pinjams.id_anggota',
                'pinjams.guru',
                'pinjams.tgl_pinjam',
                'pinjams.tgl_kembali',
                'pinjams.id_petugas',
                'pinjams.status',
                'anggotas.name AS anggota',
                'petugas.name AS petugas',
                'bukus.judul AS buku'
            )
                ->join('anggotas', 'anggotas.nisn', '=', 'pinjams.id_anggota')
                ->join('petugas', 'petugas.id', '=', 'pinjams.id_petugas')
                ->leftJoin('detailpinjams', 'detailpinjams.kode', '=', 'pinjams.kode')
                ->join('bukus', 'bukus.id', '=', 'detailpinjams.id_buku')
                ->orderBy('pinjams.created_at', 'asc')
                ->whereBetween('pinjams.tgl_pinjam', [$start, $end]);
        } elseif ($validator->fails()) {

            $username = $request->username;

            $data = pinjam::select(
                'pinjams.kode',
                'pinjams.id_anggota',
                'pinjams.guru',
                'pinjams.tgl_pinjam',
                'pinjams.tgl_kembali',
                'pinjams.id_petugas',
                'pinjams.status',
                'anggotas.name AS anggota',
                'petugas.name AS petugas',
                'bukus.judul AS buku'
            )
                ->join('anggotas', 'anggotas.nisn', '=', 'pinjams.id_anggota')
                ->join('petugas', 'petugas.id', '=', 'pinjams.id_petugas')
                ->leftJoin('detailpinjams', 'detailpinjams.kode', '=', 'pinjams.kode')
                ->join('bukus', 'bukus.id', '=', 'detailpinjams.id_buku')
                ->orderBy('pinjams.created_at', 'asc')
                ->where('anggotas.name', '=', $username);

            $st = pinjam::select('pinjams.tgl_pinjam')
                ->join('anggotas', 'pinjams.id_anggota', '=', 'anggotas.nisn')
                ->orderBy('tgl_pinjam', 'asc')
                ->where('anggotas.name', '=', $username)
                ->first();


            $en = pinjam::select('pinjams.tgl_pinjam')
                ->join('anggotas', 'pinjams.id_anggota', '=', 'anggotas.nisn')
                ->orderBy('tgl_pinjam', 'asc')
                ->where('anggotas.name', '=', $username)
                ->first();

            $start = $st->tgl_pinjam;
            $end = $en->tgl_pinjam;
        } else {

            $username = $request->username;
            $start = $request->Start;
            $end = $request->End;

            $data = pinjam::select(
                'pinjams.kode',
                'pinjams.id_anggota',
                'pinjams.guru',
                'pinjams.tgl_pinjam',
                'pinjams.tgl_kembali',
                'pinjams.id_petugas',
                'pinjams.status',
                'anggotas.name AS anggota',
                'petugas.name AS petugas',
                'bukus.judul AS buku'
            )
                ->join('anggotas', 'anggotas.nisn', '=', 'pinjams.id_anggota')
                ->join('petugas', 'petugas.id', '=', 'pinjams.id_petugas')
                ->leftJoin('detailpinjams', 'detailpinjams.kode', '=', 'pinjams.kode')
                ->join('bukus', 'bukus.id', '=', 'detailpinjams.id_buku')
                ->orderBy('pinjams.created_at', 'asc')
                ->where('anggotas.name', '=', $username)
                ->whereBetween('pinjams.tgl_pinjam', [$start, $end]);
        }

        return view('laporan.laporan-peminjaman', [
            'peminjamans' => $data->get(),
            'start' => $start,
            'end' => $end
        ]);
    }

    // print laporan buku
    public function printlaporanbuku(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'Start' => 'required',
            'End' => 'required',
        ]);

        if ($validator->fails()) {

            $data = buku::select('bukus.id', DB::raw("DATE_FORMAT(bukus.tanggal, '%d %M %Y') AS tanggal"), 'bukus.isbn', 'bukus.pengarang', 'bukus.judul', 'bukus.eks', 'bukus.thn_inv', 'asals.name AS asal', 'kategoris.name AS kategori', 'bahasas.name AS bahasa', 'bukus.no_inv', 'bukus.asl_id', 'bukus.ktg_id', 'bukus.bhs_id', 'bukus.tahun_terbit', 'bukus.sinopsis', 'bukus.photo', 'bukus.ket', 'bukus.status')
                ->join('asals', 'asals.id', '=', 'bukus.asl_id')
                ->join('kategoris', 'kategoris.id', '=', 'bukus.ktg_id')
                ->join('bahasas', 'bahasas.id', '=', 'bukus.bhs_id');

            $st = buku::select('bukus.tanggal')
                ->orderBy('tanggal', 'asc')
                ->first();


            $en = buku::select('bukus.tanggal')
                ->orderBy('tanggal', 'desc')
                ->first();

            $start = $st->tanggal;
            $end = $en->tanggal;
        } else {

            $start = $request->Start;
            $end = $request->End;

            $data = buku::select('bukus.id', DB::raw("DATE_FORMAT(bukus.tanggal, '%d %M %Y') AS tanggal"), 'bukus.isbn', 'bukus.pengarang', 'bukus.judul', 'bukus.eks', 'bukus.thn_inv', 'asals.name AS asal', 'kategoris.name AS kategori', 'bahasas.name AS bahasa', 'bukus.no_inv', 'bukus.asl_id', 'bukus.ktg_id', 'bukus.bhs_id', 'bukus.tahun_terbit', 'bukus.sinopsis', 'bukus.photo', 'bukus.ket', 'bukus.status')
                ->join('asals', 'asals.id', '=', 'bukus.asl_id')
                ->join('kategoris', 'kategoris.id', '=', 'bukus.ktg_id')
                ->join('bahasas', 'bahasas.id', '=', 'bukus.bhs_id')
                ->whereBetween('bukus.tanggal', [$start, $end]);
        }

        return view('laporan.laporan-buku', [
            'databuku' => $data->get(),
            'start' => $start,
            'end' => $end
        ]);
    }
}
