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
    <div class="alert d-flex position-relative align-items-center justify-content-between">
        <h1 class="h3 mb-0 text-gray-800">Data Anggota</h1>

        {{-- alerts --}}
        @if (session()->has('notifadd'))
            <div class="alert alert-success alert-dismissible fade show position-absolute top-0 end-0" role="alert">
                <strong>Data Successfully!</strong> Anggota Berhasil Di Tambahkan!
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        @if (session()->has('notifupdate'))
            <div class="alert alert-success alert-dismissible fade show position-absolute top-0 end-0" role="alert">
                <strong>Data Successfully!</strong> Anggota Berhasil Di Update!
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        @if (session()->has('notifhapus'))
            <div class="alert alert-danger alert-dismissible fade show position-absolute top-0 end-0" role="alert">
                <strong>Data Successfully!</strong> Anggota Berhasil Di Hapus!
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        @error('nisn')
            <div class="alert alert-danger alert-dismissible fade show position-absolute top-0 end-0" role="alert">
                <strong>Data Failed!</strong> {{ $message }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @enderror
        @error('name')
            <div class="alert alert-danger alert-dismissible fade show position-absolute top-0 end-0" role="alert">
                <strong>Data Failed!</strong> {{ $message }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @enderror
        @error('gender')
            <div class="alert alert-danger alert-dismissible fade show position-absolute top-0 end-0" role="alert">
                <strong>Data Failed!</strong> {{ $message }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @enderror
        @error('date')
            <div class="alert alert-danger alert-dismissible fade show position-absolute top-0 end-0" role="alert">
                <strong>Data Failed!</strong> {{ $message }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @enderror
        @error('phone')
            <div class="alert alert-danger alert-dismissible fade show position-absolute top-0 end-0" role="alert">
                <strong>Data Failed!</strong> {{ $message }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @enderror
        @error('address')
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
    <form action="/anggota" method="get">
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
                    <th>NISN</th>
                    <th>Nama</th>
                    <th>Jenis Kelamin</th>
                    <th>Tanggal Lahir</th>
                    <th>Nomor Telepon</th>
                    <th>Alamat</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $no = 1;
                @endphp
                @foreach ($dataanggota as $anggota)
                    <tr>
                        <td>{{ $no++ }}</td>
                        <td>{{ $anggota->nisn }}</td>
                        <td>{{ $anggota->name }}</td>
                        @if ($anggota->gender == 'L')
                            <td>Laki-Laki</td>
                        @else
                            <td>Perempuan</td>
                        @endif
                        <td>{{ $anggota->date }}</td>
                        <td>{{ $anggota->phone }}</td>
                        <td>{{ $anggota->address }}</td>
                        <td class="justify-content-center align-items-center">
                            <button class="btn btn-success btn-sm mb-2 me-1 me-sm-2" data-bs-toggle="modal"
                                data-bs-target="#edit{{ $anggota->id }}">Edit</button>
                            <button class="btn btn-danger btn-sm mb-2" data-bs-toggle="modal"
                                data-bs-target="#hapus{{ $anggota->id }}">Hapus</button>
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
                    <h1 class="modal-title fs-5" id="staticBackdropLabel">Tambah Anggota</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="/addanggota" method="POST">
                    @csrf
                    <div class="modal-body p-4">
                        <div class="row mb-3">
                            <label class="mb-2 fw-medium">NISN</label>
                            <input type="text" class="form-control" placeholder="NISN" name="nisn">
                        </div>
                        <div class="row mb-3">
                            <label class="mb-2 fw-medium">Nama</label>
                            <input type="text" class="form-control" placeholder="Nama" name="name">
                        </div>
                        <div class="row mb-3">
                            <label class="mb-2 fw-medium">Jenis Kelamin</label>
                            <select name="gender" class="form-control">
                                <option value="L">Laki-Laki</option>
                                <option value="P">Perempuan</option>
                            </select>
                        </div>
                        <div class="row mb-3">
                            <label class="mb-2 fw-medium">Tanggal Lahir</label>
                            <input type="date" class="form-control" name="date">
                        </div>
                        <div class="row mb-3">
                            <label class="mb-2 fw-medium">Nomor Telepon</label>
                            <input type="text" class="form-control" placeholder="Nomor Telepon" name="phone">
                        </div>
                        <div class="row mb-3">
                            <label class="mb-2 fw-medium">Alamat</label>
                            <textarea name="address" cols="30" rows="5" class="form-control"></textarea>
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
    @foreach ($dataanggota as $anggota)
        <div class="modal fade" id="edit{{ $anggota->id }}" data-bs-backdrop="static" data-bs-keyboard="false"
            tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="staticBackdropLabel">Edit Anggota</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="/updateanggota" method="POST">
                        @csrf
                        <input type="hidden" name="id" value="{{ $anggota->id }}">
                        <div class="modal-body p-4">
                            <div class="row mb-3">
                                <label class="mb-2 fw-medium">NISN</label>
                                <input type="text" class="form-control" placeholder="NISN" name="nisn"
                                    value="{{ $anggota->nisn }}">
                            </div>
                            <div class="row mb-3">
                                <label class="mb-2 fw-medium">Nama</label>
                                <input type="text" class="form-control" placeholder="Nama" name="name"
                                    value="{{ $anggota->name }}">
                            </div>
                            <div class="row mb-3">
                                <label class="mb-2 fw-medium">Jenis Kelamin</label>
                                <select name="gender" class="form-control">
                                    @if ($anggota->gender == 'L')
                                        <option value="L">Laki-Laki</option>
                                        <option value="P">Perempuan</option>
                                    @else
                                        <option value="P">Perempuan</option>
                                        <option value="L">Laki-Laki</option>
                                    @endif
                                </select>
                            </div>
                            <div class="row mb-3">
                                <label class="mb-2 fw-medium">Tanggal Lahir</label>
                                <input type="date" class="form-control" name="date" value="{{ $anggota->date }}">
                            </div>
                            <div class="row mb-3">
                                <label class="mb-2 fw-medium">Nomor Telepon</label>
                                <input type="text" class="form-control" placeholder="Nomor Telepon" name="phone"
                                    value="{{ $anggota->phone }}">
                            </div>
                            <div class="row mb-3">
                                <label class="mb-2 fw-medium">Alamat</label>
                                <textarea name="address" cols="30" rows="5" class="form-control">{{ $anggota->address }}</textarea>
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
    @foreach ($dataanggota as $anggota)
        <div class="modal fade" id="hapus{{ $anggota->id }}" data-bs-backdrop="static" data-bs-keyboard="false"
            tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="staticBackdropLabel">Hapus Anggota {{ $anggota->name }}</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="/hapusanggota" method="POST">
                        @csrf
                        <input type="hidden" name="id" value="{{ $anggota->id }}">
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-danger">Hapus</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endforeach
@endsection
