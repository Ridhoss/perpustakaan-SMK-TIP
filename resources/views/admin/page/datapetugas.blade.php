@extends('admin.layout.layout')

@section('css')
    <style>
        .cari {
            width: 150px !important;
        }

        .lainnya {
            right: 8px;
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
        <h1 class="h3 mb-0 text-gray-800 font-primary">Data Petugas</h1>

        {{-- alerts --}}
        @if (session()->has('notifadd'))
            <div class="alert alert-success alert-dismissible fade show position-absolute top-0 end-0" role="alert">
                <strong>Data Successfully!</strong> Petugas Berhasil Di Tambahkan!
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        @if (session()->has('notifupdate'))
            <div class="alert alert-success alert-dismissible fade show position-absolute top-0 end-0" role="alert">
                <strong>Data Successfully!</strong> Petugas Berhasil Di Update!
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        @if (session()->has('uppass'))
            <div class="alert alert-success alert-dismissible fade show position-absolute top-0 end-0" role="alert">
                <strong>Data Successfully!</strong> Password Berhasil Di Update!
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        @if (session()->has('uppassgal'))
            <div class="alert alert-danger alert-dismissible fade show position-absolute top-0 end-0" role="alert">
                <strong>Data Failed!</strong> Konfirmasi Password Gagal!
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        @if (session()->has('notifhapus'))
            <div class="alert alert-danger alert-dismissible fade show position-absolute top-0 end-0" role="alert">
                <strong>Data Successfully!</strong> Petugas Berhasil Di Hapus!
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        @error('username')
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
        @error('password')
            <div class="alert alert-danger alert-dismissible fade show position-absolute top-0 end-0" role="alert">
                <strong>Data Failed!</strong> {{ $message }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @enderror
        @error('email')
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
        @error('gender')
            <div class="alert alert-danger alert-dismissible fade show position-absolute top-0 end-0" role="alert">
                <strong>Data Failed!</strong> {{ $message }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @enderror
        @error('religion')
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
        @error('photo')
            <div class="alert alert-danger alert-dismissible fade show position-absolute top-0 end-0" role="alert">
                <strong>Data Failed!</strong> {{ $message }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @enderror
        @error('confirmation')
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
    <form action="/petugas" method="get">
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
                    <th>Username</th>
                    <th>Nama</th>
                    <th>Email</th>
                    <th>Nomor Telepon</th>
                    <th>Jenis Kelamin</th>
                    <th>Agama</th>
                    <th>Tanggal Lahir</th>
                    <th>Foto</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $no = 1;
                @endphp
                @foreach ($datapetugas as $petugas)
                    <tr>
                        <td>{{ $no++ }}</td>
                        <td>{{ $petugas->username }}</td>
                        <td>{{ $petugas->name }}</td>
                        <td>{{ $petugas->email }}</td>
                        <td>{{ $petugas->phone }}</td>
                        @if ($petugas->gender == 'L')
                            <td>Laki-Laki</td>
                        @else
                            <td>Perempuan</td>
                        @endif
                        <td>{{ $petugas->religion }}</td>
                        <td>{{ $petugas->date }}</td>
                        <td><img src="{{ Storage::url('public/petugas/' . $petugas->photo) }}" width="100"
                                class="rounded">
                        </td>
                        <td>
                            <button class="btn btn-success btn-sm mb-2 w-100" data-bs-toggle="modal"
                                data-bs-target="#edit{{ $petugas->id }}">Edit</button>
                            <button class="btn btn-secondary btn-sm mb-2 w-100" data-bs-toggle="modal"
                                data-bs-target="#password{{ $petugas->id }}">Edit Password</button>
                            <button class="btn btn-danger btn-sm mb-2 w-100" data-bs-toggle="modal"
                                data-bs-target="#hapus{{ $petugas->id }}">Hapus</button>
                            {{-- <button class="btn btn-sm btn-secondary" type="button" data-bs-toggle="collapse"
                            data-bs-target="#action{{ $petugas->id }}" aria-expanded="false"
                            aria-controls="collapseExample">
                            Action
                        </button>
                        <div class="lainnya collapse position-absolute mt-2" id="action{{ $petugas->id }}">
                            <div class="card card-body">
                                <h6 class="text-secondary mb-3">Action :</h6>
                                <button class="btn btn-success btn-sm mb-2" data-bs-toggle="modal"
                                    data-bs-target="#edit{{ $petugas->id }}">Edit</button>
                                <button class="btn btn-secondary btn-sm mb-2" data-bs-toggle="modal"
                                    data-bs-target="#password{{ $petugas->id }}">Edit Password</button>
                                <button class="btn btn-danger btn-sm mb-2" data-bs-toggle="modal"
                                    data-bs-target="#hapus{{ $petugas->id }}">Hapus</button>
                            </div>
                        </div> --}}
                        </td>
                        {{-- <td class="justify-content-center align-items-center">
                        <button class="btn btn-success btn-sm mb-2 me-1 me-sm-2" data-bs-toggle="modal"
                            data-bs-target="#edit{{ $admin->id }}">Edit</button>
                        <button class="btn btn-danger btn-sm mb-2" data-bs-toggle="modal"
                            data-bs-target="#hapus{{ $admin->id }}">Hapus</button>
                    </td> --}}
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
                    <h1 class="modal-title fs-5" id="staticBackdropLabel">Tambah Petugas</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="/addpetugas" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body p-4">
                        <div class="row mb-3">
                            <label class="mb-2 fw-medium">Username</label>
                            <input type="text" class="form-control" placeholder="Username" name="username" required>
                        </div>
                        <div class="row mb-3">
                            <label class="mb-2 fw-medium">Nama</label>
                            <input type="text" class="form-control" placeholder="Nama" name="name" required>
                        </div>
                        <div class="row mb-3">
                            <label class="mb-2 fw-medium">Password</label>
                            <input type="password" class="form-control" placeholder="Password" name="password" required>
                        </div>
                        <div class="row mb-3">
                            <label class="mb-2 fw-medium">Email</label>
                            <input type="text" class="form-control" placeholder="Email" name="email" required>
                        </div>
                        <div class="row mb-3">
                            <label class="mb-2 fw-medium">Nomor Telepon</label>
                            <input type="text" class="form-control" placeholder="Nomor Telepon" name="phone"
                                required>
                        </div>
                        <div class="row mb-3">
                            <label class="mb-2 fw-medium">Jenis Kelamin</label>
                            <select name="gender" class="form-control" required>
                                <option value="L">Laki-Laki</option>
                                <option value="P">Perempuan</option>
                            </select>
                        </div>
                        <div class="row mb-3">
                            <label class="mb-2 fw-medium">Agama</label>
                            <select name="religion" class="form-control" required>
                                <option value="Islam">Islam</option>
                                <option value="Kristen">Kristen</option>
                                <option value="Katolik">Katolik</option>
                                <option value="Hindu">Hindu</option>
                                <option value="Budha">Budha</option>
                                <option value="Konghucu">Konghucu</option>
                            </select>
                        </div>
                        <div class="row mb-3">
                            <label class="mb-2 fw-medium">Tanggal Lahir</label>
                            <input type="date" class="form-control" name="date" required>
                        </div>
                        <div class="row mb-3">
                            <label class="mb-2 fw-medium">Foto</label>
                            <input type="file" class="form-control" name="photo" required>
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
    @foreach ($datapetugas as $petugas)
        <div class="modal fade" id="edit{{ $petugas->id }}" data-bs-backdrop="static" data-bs-keyboard="false"
            tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="staticBackdropLabel">Edit Petugas</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="/updatepetugas" method="POST" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="id" value="{{ $petugas->id }}">
                        <input type="hidden" name="oldphoto" value="{{ $petugas->photo }}">
                        <div class="modal-body p-4">
                            <div class="row mb-3">
                                <label class="mb-2 fw-medium">Username</label>
                                <input type="text" class="form-control" placeholder="Username" name="username"
                                    value="{{ $petugas->username }}" required>
                            </div>
                            <div class="row mb-3">
                                <label class="mb-2 fw-medium">Nama</label>
                                <input type="text" class="form-control" placeholder="Nama" name="name"
                                    value="{{ $petugas->name }}" required>
                            </div>
                            <div class="row mb-3">
                                <label class="mb-2 fw-medium">Email</label>
                                <input type="text" class="form-control" placeholder="Email" name="email"
                                    value="{{ $petugas->email }}" required>
                            </div>
                            <div class="row mb-3">
                                <label class="mb-2 fw-medium">Nomor Telepon</label>
                                <input type="text" class="form-control" placeholder="Nomor Telepon" name="phone"
                                    value="{{ $petugas->phone }}" required>
                            </div>
                            <div class="row mb-3">
                                <label class="mb-2 fw-medium">Jenis Kelamin</label>
                                <select name="gender" class="form-control" required>
                                    @if ($petugas->gender == 'L')
                                        <option value="L">Laki-Laki</option>
                                        <option value="P">Perempuan</option>
                                    @else
                                        <option value="P">Perempuan</option>
                                        <option value="L">Laki-Laki</option>
                                    @endif
                                </select>
                            </div>
                            <div class="row mb-3">
                                <label class="mb-2 fw-medium">Agama</label>
                                <select name="religion" class="form-control" required>
                                    @if ($petugas->religion == 'Islam')
                                        <option value="Islam">Islam</option>
                                        <option value="Kristen">Kristen</option>
                                        <option value="Katolik">Katolik</option>
                                        <option value="Hindu">Hindu</option>
                                        <option value="Budha">Budha</option>
                                        <option value="Konghucu">Konghucu</option>
                                    @elseif($petugas->religion == 'Kristen')
                                        <option value="Kristen">Kristen</option>
                                        <option value="Islam">Islam</option>
                                        <option value="Katolik">Katolik</option>
                                        <option value="Hindu">Hindu</option>
                                        <option value="Budha">Budha</option>
                                        <option value="Konghucu">Konghucu</option>
                                    @elseif($petugas->religion == 'Katolik')
                                        <option value="Katolik">Katolik</option>
                                        <option value="Islam">Islam</option>
                                        <option value="Kristen">Kristen</option>
                                        <option value="Hindu">Hindu</option>
                                        <option value="Budha">Budha</option>
                                        <option value="Konghucu">Konghucu</option>
                                    @elseif($petugas->religion == 'Hindu')
                                        <option value="Hindu">Hindu</option>
                                        <option value="Islam">Islam</option>
                                        <option value="Kristen">Kristen</option>
                                        <option value="Katolik">Katolik</option>
                                        <option value="Budha">Budha</option>
                                        <option value="Konghucu">Konghucu</option>
                                    @elseif($petugas->religion == 'Budha')
                                        <option value="Islam">Islam</option>
                                        <option value="Budha">Budha</option>
                                        <option value="Kristen">Kristen</option>
                                        <option value="Katolik">Katolik</option>
                                        <option value="Hindu">Hindu</option>
                                        <option value="Konghucu">Konghucu</option>
                                    @elseif($petugas->religion == 'Konghucu')
                                        <option value="Konghucu">Konghucu</option>
                                        <option value="Islam">Islam</option>
                                        <option value="Kristen">Kristen</option>
                                        <option value="Katolik">Katolik</option>
                                        <option value="Hindu">Hindu</option>
                                        <option value="Budha">Budha</option>
                                    @else
                                        <option value="Islam">Islam</option>
                                        <option value="Kristen">Kristen</option>
                                        <option value="Katolik">Katolik</option>
                                        <option value="Hindu">Hindu</option>
                                        <option value="Budha">Budha</option>
                                        <option value="Konghucu">Konghucu</option>
                                    @endif
                                </select>
                            </div>
                            <div class="row mb-3">
                                <label class="mb-2 fw-medium">Tanggal Lahir</label>
                                <input type="date" class="form-control" name="date" value="{{ $petugas->date }}"
                                    required>
                            </div>
                            <div class="row mb-3">
                                <label class="mb-2 fw-medium">Foto <span class="text-danger">(*Kosongkan jika tidak ingin
                                        mengganti)</span></label>
                                <input type="file" class="form-control" name="photo">
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

    {{-- Modal Password --}}
    @foreach ($datapetugas as $petugas)
        <div class="modal fade" id="password{{ $petugas->id }}" data-bs-backdrop="static" data-bs-keyboard="false"
            tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="staticBackdropLabel">Edit Password</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="/updatepasspetugas" method="POST">
                        @csrf
                        <div class="modal-body p-4">
                            <input type="hidden" name="id" value="{{ $petugas->id }}">
                            <div class="row mb-3">
                                <label class="mb-2 fw-medium">Password Baru</label>
                                <input type="password" class="form-control" placeholder="Password Baru" name="password"
                                    required>
                            </div>
                            <div class="row mb-3">
                                <label class="mb-2 fw-medium">Konfirmasi Password Baru</label>
                                <input type="password" class="form-control" placeholder="Konfirmasi Password Baru"
                                    name="confirmation" required>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <a href="" class="btn btn-secondary" data-bs-dismiss="modal">Close</a>
                            <button type="submit" class="btn btn-success">Save</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endforeach

    <!-- Modal Hapus -->
    @foreach ($datapetugas as $petugas)
        <div class="modal fade" id="hapus{{ $petugas->id }}" data-bs-backdrop="static" data-bs-keyboard="false"
            tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="staticBackdropLabel">Hapus Petugas {{ $petugas->name }}</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="/hapuspetugas" method="POST">
                        @csrf
                        <input type="hidden" name="id" value="{{ $petugas->id }}">
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
