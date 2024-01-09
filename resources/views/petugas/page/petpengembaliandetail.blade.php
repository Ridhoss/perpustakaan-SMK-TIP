@extends('petugas.layout.petlayout')

@section('css')
    <style>
        .card-title {
            font-size: 1.2rem;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 100px;
        }

        .cari {
            width: 150px !important;
        }

        .pre-book {
            background-color: #ddc190;
            color: #002939;
        }

        .pre-book:hover {
            background-color: #002939;
            color: #ddc190;
            transition: 0.4s
        }

        .disabled-card {
            pointer-events: none;
            opacity: 0.7;
        }

        .custom-img {
            max-width: 190px !important;
            max-height: 278.56px !important;
        }

        .col-12 {
            background-color: #ddc190;
        }

        .container {
            background-color: #fff;
        }

        .nopem {
            font-size: 30px;
        }

        @media screen and (min-width: 768px) {
            .cari {
                width: 200px !important;
            }
        }
    </style>
@endsection

@section('content')
    <!-- Page Heading -->
    <div class="d-flex position-relative align-items-center justify-content-between p-4">
        <h1 class="h3 mb-0 text-gray-800 font-primary">Detail Informasi Pengembalian Buku</h1>

        {{-- alert --}}
        @error('id_petugas')
            <div class="alert alert-danger alert-dismissible fade show position-absolute top-0 end-0" role="alert">
                <strong>Data Failed!</strong> {{ $message }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @enderror
        @error('kode')
            <div class="alert alert-danger alert-dismissible fade show position-absolute top-0 end-0" role="alert">
                <strong>Data Failed!</strong> {{ $message }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @enderror
        @error('id_buku')
            <div class="alert alert-danger alert-dismissible fade show position-absolute top-0 end-0" role="alert">
                <strong>Data Failed!</strong> {{ $message }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @enderror
        @error('id_anggota')
            <div class="alert alert-danger alert-dismissible fade show position-absolute top-0 end-0" role="alert">
                <strong>Data Failed!</strong> {{ $message }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @enderror
        @error('peminjaman')
            <div class="alert alert-danger alert-dismissible fade show position-absolute top-0 end-0" role="alert">
                <strong>Data Failed!</strong> {{ $message }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @enderror
        @error('pengembalian')
            <div class="alert alert-danger alert-dismissible fade show position-absolute top-0 end-0" role="alert">
                <strong>Data Failed!</strong> {{ $message }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @enderror
        @error('qty')
            <div class="alert alert-danger alert-dismissible fade show position-absolute top-0 end-0" role="alert">
                <strong>Data Failed!</strong> {{ $message }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @enderror
        @error('old_qty')
            <div class="alert alert-danger alert-dismissible fade show position-absolute top-0 end-0" role="alert">
                <strong>Data Failed!</strong> {{ $message }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @enderror

    </div>

    {{-- cari --}}
    {{-- <form action="/petpeminjamansiswa" method="get">
        <div class="d-flex justify-content-end mb-5">
            <input type="search" name="cari" class="cari form-control me-3" placeholder="Cari"
                value="{{ $lastquery }}" id="cari">
            <button type="submit" class="btn btn-outline-secondary me-3">Cari</button>
        </div>
    </form> --}}

    <form action="/pengembalianbuku" method="POST">
        @csrf
        <div class="col-12 mt-4 rounded-3 p-4 d-block d-lg-flex flex-column shadow">

            <div class="container shadow mt-4 mt-lg-0 p-4 rounded mb-4">

                @php
                    $now = date('Y-m-d');
                @endphp

                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h1 class="nopem text-secondary fw-bold">Nomor Peminjaman : {{ $kode }}</h1>
                    <h4 class="nopem text-secondary fs-5">Tanggal Pengembalian :
                        {{ \Carbon\Carbon::createFromFormat('Y-m-d', $now)->format('d F Y') }}</h4>
                </div>

                <input type="hidden" name="kode" value="{{ $kode }}">
                <input type="hidden" name="tgl_kembali" value="{{ $now }}">
                <input type="hidden" name="id_petugas" value="{{ $user->id }}">
                <input type="hidden" name="id_anggota" value="{{ $peminjaman->nisn }}">

                <div class="row mb-2 px-2">
                    <label class="mb-2 fw-medium">Nama Peminjam</label>
                    <input type="text" class="form-control text-center" name="anggota"
                        placeholder="{{ $peminjaman->nisn }} - {{ $peminjaman->anggota }}" readonly>
                </div>
                <div class="row mb-2 px-2">
                    <label class="mb-2 fw-medium">Tanggal Peminjaman</label>
                    <input type="text" class="form-control text-center" name="pinjam"
                        placeholder="{{ \Carbon\Carbon::createFromFormat('Y-m-d', $peminjaman->tgl_pinjam)->format('d F Y') }}"
                        readonly>
                </div>
                <div class="row mb-2 px-2">
                    <label class="mb-2 fw-medium">Tanggal Pengembalian</label>
                    <input type="text" class="form-control text-center" name="kembali"
                        placeholder="{{ \Carbon\Carbon::createFromFormat('Y-m-d', $peminjaman->tgl_kembali)->format('d F Y') }}"
                        readonly>
                </div>
                <div class="row mb-2 px-2">
                    <label class="mb-2 fw-medium">Petugas</label>
                    <input type="text" class="form-control text-center" name="petugas"
                        placeholder="{{ $peminjaman->petugas }}" readonly>
                </div>

            </div>

            <div class="container shadow mt-4 mt-lg-0 p-4 rounded">

                <h1 class="nopem text-secondary mb-4">Buku :</h1>

                {{-- buku yang akan di pinjam --}}

                @foreach ($bukus as $buku)
                    <div class="card mb-3" style="max-width: 500px;">
                        <div class="row g-0">
                            <div class="col-md-4">
                                <img src="{{ Storage::url('public/buku/' . $buku->photo) }}"
                                    class="img-fluid rounded-start">
                            </div>
                            <div class="col-md-8">
                                <div class="card-body">
                                    <h1 class="card-text fs-3 text-uppercase fw-bold text-secondary w-100">
                                        {{ Str::limit($buku->judul, 12, '..') }}</h1>
                                    <p class="card-text text-secondary mt-2 border-top pt-2">
                                        <span class="fw-bold">ISBN :</span> {{ $buku->isbn }}<br>
                                        <span class="fw-bold">Pengarang :</span> {{ $buku->pengarang }} <br>
                                        <span class="fw-bold">Tahun Terbit :</span> {{ $buku->tahun_terbit }}
                                    </p>
                                    <hr>
                                    <p class="card-text"><small class="text-body-secondary fw-bold">Jumlah Buku : {{ $buku->qty }}</small></p>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach

                {{-- akhir buku --}}



                <div class="d-flex justify-content-end mt-4">
                    <a href="/petpengembalian" type="button" class="btn btn-secondary me-2">Batal</a>
                    <button type="submit" class="btn btn-success">Kembalikan</button>
                </div>

            </div>
        </div>
    </form>

    <script>
        window.onload = function() {

            var aktif = document.getElementById("pengem");

            aktif.classList.add('aktif');

        };
    </script>
@endsection
