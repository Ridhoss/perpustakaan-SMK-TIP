<?php

namespace App\Http\Controllers;

use App\Charts\PeminjamanChart;
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
use Carbon\Carbon;
use Dflydev\DotAccessData\Data;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PetugasController extends Controller
{


    // Route

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

        return view('petugas.page.petdashboard', [
            'chart' => $chart->build(),
            'user' => Auth::user(),
            'jumlahbuku' => buku::count(),
            'jumlahpeminjaman' => pinjam::count(),
            'jumlahanggota' => anggota::count(),
            'datalaris' => $bukularis->get(),
            'anggotafav' => $anggotafav->get()
        ]);
    }
    // ke halaman peminjaman
    public function peminjaman(Request $request)
    {

        if ($request->has('cari')) {
            $databuku = buku::select(
                'bukus.isbn',
                'bukus.pengarang',
                'bukus.judul',
                'bukus.thn_inv',
                DB::raw('SUM(CASE WHEN bukus.status = 1 THEN 1 ELSE 0 END) AS jumlah'),
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
                ->OrWhere('pengarang', 'LIKE', '%' . $request->cari . '%')
                ->orderByRaw('CASE WHEN bukus.status = 1 THEN 0 ELSE 1 END');
        } else {
            $databuku = buku::select(
                'bukus.isbn',
                'bukus.pengarang',
                'bukus.judul',
                'bukus.thn_inv',
                DB::raw('SUM(CASE WHEN bukus.status = 1 THEN 1 ELSE 0 END) AS jumlah'),
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
                ->orderByRaw('CASE WHEN bukus.status = 1 THEN 0 ELSE 1 END');
        }

        return view('petugas.page.petpeminjaman', [
            'user' => Auth::user(),
            'datbuku' => $databuku->get(),
            'lastquery' => $request->cari
        ]);
    }
    // ke halaman peminjamandetail
    public function peminjamandetail(Request $request)
    {

        $todays = date('Ymd');

        // Mendapatkan nomor terakhir dari database
        $lastNumber = pinjam::orderBy('id', 'desc')->value('kode');


        $start = 0;

        // Jika ada nomor terakhir yang ada sebelumnya, ambil angka terakhirnya
        if ($lastNumber == null) {
            $noAkhir = 0;
            $start = $noAkhir + 1;
        } else {
            if ($lastNumber) {
                $noAkhir = (int) substr($lastNumber, -5);
                $start = $noAkhir + 1;
            }
        }

        $end = min($start, 99999); // Ambil 5 nomor terakhir atau hingga 99999

        for ($i = $start; $i <= $end; $i++) {
            $newNumber = str_pad($i, 5, '0', STR_PAD_LEFT); // Format nomor dengan panjang 5 digit, mengisi dengan '0' di depan jika kurang dari 5 digit
        }

        $randomNumber = $todays . $newNumber;

        $buku = buku::select(
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
            ->where('status', '1')
            ->groupBy('bukus.tanggal', 'bukus.isbn', 'bukus.pengarang', 'bukus.judul', 'bukus.thn_inv', 'bukus.asl_id', 'bukus.ktg_id', 'bukus.bhs_id', 'asal', 'kategori', 'bahasa', 'bukus.tahun_terbit', 'bukus.sinopsis', 'bukus.photo', 'bukus.ket')
            ->where('bukus.isbn', '=', $request->buku)
            ->take(1);



        return view('petugas.page.petdetail', [
            'user' => Auth::user(),
            'datbuku' => $buku->get(),
            'datanggota' => anggota::all()->where('status', '=', '1'),
            'custom' => $randomNumber
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

        return view('petugas.page.petdataanggota', [
            'dataanggota' => $dataanggota->get(),
            'lastquery' => $request->cari,
            'user' => Auth::user()
        ]);
    }

    // ke halaman databuku
    public function databuku(Request $request)
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

        return view('petugas.page.petdatabuku', [
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


        return view('petugas.page.petdatapeminjaman', [
            'lastquery' => $request->cari,
            'datapeminjaman' => $datapeminjaman->get(),
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


        return view('petugas.page.petdatapengembalian', [
            'lastquery' => $request->cari,
            'lastquerymodal' => $request->searchpeminjaman,
            'datapengembalian' => $datapengembalian->get(),
            'user' => Auth::user()
        ]);
    }

    // ke halaman peminjaman tunggal
    public function peminjamantunggal(Request $request)
    {

        $todays = date('Ymd');

        // Mendapatkan nomor terakhir dari database
        $lastNumber = pinjam::orderBy('id', 'desc')->value('kode');


        $start = 0;

        // Jika ada nomor terakhir yang ada sebelumnya, ambil angka terakhirnya
        if ($lastNumber == null) {
            $noAkhir = 0;
            $start = $noAkhir + 1;
        } else {
            if ($lastNumber) {
                $noAkhir = (int) substr($lastNumber, -5);
                $start = $noAkhir + 1;
            }
        }

        $end = min($start, 99999); // Ambil 5 nomor terakhir atau hingga 99999

        for ($i = $start; $i <= $end; $i++) {
            $newNumber = str_pad($i, 5, '0', STR_PAD_LEFT); // Format nomor dengan panjang 5 digit, mengisi dengan '0' di depan jika kurang dari 5 digit
        }

        $randomNumber = $todays . $newNumber;

        $databuku = buku::select(
            'bukus.isbn',
            'bukus.pengarang',
            'bukus.judul',
            'bukus.thn_inv',
            DB::raw('SUM(CASE WHEN bukus.status = 1 THEN 1 ELSE 0 END) AS jumlah'),
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
            ->orderByRaw('CASE WHEN bukus.status = 1 THEN 0 ELSE 1 END');


        return view('petugas.page.petpeminjamantunggal', [
            'user' => Auth::user(),
            'datbuku' => $databuku->get(),
            'lastquery' => $request->cari,
            'datanggota' => anggota::all()->where('status', '=', '1'),
            'custom' => $randomNumber
        ]);
    }

    public function detpeminjamansis(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'buku' => 'required',
            'id_petugas' => 'required',
            'kode' => 'required',
            'id_anggota' => 'required',
            'peminjaman' => 'required',
            'pengembalian' => 'required'
        ]);

        if ($validator->fails()) {
            return redirect('/petpeminjamansiswa')
                ->withErrors($validator);
        }

        $anggota = anggota::select('*')
            ->where('nisn', $request->id_anggota);

        $petugas = petugas::select('*')
            ->where('id', $request->id_petugas);


        $buku = buku::select('isbn', 'judul', 'pengarang', 'photo', 'tahun_terbit')
            ->whereIn('isbn', $request->buku)
            ->groupBy('isbn', 'judul', 'pengarang', 'photo', 'tahun_terbit');

        return view('petugas.page.petdetailtunggal', [
            'user' => Auth::user(),
            'kode' => $request->kode,
            'anggota' => $anggota->first(),
            'petugas' => $petugas->first(),
            'tgl_pinjam' => $request->peminjaman,
            'tgl_kembali' => $request->pengembalian,
            'buku' => $buku->get()
        ]);
    }

    // ke halaman pengembalian
    public function pengembalian(Request $request)
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
                ->where('pinjams.status', '=', 'dipinjam')
                ->OrWhere('pinjams.kode', 'LIKE', '%' . $request->cari . '%')
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
                ->groupBy('pinjams.kode', 'pinjams.id_anggota', 'pinjams.guru', 'pinjams.tgl_pinjam', 'pinjams.tgl_kembali', 'pinjams.id_petugas', 'pinjams.status', 'anggota', 'petugas')
                ->where('pinjams.status', '=', 'dipinjam');
        }

        return view('petugas.page.petpengembalian', [
            'user' => Auth::user(),
            'lastquery' => $request->cari,
            'datapeminjaman' => $datapeminjaman->get()
        ]);
    }

    // ke detail pengembalian
    public function detailpengembalian(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'kode' => 'required',
        ]);

        if ($validator->fails()) {
            return redirect('/petpengembalian')
                ->withErrors($validator);
        }

        $peminjaman = pinjam::select('pinjams.*', 'petugas.name AS petugas', 'anggotas.name AS anggota', 'anggotas.nisn')
            ->where('pinjams.kode', $request->kode)
            ->join('petugas', 'pinjams.id_petugas', '=', 'petugas.id')
            ->join('anggotas', 'pinjams.id_anggota', '=', 'anggotas.nisn');

        $idbuku = detailpinjam::select('id_buku')
            ->where('kode', $request->kode);

        $buku = detailpinjam::select('detailpinjams.isbn', 'bukus.judul', DB::raw('COUNT(detailpinjams.qty) AS qty'), 'bukus.pengarang', 'bukus.photo', 'bukus.tahun_terbit')
            ->join('bukus', 'detailpinjams.id_buku', '=', 'bukus.id')
            ->groupBy('isbn', 'qty', 'judul', 'pengarang', 'photo', 'tahun_terbit')
            ->where('detailpinjams.kode', $request->kode);

        return view('petugas.page.petpengembaliandetail', [
            'user' => Auth::user(),
            'kode' => $request->kode,
            'peminjaman' => $peminjaman->first(),
            'bukus' => $buku->get()

        ]);
    }






    // crud


    // transaksi peminjaman
    public function addpeminjaman(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'id_petugas' => 'required',
            'isbn' => 'required',
            'kode' => 'required',
            'id_anggota' => 'required',
            'guru' => 'required',
            'peminjaman' => 'required',
            'pengembalian' => 'required',
            'qty' => 'required'
        ]);

        if ($validator->fails()) {
            return redirect('/petpeminjaman')
                ->withErrors($validator);
        }

        // query ke tabel head pinjam

        pinjam::create([
            'kode' => $request->kode,
            'id_anggota' => $request->id_anggota,
            'guru' => $request->guru,
            'tgl_pinjam' => $request->peminjaman,
            'tgl_kembali' => $request->pengembalian,
            'id_petugas' => $request->id_petugas,
            'status' => 'dipinjam'
        ]);

        // query ke tabel detail pinjam

        $bukus = buku::select('*')
            ->where('isbn', '=', $request->isbn)
            ->where('status', '=', '1')
            ->take($request->qty)
            ->get();

        foreach ($bukus as $buku) {

            $bukuf = buku::find($buku->id);

            $bukuf->update([
                'status' => '0'
            ]);

            detailpinjam::create([
                'kode' => $request->kode,
                'id_buku' => $buku->id,
                'isbn' => $buku->isbn,
                'qty' => '1'
            ]);
        }

        // query update status anggota

        $anggota = anggota::select('*')
            ->where('nisn', '=', $request->id_anggota)
            ->where('status', '=', '1')
            ->first();

        $anggota->update([
            'status' => '0'
        ]);

        // print

        $kode = $request->kode;

        return redirect('/printinvoice?kode=' . $kode . '&role=petugas');
    }


    // transaksi peminjaman siswa
    public function addpeminjamansiswa(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'kode' => 'required',
            'id_anggota' => 'required',
            'tgl_pinjam' => 'required',
            'tgl_kembali' => 'required',
            'id_petugas' => 'required',
            'buku' => 'required'
        ]);

        if ($validator->fails()) {
            return redirect('/petpeminjamansiswa')
                ->withErrors($validator);
        }

        // query ke tabel head pinjam

        pinjam::create([
            'kode' => $request->kode,
            'id_anggota' => $request->id_anggota,
            'guru' => '-',
            'tgl_pinjam' => $request->tgl_pinjam,
            'tgl_kembali' => $request->tgl_kembali,
            'id_petugas' => $request->id_petugas,
            'status' => 'dipinjam'
        ]);

        // query ke tabel detail pinjam

        $bukus = $request->buku;

        foreach ($bukus as $bukud) {

            $buku = buku::select('*')
                ->where('isbn', $bukud)
                ->where('status', '=', '1')
                ->first();

            $buku->update([
                'status' => '0'
            ]);

            detailpinjam::create([
                'kode' => $request->kode,
                'id_buku' => $buku->id,
                'isbn' => $buku->isbn,
                'qty' => '1'
            ]);
        }

        // query update status anggota

        $anggota = anggota::select('*')
            ->where('nisn', '=', $request->id_anggota)
            ->where('status', '=', '1')
            ->first();

        $anggota->update([
            'status' => '0'
        ]);

        // print

        $kode = $request->kode;

        return redirect('/printinvoice?kode=' . $kode . '&role=petugas');
    }

    // pengembalian
    public function pengembalianbuku(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'kode' => 'required',
            'tgl_kembali' => 'required',
            'id_petugas' => 'required',
            'id_anggota' => 'required'
        ]);

        if ($validator->fails()) {
            return redirect('/petpengembalian')
                ->withErrors($validator);
        }

        // denda

        $tglkembali = Carbon::parse($request->tgl_kembali);
        $tglhariini = Carbon::parse(date('Y-m-d'));

        $denda_telat = 2000;

        if ($tglhariini->isAfter($tglkembali)) {
            $jarak = $tglkembali->diff($tglhariini);
            $jarakhari = $jarak->d;
        } else {
            $jarakhari = 0;
        }

        $dendatelat = $denda_telat * $jarakhari;

        if ($dendatelat > 100000) {
            $dendatelat = 100000;
        }

        // query pengembalian

        $data = pinjam::select('*')
            ->where('kode', '=', $request->kode)
            ->first();

        $data->update([
            'status' => 'dikembalikan'
        ]);

        $detail = detailpinjam::select('*')
            ->where('kode', $request->kode)
            ->get();

        $countbuku = count($detail);

        pengembalian::create([
            'kode' => $request->kode,
            'tgl_kembali' => $request->tgl_kembali,
            'denda' => $dendatelat,
            'qty' => $countbuku,
            'keterangan' => 'Dikembalikan',
            'id_petugas' => $request->id_petugas
        ]);

        foreach ($detail as $details) {

            $datadetail = buku::select('*')
                ->where('id', '=', $details->id_buku)
                ->where('status', '=', '0')
                ->first();

            $datadetail->update([
                'status' => '1'
            ]);
        }

        $anggota = anggota::select('*')
            ->where('nisn', '=', $request->id_anggota)
            ->first();

        $anggota->update([
            'status' => '1'
        ]);

        return redirect('/petpengembalian')->with('notifubah', 'Berhasil Menambahkan');
    }




    // print

    // print invoice

    public function printinvoice(Request $request)
    {

        // validasi request

        $kode = $request->kode;
        $role = $request->role;
        $where = $request->where;

        // mengalihkan halaman after print

        if ($role == 'petugas') {
            if ($where == 'petpeminjaman') {
                $go = "window.location='/petdatapeminjaman'";
            } else {
                $go = "window.location='/petdatapengembalian'";
            }
        }

        // query ke halaman invoice

        $datapinjam = pinjam::select('pinjams.id', 'pinjams.kode', 'pinjams.id_anggota', 'pinjams.guru', 'pinjams.tgl_pinjam', 'pinjams.tgl_kembali', 'pinjams.id_petugas', 'pinjams.status', 'anggotas.name AS anggota', 'anggotas.nisn', 'petugas.name AS petugas')
            ->join('anggotas', 'pinjams.id_anggota', '=', 'anggotas.nisn')
            ->join('petugas', 'pinjams.id_petugas', '=', 'petugas.id')
            ->where('pinjams.kode', $kode)
            ->first();

        $datadetailtunggal = detailpinjam::select('detailpinjams.isbn', 'bukus.judul', DB::raw('COUNT(detailpinjams.qty) AS qty'))
            ->join('bukus', 'detailpinjams.id_buku', '=', 'bukus.id')
            ->groupBy('isbn', 'qty', 'judul')
            ->where('detailpinjams.kode', $kode);

        return view('laporan.invoice-peminjaman', [
            'kode' => $kode,
            'gowhere' => $go,
            'tgl_pinjam' => $datapinjam->tgl_pinjam,
            'tgl_kembali' => $datapinjam->tgl_kembali,
            'petugas' => $datapinjam->petugas,
            'anggota' => $datapinjam->anggota,
            'anggotanisn' => $datapinjam->nisn,
            'guru' => $datapinjam->guru,
            'buku' => $datadetailtunggal->get()

        ]);
    }

    // print anggota
    public function printanggota(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'anggota' => 'required'
        ]);

        if ($validator->fails()) {

            $data = anggota::select('*');
        } else {
            $ids = $request->anggota;

            $data = anggota::select('*')
                ->whereIn('id', $ids);
        }


        return view('laporan.kartuanggota', [
            'anggotas' => $data->get(),
            'role' => $request->role
        ]);
    }
}
