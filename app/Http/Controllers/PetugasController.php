<?php

namespace App\Http\Controllers;

use App\Models\anggota;
use App\Models\buku;
use App\Models\detailpinjam;
use App\Models\kategori;
use App\Models\penerbit;
use App\Models\pengarang;
use App\Models\pengembalian;
use App\Models\pinjam;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PetugasController extends Controller
{


    // Route

    // ke halaman dashboard
    public function dashboard()
    {
        return view('petugas.page.petdashboard', [
            'user' => Auth::user()
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
                ->where('status', '1')
                ->groupBy('bukus.tanggal', 'bukus.isbn', 'bukus.pengarang', 'bukus.judul', 'bukus.thn_inv', 'bukus.asl_id', 'bukus.ktg_id', 'bukus.bhs_id', 'asal', 'kategori', 'bahasa', 'bukus.tahun_terbit', 'bukus.sinopsis', 'bukus.photo', 'bukus.ket');
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
            'datanggota' => anggota::all()
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
            $databuku = buku::select('bukus.id', 'bukus.isbn', 'bukus.title', 'kategoris.name AS kategori', 'bukus.kategori_id', 'bukus.penerbit_id', 'bukus.pengarang_id', 'penerbits.name AS penerbit', 'pengarangs.name AS pengarang', 'bukus.jumlah_halaman', 'bukus.stok', 'bukus.tahun_terbit', 'bukus.sinopsis', 'bukus.photo')
                ->join('kategoris', 'kategoris.id', '=', 'bukus.kategori_id')
                ->join('penerbits', 'penerbits.id', '=', 'bukus.penerbit_id')
                ->join('pengarangs', 'pengarangs.id', '=', 'bukus.pengarang_id')
                ->where('title', 'LIKE', '%' . $request->cari . '%')
                ->OrWhere('isbn', 'LIKE', '%' . $request->cari . '%')
                ->OrWhere('kategoris.name', 'LIKE', '%' . $request->cari . '%')
                ->OrWhere('penerbits.name', 'LIKE', '%' . $request->cari . '%')
                ->OrWhere('pengarangs.name', 'LIKE', '%' . $request->cari . '%');
        } else {
            $databuku = buku::select('bukus.id', 'bukus.isbn', 'bukus.title', 'kategoris.name AS kategori', 'bukus.kategori_id', 'bukus.penerbit_id', 'bukus.pengarang_id', 'penerbits.name AS penerbit', 'pengarangs.name AS pengarang', 'bukus.jumlah_halaman', 'bukus.stok', 'bukus.tahun_terbit', 'bukus.sinopsis', 'bukus.photo')
                ->join('kategoris', 'kategoris.id', '=', 'bukus.kategori_id')
                ->join('penerbits', 'penerbits.id', '=', 'bukus.penerbit_id')
                ->join('pengarangs', 'pengarangs.id', '=', 'bukus.pengarang_id');
        }

        $datakat = kategori::all();

        return view('petugas.page.petdatabuku', [
            'databuku' => $databuku->get(),
            'lastquery' => $request->cari,
            'datakat' => $datakat,
            'user' => Auth::user()
        ]);
    }

    // ke halaman datapeminjaman
    public function datapeminjaman(Request $request)
    {

        if ($request->has('cari')) {
            $datapeminjaman = pinjam::select('pinjams.id','pinjams.kode', 'bukus.isbn', 'bukus.judul', 'anggotas.name AS anggota', 'anggotas.nisn', 'detailpinjams.tgl_pinjam', 'detailpinjams.tgl_kembali', 'petugas.name AS petugas', 'detailpinjams.qty', 'pinjams.status')
                ->join('bukus', 'bukus.isbn', '=', 'pinjams.id_buku')
                ->join('anggotas', 'anggotas.id', '=', 'pinjams.id_anggota')
                ->join('detailpinjams', 'detailpinjams.kode', '=', 'pinjams.kode')
                ->join('petugas', 'petugas.id', '=', 'detailpinjams.id_petugas')
                ->groupBy('id','pinjams.kode', 'judul', 'isbn', 'anggota', 'nisn', 'tgl_pinjam', 'tgl_kembali', 'petugas', 'qty', 'pinjams.status')
                ->where('pinjams.kode', 'LIKE', '%' . $request->cari . '%')
                ->OrWhere('anggotas.name', 'LIKE', '%' . $request->cari . '%')
                ->OrWhere('anggotas.nisn', 'LIKE', '%' . $request->cari . '%')
                ->OrWhere('bukus.judul', 'LIKE', '%' . $request->cari . '%');
        } else {
            $datapeminjaman = pinjam::select('pinjams.id','pinjams.kode', 'bukus.isbn', 'bukus.judul', 'anggotas.name AS anggota', 'detailpinjams.tgl_pinjam', 'detailpinjams.tgl_kembali', 'petugas.name AS petugas', 'detailpinjams.qty', 'pinjams.status')
                ->join('bukus', 'bukus.isbn', '=', 'pinjams.id_buku')
                ->join('anggotas', 'anggotas.id', '=', 'pinjams.id_anggota')
                ->join('detailpinjams', 'detailpinjams.kode', '=', 'pinjams.kode')
                ->join('petugas', 'petugas.id', '=', 'detailpinjams.id_petugas')
                ->groupBy('id','pinjams.kode', 'judul', 'isbn', 'anggota', 'tgl_pinjam', 'tgl_kembali', 'petugas', 'qty', 'pinjams.status');
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
            'datapengembalian' => $datapengembalian->get(),
            'user' => Auth::user()
        ]);
    }






    // crud


    // transaksi peminjaman
    public function addpeminjaman(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id_petugas' => 'required',
            'kode' => 'required|unique:pinjams',
            'isbn' => 'required',
            'id_anggota' => 'required',
            'peminjaman' => 'required',
            'pengembalian' => 'required',
            'qty' => 'required',
            'old_qty' => 'required'
        ]);

        if ($validator->fails()) {
            return redirect('/petpeminjaman')
                ->withErrors($validator);
        }

        // $qtybaru = $request->old_qty - $request->qty;

        $buku = buku::select('*')
            ->where('isbn', '=', $request->isbn)
            ->take($request->qty);

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

        return redirect('/petpeminjaman')->with('notifpinjam', 'Berhasil Menambahkan');
    }
}
