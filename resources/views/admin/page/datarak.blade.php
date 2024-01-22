@extends('admin.layout.layout')

@section('css')
    <style>
        .cari {
            width: 150px !important;
        }

        @media screen and (min-width: 768px) {
            .cari {
                width: 200px !important;
            }
        }
    </style>
@endsection

@section('content')
    {{-- header --}}
    <div class="d-flex position-relative align-items-center justify-content-between p-4">
        <h1 class="h3 mb-0 text-gray-800 font-primary">Data Rak Buku</h1>

        {{-- alerts --}}
        @if (session()->has('notifadd'))
            <div class="alert alert-success alert-dismissible fade show position-absolute top-0 end-0" role="alert">
                <strong>Data Successfully!</strong> Rak Buku Berhasil Di Tambahkan!
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        @if (session()->has('notifupdate'))
            <div class="alert alert-success alert-dismissible fade show position-absolute top-0 end-0" role="alert">
                <strong>Data Successfully!</strong> Rak Buku Berhasil Di Ubah!
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        @if (session()->has('notifhapus'))
            <div class="alert alert-danger alert-dismissible fade show position-absolute top-0 end-0" role="alert">
                <strong>Data Successfully!</strong> Rak Buku Berhasil Di Hapus!
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        @if (session()->has('notifterdapatbuku'))
            <div class="alert alert-danger alert-dismissible fade show position-absolute top-0 end-0" role="alert">
                <strong>Data Failed!</strong> Rak Buku Berisi Buku!
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        @error('nama')
            <div class="alert alert-danger alert-dismissible fade show position-absolute top-0 end-0" role="alert">
                <strong>Data Failed!</strong> {{ $message }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @enderror
        @error('kapasitas')
            <div class="alert alert-danger alert-dismissible fade show position-absolute top-0 end-0" role="alert">
                <strong>Data Failed!</strong> {{ $message }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @enderror
        @error('keterangan')
            <div class="alert alert-danger alert-dismissible fade show position-absolute top-0 end-0" role="alert">
                <strong>Data Failed!</strong> {{ $message }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @enderror

        {{-- end alert --}}
        {{-- button add --}}
        <a class="mt-0 mt-sm-0 btn btn-sm btn-primary shadow-sm" data-bs-toggle="modal" data-bs-target="#tambah">Tambah</a>
    </div>

    {{-- cari --}}
    <form action="/rak" method="get">
        <div class="d-flex justify-content-end mb-4">
            <input type="search" name="cari" class="cari form-control me-3" placeholder="Cari"
                value="{{ $lastquery }}">
            {{-- <button type="submit" class="btn btn-outline-secondary me-3">Cari</button> --}}
        </div>
    </form>

    <div class="table-responsive">
        <table class="table text-center">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama Rak</th>
                    <th>Kapasitas</th>
                    <th>Kapasitas Tersedia</th>
                    <th>Keterangan</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $no = 1;
                @endphp
                @foreach ($datarak as $rak)
                    <tr>
                        <td>{{ $no++ }}</td>
                        <td>{{ $rak->nama }}</td>
                        <td>{{ $rak->kapasitas }}</td>
                        <td>{{ $rak->kapasitas_tersedia }}</td>
                        <td>{{ $rak->keterangan }}</td>
                        <td class="justify-content-center align-items-center">
                            <button class="btn btn-success btn-sm mb-2 me-1 me-sm-2" data-bs-toggle="modal"
                                data-bs-target="#edit{{ $rak->id }}">Edit</button>
                            <button class="btn btn-danger btn-sm mb-2" data-bs-toggle="modal"
                                data-bs-target="#hapus{{ $rak->id }}">Hapus</button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Modal Tambah -->
    <div class="modal fade" id="tambah" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
        aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="staticBackdropLabel">Tambah Rak Buku</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="/addrak" method="POST">
                    @csrf
                    <div class="modal-body p-4">
                        <div class="row mb-3">
                            <label class="mb-2 fw-medium">Nama Rak</label>
                            <input type="text" class="form-control" placeholder="Nama Rak" name="nama" required>
                        </div>
                        <div class="row mb-3">
                            <label class="mb-2 fw-medium">Kapasitas</label>
                            <input type="number" class="form-control" placeholder="Kapasitas" name="kapasitas"
                                id="kapasitas" required>
                        </div>
                        <div class="row mb-3">
                            <label class="mb-2 fw-medium">Keterangan Atau Lokasi</label>
                            <textarea name="keterangan" cols="30" rows="5" class="form-control" required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Edit -->
    @foreach ($datarak as $rak)
        <div class="modal fade" id="edit{{ $rak->id }}" data-bs-backdrop="static" data-bs-keyboard="false"
            tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="staticBackdropLabel">Edit Rak Buku</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="/uprak" method="POST">
                        @csrf
                        <input type="hidden" name="id" value="{{ $rak->id }}">
                        <input type="hidden" name="old_kaps" value="{{ $rak->kapasitas }}">
                        <input type="hidden" name="tersedia" value="{{ $rak->kapasitas_tersedia }}">
                        <div class="modal-body p-4">
                            <div class="row mb-3">
                                <label class="mb-2 fw-medium">Nama Rak</label>
                                <input type="text" class="form-control" placeholder="Nama Rak" name="nama"
                                    value="{{ $rak->nama }}" required>
                            </div>
                            <div class="row mb-3">
                                <label class="mb-2 fw-medium">Kapasitas</label>
                                <input type="number" class="form-control" placeholder="Kapasitas" name="new_kaps"
                                    value="{{ $rak->kapasitas }}" id="kapasitas" required>
                            </div>
                            <div class="row mb-3">
                                <label class="mb-2 fw-medium">Keterangan Atau Lokasi</label>
                                <textarea name="keterangan" cols="30" rows="5" class="form-control" required>{{ $rak->keterangan }}</textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-success">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endforeach

    <!-- Modal Hapus -->
    @foreach ($datarak as $rak)
        <div class="modal fade" id="hapus{{ $rak->id }}" data-bs-backdrop="static" data-bs-keyboard="false"
            tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="staticBackdropLabel">Hapus rak {{ $rak->nama }}</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="/delrak" method="POST">
                        @csrf
                        <input type="hidden" name="id" value="{{ $rak->id }}">
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-danger">Hapus</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endforeach

    <script>
        window.onload = function() {

            var aktif = document.getElementById("maindata");
            var active = document.getElementById("rak");
            aktif.classList.add('aktif');
            active.classList.add('active');

        };
    </script>

    <script>
        const numberInput = document.getElementById('kapasitas');

        numberInput.addEventListener('input', function() {
            let value = parseFloat(this.value);

            if (isNaN(value)) {
                // Handle non-numeric input
                this.value = 1;
            } else if (value < 1) {
                this.value = 1;
            }
        });
    </script>
@endsection
