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

        .pre-book {
            background-color: #ddc190;
            color: #002939;
        }

        .pre-book:hover {
            background-color: #002939;
            color: #ddc190;
            transition: 0.4s
        }

        .col-12 {
            background-color: #ddc190;
        }

        .pengarang,
        .title {
            color: #002939;
        }

        .container-left {
            background-color: #fff;
            height: 600px;
        }

        .container-right {
            width: 500px !important;
            background-color: #fff;
            height: 560px;
        }

        .dtl-pmj {
            font-size: 2rem;
            font-weight: 700;
        }

        .button {
            margin-top: 30px;
        }

        @media screen and (min-width: 768px) {
            .cari {
                width: 200px !important;
            }

            .container-left {
                height: 560px;
            }

            .button {
                margin-top: 60px;
            }
        }
    </style>
@endsection

@section('content')
    <!-- Page Heading -->
    <div class="d-flex position-relative align-items-center justify-content-between p-4">
        <h1 class="h3 mb-0 text-gray-800 font-primary">Peminjaman Buku Siswa</h1>

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

    <div class="col-12 mt-4 rounded-3 p-4 d-block d-lg-flex shadow">

        <div class="col-lg-8">
            <div class="container-left shadow mt-4 mt-lg-0 p-4 rounded">
                <h1 class="dtl-pmj text-secondary">Buku :</h1>
                <div class="right-body p-4">
                    <form action="/detailpinjambukusiswa" method="POST">
                        <table id="myTable" class="display">
                            <thead>
                                <tr>
                                    <th>Foto</th>
                                    <th>ISBN</th>
                                    <th>Judul</th>
                                    <th>Pengarang</th>
                                    <th>Stok</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $no = 1;
                                @endphp
                                @foreach ($datbuku as $buku)
                                    <tr>
                                        <td>
                                            <img src="{{ Storage::url('public/buku/' . $buku->photo) }}" width="50"
                                                class="rounded">
                                        </td>
                                        <td>{{ $buku->isbn }}</td>
                                        <td>{{ $buku->judul }}</td>
                                        <td>{{ $buku->pengarang }}</td>
                                        <td>{{ $buku->jumlah }}</td>
                                        @if ($buku->jumlah != 0)
                                            <td>
                                                <input type="checkbox" class="btn-check" id="{{ $buku->isbn }}"
                                                    autocomplete="off" name="buku[]" value="{{ $buku->isbn }}">
                                                <label class="btn btn-outline-success"
                                                    for="{{ $buku->isbn }}">Pilih</label>
                                            </td>
                                        @else
                                            <td>
                                                <button class="btn btn-danger btn-sm" disabled>Stok Habis</button>
                                            </td>
                                        @endif
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                </div>
            </div>
        </div>
        <div class="col-lg-4 d-flex justify-content-start align-items-start">
            <div class="container-right shadow mt-4 mt-lg-0 p-4 rounded">
                <h1 class="dtl-pmj text-secondary mb-3">Data Peminjaman :</h1>
                <div class="right-body p-4">
                    @php
                        $today = date('Y-m-d');
                        $besok = date('Y-m-d', strtotime('+1 day'));
                        $todays = date('Ymd');

                    @endphp
                    @csrf
                    <input type="hidden" name="id_petugas" value="{{ $user->id }}">
                    {{-- <input type="hidden" name="old_qty" value="{{ $oldqty }}"> --}}
                    {{-- <input type="hidden" name="isbn" value="{{ $idbuku }}"> --}}
                    <input type="hidden" name="kode" value="{{ $custom }}">
                    {{-- <input type="hidden" name="kondisi" value="peminjaman"> --}}
                    {{-- <input type="hidden" name="role" value="petugas"> --}}
                    <div class="row mb-2 px-2">
                        <label class="mb-2 fw-medium">Kode Peminjaman</label>
                        <input type="text" class="form-control text-center" placeholder="Kode" name=""
                            value="{{ $custom }}" required disabled>
                    </div>
                    <div class="row mb-2">
                        <label class="mb-2 fw-medium ms-2">Anggota Peminjam</label>
                        <select class="form-select" id="select_box" name="id_anggota">
                            <option value="">Pilih Anggota</option>
                            @foreach ($datanggota as $anggota)
                                <option value="{{ $anggota->nisn }}">{{ $anggota->nisn }} - {{ $anggota->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="row mb-2 px-2">
                        <label class="mb-2 fw-medium">Tanggal Peminjaman</label>
                        <input type="date" class="form-control text-center" name="peminjaman"
                            value="{{ $today }}" min="{{ $today }}" required readonly>
                    </div>
                    <div class="row mb-2 px-2">
                        <label class="mb-2 fw-medium">Tanggal Pengembalian</label>
                        <input type="date" class="form-control text-center" name="pengembalian"
                            min="{{ $besok }}" required>
                    </div>
                    <div class="d-flex justify-content-end button">
                        <button type="submit" class="btn btn-success">Pinjam</button>
                    </div>
                    </form>
                </div>
            </div>
        </div>
    </div>


    <script>
        window.onload = function() {
            // Get the input element by its ID
            // var inputElement = document.getElementById("cari");

            // Set focus to the input element when the page is loaded
            // inputElement.focus();

            var aktif = document.getElementById("pin-tung");

            aktif.classList.add('aktif');

        };
    </script>

    <script>
        $(document).ready(function() {
            new DataTable("#myTable");
        });

        $('#myTable').dataTable({
            "scrollY": "300px",
            "scrollCollapse": true,
            "paging": false
        });
    </script>
@endsection
