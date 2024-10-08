<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\PetugasController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/


// Route Test
// Route::get('/layout', function () {
//     return view('petugas.layout.petlayout');
// });
Route::get('/test', function () {
    return view('laporan.laporan-buku');
});

// Halaman landing
Route::get('/', [AdminController::class, 'landing'])->middleware('guest');

// Halaman login 
Route::get('/login', function () {

    if (Auth::guard('admin')->check()) {
        return redirect('/dashboard');
    } else if (Auth::guard('petugas')->check()) {
        return redirect('/petdashboard');
    } else {
        return view('login');
    }
})->name('login')->middleware('guest');

// Halaman register 
Route::get('/register', function () {
    if (Auth::guard('admin')->check()) {
        return redirect('/dashboard');
    } else if (Auth::guard('petugas')->check()) {
        return redirect('/petdashboard');
    } else {
        return view('register');
    }
})->middleware('guest');


// login & register

// AksiRegister
Route::post('/regadmin', [AdminController::class, 'RegisterAdmin']);
// AksiLogin
Route::post('/loginadmin', [AdminController::class, 'LoginAdmin']);
// AksiLogout
Route::post('/logout', [AdminController::class, 'Logout']);


// route


// admin

// ke dashboard
Route::get('/dashboard', [AdminController::class, 'dashboard'])->middleware('auth:admin');
// ke data buku
Route::get('/buku', [AdminController::class, 'databuku'])->middleware('auth:admin');
// ke data group buku
Route::get('/bukus', [AdminController::class, 'databukus'])->middleware('auth:admin');
// ke data kategori
Route::get('/kategori', [AdminController::class, 'datakat'])->middleware('auth:admin');
// ke data asal buku    
Route::get('/asal', [AdminController::class, 'dataasal'])->middleware('auth:admin');
// ke data bahasa buku
Route::get('/bahasa', [AdminController::class, 'databahasa'])->middleware('auth:admin');
// ke data anggota
Route::get('/anggota', [AdminController::class, 'dataanggota'])->middleware('auth:admin');
// ke data admin
Route::get('/admin', [AdminController::class, 'dataadmin'])->middleware('auth:admin');
// ke data petugas
Route::get('/petugas', [AdminController::class, 'datapetugas'])->middleware('auth:admin');
// ke data peminjaman
Route::get('/peminjaman', [AdminController::class, 'datapeminjaman'])->middleware('auth:admin');
// ke data pengembalian
Route::get('/pengembalian', [AdminController::class, 'datapengembalian'])->middleware('auth:admin');
// ke data rak
Route::get('/rak', [AdminController::class, 'datarak'])->middleware('auth:admin');


// petugas

// ke dashboard
Route::get('/petdashboard', [PetugasController::class, 'dashboard'])->middleware('auth:petugas');
// ke halaman peminjaman jamak
Route::get('/petpeminjaman', [PetugasController::class, 'peminjaman'])->middleware('auth:petugas');
// ke halaman peminjaman detail
Route::get('/petpeminjamandetail', [PetugasController::class, 'peminjamandetail'])->middleware('auth:petugas');
// ke data peminjaman
Route::get('/petdatapeminjaman', [PetugasController::class, 'datapeminjaman'])->middleware('auth:petugas');
// ke data pengembalian
Route::get('/petdatapengembalian', [PetugasController::class, 'datapengembalian'])->middleware('auth:petugas');
// ke data pet buku
Route::get('/petdatabuku', [PetugasController::class, 'databuku'])->middleware('auth:petugas');
// ke data pet anggota
Route::get('/petdataanggota', [PetugasController::class, 'dataanggota'])->middleware('auth:petugas');
// ke halaman peminjaman tunggal
Route::get('/petpeminjamansiswa', [PetugasController::class, 'peminjamantunggal'])->middleware('auth:petugas');
// ke detail pinjam buku siswa
Route::post('/detailpinjambukusiswa', [PetugasController::class, 'detpeminjamansis'])->middleware('auth:petugas');
// ke halaman pengembalian
Route::get('/petpengembalian', [PetugasController::class, 'pengembalian'])->middleware('auth:petugas');
// ke halaman detail pengembalian
Route::post('/petdetailpengembalian', [PetugasController::class, 'detailpengembalian'])->middleware('auth:petugas');



