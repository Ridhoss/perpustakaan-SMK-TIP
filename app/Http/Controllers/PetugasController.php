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
        $subquery = pinjam::select('id_buku', DB::raw('COUNT(id_buku) AS jumlah_sebelum_group'))
            ->groupBy('id_buku');

        $bukularis = pinjam::select('pinjams.id_buku', 'bukus.judul', DB::raw('COALESCE(sub.jumlah_sebelum_group, 0) AS jumlah'))
            ->leftJoinSub($subquery, 'sub', function ($join) {
                $join->on('pinjams.id_buku', '=', 'sub.id_buku');
            })
            ->join('bukus', 'pinjams.id_buku', '=', 'bukus.isbn')
            ->groupBy('id_buku', 'judul', 'jumlah_sebelum_group')
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
            $datapeminjaman = pinjam::select('pinjams.id', 'pinjams.kode', 'bukus.isbn', 'bukus.judul', 'anggotas.name AS anggota', 'anggotas.id AS agtid', 'anggotas.nisn', 'detailpinjams.tgl_pinjam', 'detailpinjams.tgl_kembali', 'petugas.id AS petid', 'petugas.name AS petugas', 'detailpinjams.qty AS qty', 'pinjams.status')
                ->join('bukus', 'bukus.isbn', '=', 'pinjams.id_buku')
                ->join('anggotas', 'anggotas.nisn', '=', 'pinjams.id_anggota')
                ->join('detailpinjams', 'detailpinjams.kode', '=', 'pinjams.kode')
                ->join('petugas', 'petugas.id', '=', 'detailpinjams.id_petugas')
                ->groupBy('id', 'pinjams.kode', 'judul', 'isbn', 'id_petugas', 'anggota', 'agtid', 'nisn', 'tgl_pinjam', 'petid', 'tgl_kembali', 'petugas', 'qty', 'pinjams.status')
                ->where('pinjams.kode', 'LIKE', '%' . $request->cari . '%')
                ->OrWhere('anggotas.name', 'LIKE', '%' . $request->cari . '%')
                ->OrWhere('anggotas.nisn', 'LIKE', '%' . $request->cari . '%')
                ->OrWhere('bukus.judul', 'LIKE', '%' . $request->cari . '%');
        } else {
            $datapeminjaman = pinjam::select('pinjams.id', 'pinjams.kode', 'bukus.isbn', 'bukus.judul', 'anggotas.name AS anggota', 'anggotas.id AS agtid', 'anggotas.nisn', 'detailpinjams.tgl_pinjam', 'detailpinjams.tgl_kembali', 'petugas.id AS petid', 'petugas.name AS petugas', 'detailpinjams.qty AS qty', 'pinjams.status')
                ->join('bukus', 'bukus.isbn', '=', 'pinjams.id_buku')
                ->join('anggotas', 'anggotas.nisn', '=', 'pinjams.id_anggota')
                ->join('detailpinjams', 'detailpinjams.kode', '=', 'pinjams.kode')
                ->join('petugas', 'petugas.id', '=', 'detailpinjams.id_petugas')
                ->groupBy('id', 'pinjams.kode', 'judul', 'isbn', 'anggota', 'agtid', 'nisn', 'tgl_pinjam', 'tgl_kembali', 'petid', 'petugas', 'qty', 'pinjams.status');
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

        $datapeminjaman = pinjam::select('pinjams.id', 'pinjams.kode', 'bukus.isbn', 'bukus.judul', 'anggotas.name AS anggota', 'anggotas.id AS agtid', 'anggotas.nisn', 'detailpinjams.tgl_pinjam', 'detailpinjams.tgl_kembali', 'petugas.id AS petid', 'petugas.name AS petugas', 'detailpinjams.qty AS qty', 'pinjams.status')
            ->join('bukus', 'bukus.isbn', '=', 'pinjams.id_buku')
            ->join('anggotas', 'anggotas.nisn', '=', 'pinjams.id_anggota')
            ->join('detailpinjams', 'detailpinjams.kode', '=', 'pinjams.kode')
            ->join('petugas', 'petugas.id', '=', 'detailpinjams.id_petugas')
            ->where('pinjams.status', 'dipinjam')
            ->orWhere('pinjams.status', 'dihapus')
            ->groupBy('id', 'pinjams.kode', 'judul', 'isbn', 'anggota', 'agtid', 'nisn', 'tgl_pinjam', 'tgl_kembali', 'petid', 'petugas', 'qty', 'pinjams.status');

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


        return view('petugas.page.petdatapengembalian', [
            'lastquery' => $request->cari,
            'lastquerymodal' => $request->searchpeminjaman,
            'datapengembalian' => $datapengembalian->get(),
            'datapeminjaman' => $datapeminjaman->get(),
            'user' => Auth::user()
        ]);
    }






    // crud


    // transaksi peminjaman
    public function addpeminjaman(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id_petugas' => 'required',
            'kode' => 'required',
            'isbn' => 'required',
            'id_anggota' => 'required',
            'peminjaman' => 'required',
            'pengembalian' => 'required',
            'qty' => 'required',
            'old_qty' => 'required'
        ]);

        if ($request->role == 'admin') {
            if ($validator->fails()) {
                return redirect('/peminjaman')
                    ->withErrors($validator);
            }
        } else {
            if ($validator->fails()) {
                return redirect('/petpeminjaman')
                    ->withErrors($validator);
            }
        }


        if ($request->kondisi == "peminjaman") {
            // $qtybaru = $request->old_qty - $request->qty;

            $buku = buku::select('*')
                ->where('isbn', '=', $request->isbn)
                ->where('status', '=', '1')
                ->take($request->qty);

            $anggota = anggota::select('*')
                ->where('nisn', '=', $request->id_anggota)
                ->where('status', '=', '1')
                ->first();

            pinjam::create([
                'kode' => $request->kode,
                'id_buku' => $request->isbn,
                'id_anggota' => $request->id_anggota,
                'status' => 'dipinjam'
            ]);

            detailpinjam::create([
                'kode' => $request->kode,
                'tgl_pinjam' => $request->peminjaman,
                'tgl_kembali' => $request->pengembalian,
                'id_petugas' => $request->id_petugas,
                'qty' => $request->qty
            ]);

            $buku->update([
                'status' => '0'
            ]);

            $anggota->update([
                'status' => '0'
            ]);
        }

        // query ke halaman invoice

        $petugas = petugas::select('name')
            ->where('id', '=', $request->id_petugas);

        $anggota = anggota::select('nisn', 'name')
            ->where('nisn', '=', $request->id_anggota);

        $buku = buku::select('judul')
            ->where('isbn', '=', $request->isbn)
            ->take(1);

        return view('laporan.invoice-peminjaman', [
            'kode' => $request->kode,
            'role' => $request->role,
            'tgl_pinjam' => $request->peminjaman,
            'tgl_kembali' => $request->pengembalian,
            'petugas' => $petugas->get(),
            'anggota' => $anggota->get(),
            'buku' => $buku->get(),
            'qty' => $request->qty


        ]);

        // return redirect('/petdatapeminjaman')->with('notifpinjam', 'Berhasil Menambahkan');
    }

    // print

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
