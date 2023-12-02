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
        <h1 class="h3 mb-0 text-gray-800 font-primary">Peminjaman Buku</h1>

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
    <form action="/petpeminjaman" method="get">
        <div class="d-flex justify-content-end mb-5">
            <input type="search" name="cari" class="cari form-control me-3" placeholder="Cari"
                value="{{ $lastquery }}" id="cari">
            {{-- <button type="submit" class="btn btn-outline-secondary me-3">Cari</button> --}}
        </div>
    </form>

    <div class="row ms-2 d-flex align-items-center justify-content-center">


        {{-- start foreach buku --}}
        @foreach ($datbuku as $buku)
            <div class="card shadow p-2 me-4 mb-4 {{ $buku->jumlah == 0 ? 'disabled-card' : '' }}" style="width: 13rem;">
                <img src="{{ Storage::url('public/buku/' . $buku->photo) }}" class="custom-img" id="img">
                <div class="card-body d-flex justify-content-between">
                    <div class="ket">
                        <h5 class="card-title">{{ $buku->judul }}</h5>
                        <h6 class="mt-1">{{ $buku->jumlah }} Pcs</h6>
                    </div>
                    <div class="bookmark mt-3">
                        <form action="petpeminjamandetail" method="GET">
                            @csrf
                            <input type="hidden" name="buku" value="{{ $buku->isbn }}">
                            <button type="submit" class="pre-book py-2 px-3 rounded border-0"><i
                                    class="fa-solid fa-bookmark"></i></i></a>
                        </form>
                    </div>
                </div>
            </div>
        @endforeach

        {{-- akhir foreach buku --}}

        <script>
            window.onload = function() {
                // Get the input element by its ID
                var inputElement = document.getElementById("cari");

                // Set focus to the input element when the page is loaded
                inputElement.focus();
            };
        </script>

    </div>
@endsection