// crud 

// kategori

// tambah kategori
Route::post('/addkategori', [AdminController::class, 'addkategori']);
// edit kategori
Route::post('/updatekategori', [AdminController::class, 'upkategori']);
// hapuskategori
Route::post('/hapuskategori', [AdminController::class, 'delkategori']);

// asal buku

// tambah asal
Route::post('/addasal', [AdminController::class, 'addasal']);
// edit asal
Route::post('/updateasal', [AdminController::class, 'upasal']);
// hapusasal
Route::post('/hapusasal', [AdminController::class, 'delasal']);

// bahasa

// tambah bahasa
Route::post('/addbahasa', [AdminController::class, 'addbahasa']);
// edit bahasa
Route::post('/updatebahasa', [AdminController::class, 'upbahasa']);
// hapusbahasa
Route::post('/hapusbahasa', [AdminController::class, 'delbahasa']);

// anggota

// tambah anggota
Route::post('/addanggota', [AdminController::class, 'addanggota']);
// edit anggota
Route::post('/updateanggota', [AdminController::class, 'upanggota']);
// hapusanggota
Route::post('/hapusanggota', [AdminController::class, 'delanggota']);

// admin

// tambah admin
Route::post('/addadmin', [AdminController::class, 'addadmin']);
// edit admin
Route::post('/updateadmin', [AdminController::class, 'upadmin']);
// edit password admin
Route::post('/updatepassadmin', [AdminController::class, 'uppassadmin']);
// hapus admin
Route::post('/hapusadmin', [AdminController::class, 'deladmin']);

// petugas

// tambah petugas
Route::post('/addpetugas', [AdminController::class, 'addpetugas']);
// edit petugas
Route::post('/updatepetugas', [AdminController::class, 'uppetugas']);
// edit password petugas
Route::post('/updatepasspetugas', [AdminController::class, 'uppasspetugas']);
// hapus petugas
Route::post('/hapuspetugas', [AdminController::class, 'delpetugas']);

// detail buku

// tambah buku
Route::post('/addbuku', [AdminController::class, 'addbuku']);
// edit buku
Route::post('/updatebuku', [AdminController::class, 'upbuku']);
// hapus buku
Route::post('/hapusbuku', [AdminController::class, 'delbuku']);

// buku

// edit buku
Route::post('/updatebukus', [AdminController::class, 'upbukus']);
// hapus buku
Route::post('/hapusbukus', [AdminController::class, 'delbukus']);

// rak

// tambah rak buku
Route::post('/addrak', [AdminController::class, 'addrak']);
// edit rak buku
Route::post('/uprak', [AdminController::class, 'uprak']);
// hapus rak buku
Route::post('/delrak', [AdminController::class, 'delrak']);





// PEMINJAMAN & PENGEMBALIAN

// aksi pinjam buku
Route::post('/pinjambuku', [PetugasController::class, 'addpeminjaman']);
// // aksi pinjam buku siswa
Route::post('/pinjambukusiswa', [PetugasController::class, 'addpeminjamansiswa']);
// ubah status (admin)
Route::post('/pengembalianbuku', [PetugasController::class, 'pengembalianbuku']);
// hapus peminjaman (admin)
Route::post('/hapuspeminjaman', [AdminController::class, 'delpeminjaman']);
// hapus pengembalian (admin)
Route::post('/hapuspengembalian', [AdminController::class, 'delpengembalian']);


// print

// print kartu anggota
Route::get('/printinvoice', [PetugasController::class, 'printinvoice']);

// print kartu anggota
Route::post('/printanggota', [PetugasController::class, 'printanggota']);

// print laporan peminjaman
Route::post('/printlaporan', [AdminController::class, 'printlaporan']);

// print laporan buku
Route::post('/printlaporanbuku', [AdminController::class, 'printlaporanbuku']);
