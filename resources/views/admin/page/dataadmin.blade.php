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
        <h1 class="h3 mb-0 text-gray-800 font-primary">Data Admin</h1>

        {{-- alerts --}}
        @if (session()->has('notifadd'))
            <div class="alert alert-success alert-dismissible fade show position-absolute top-0 end-0" role="alert">
                <strong>Data Successfully!</strong> Admin Berhasil Di Tambahkan!
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        @if (session()->has('notifupdate'))
            <div class="alert alert-success alert-dismissible fade show position-absolute top-0 end-0" role="alert">
                <strong>Data Successfully!</strong> Admin Berhasil Di Update!
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
                <strong>Data Successfully!</strong> Admin Berhasil Di Hapus!
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
    <form action="/admin" method="get">
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
                @foreach ($dataadmin as $admin)
                    <tr>
                        <td>{{ $no++ }}</td>
                        <td>{{ $admin->username }}</td>
                        <td>{{ $admin->name }}</td>
                        <td>{{ $admin->email }}</td>
                        <td>{{ $admin->phone }}</td>
                        @if ($admin->gender == 'L')
                            <td>Laki-Laki</td>
                        @else
                            <td>Perempuan</td>
                        @endif
                        <td>{{ $admin->religion }}</td>
                        <td>{{ $admin->date }}</td>
                        <td><img src="{{ Storage::url('public/admin/' . $admin->photo) }}" width="100"
                                class="rounded">
                        </td>
                        <td>
                            <button class="btn btn-success btn-sm mb-2 w-100" data-bs-toggle="modal"
                                data-bs-target="#edit{{ $admin->id }}">Edit</button>
                            <button class="btn btn-secondary btn-sm mb-2 w-100" data-bs-toggle="modal"
                                data-bs-target="#password{{ $admin->id }}">Edit Password</button>
                            <button class="btn btn-danger btn-sm mb-2 w-100" data-bs-toggle="modal"
                                data-bs-target="#hapus{{ $admin->id }}">Hapus</button>

                            {{-- <button class="btn btn-sm btn-secondary" type="button" data-bs-toggle="collapse"
                            data-bs-target="#action{{ $admin->id }}" aria-expanded="false"
                            aria-controls="collapseExample" aria-haspopup="true">
                            Action
                        </button> --}}

                            {{-- <div class="lainnya collapse position-absolute mt-2" id="action{{ $admin->id }}">
                            <div class="card card-body">
                                <h6 class="text-secondary mb-3">Action :</h6>
                                <button class="btn btn-success btn-sm mb-2" data-bs-toggle="modal"
                                    data-bs-target="#edit{{ $admin->id }}">Edit</button>
                                <button class="btn btn-secondary btn-sm mb-2" data-bs-toggle="modal"
                                    data-bs-target="#password{{ $admin->id }}">Edit Password</button>
                                <button class="btn btn-danger btn-sm mb-2" data-bs-toggle="modal"
                                    data-bs-target="#hapus{{ $admin->id }}">Hapus</button>
                            </div>
                        </div> --}}
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
                    <h1 class="modal-title fs-5" id="staticBackdropLabel">Tambah Admin</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="/addadmin" method="POST" enctype="multipart/form-data">
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
    @foreach ($dataadmin as $admin)
        <div class="modal fade" id="edit{{ $admin->id }}" data-bs-backdrop="static" data-bs-keyboard="false"
            tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="staticBackdropLabel">Edit Admin</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="/updateadmin" method="POST" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="id" value="{{ $admin->id }}">
                        <input type="hidden" name="oldphoto" value="{{ $admin->photo }}">
                        <div class="modal-body p-4">
                            <div class="row mb-3">
                                <label class="mb-2 fw-medium">Username</label>
                                <input type="text" class="form-control" placeholder="Username" name="username"
                                    value="{{ $admin->username }}" required>
                            </div>
                            <div class="row mb-3">
                                <label class="mb-2 fw-medium">Nama</label>
                                <input type="text" class="form-control" placeholder="Nama" name="name"
                                    value="{{ $admin->name }}" required>
                            </div>
                            <div class="row mb-3">
                                <label class="mb-2 fw-medium">Email</label>
                                <input type="text" class="form-control" placeholder="Email" name="email"
                                    value="{{ $admin->email }}" required>
                            </div>
                            <div class="row mb-3">
                                <label class="mb-2 fw-medium">Nomor Telepon</label>
                                <input type="text" class="form-control" placeholder="Nomor Telepon" name="phone"
                                    value="{{ $admin->phone }}" required>
                            </div>
                            <div class="row mb-3">
                                <label class="mb-2 fw-medium">Jenis Kelamin</label>
                                <select name="gender" class="form-control" required>
                                    @if ($admin->gender == 'L')
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
                                    @if ($admin->religion == 'Islam')
                                        <option value="Islam">Islam</option>
                                        <option value="Kristen">Kristen</option>
                                        <option value="Katolik">Katolik</option>
                                        <option value="Hindu">Hindu</option>
                                        <option value="Budha">Budha</option>
                                        <option value="Konghucu">Konghucu</option>
                                    @elseif($admin->religion == 'Kristen')
                                        <option value="Kristen">Kristen</option>
                                        <option value="Islam">Islam</option>
                                        <option value="Katolik">Katolik</option>
                                        <option value="Hindu">Hindu</option>
                                        <option value="Budha">Budha</option>
                                        <option value="Konghucu">Konghucu</option>
                                    @elseif($admin->religion == 'Katolik')
                                        <option value="Katolik">Katolik</option>
                                        <option value="Islam">Islam</option>
                                        <option value="Kristen">Kristen</option>
                                        <option value="Hindu">Hindu</option>
                                        <option value="Budha">Budha</option>
                                        <option value="Konghucu">Konghucu</option>
                                    @elseif($admin->religion == 'Hindu')
                                        <option value="Hindu">Hindu</option>
                                        <option value="Islam">Islam</option>
                                        <option value="Kristen">Kristen</option>
                                        <option value="Katolik">Katolik</option>
                                        <option value="Budha">Budha</option>
                                        <option value="Konghucu">Konghucu</option>
                                    @elseif($admin->religion == 'Budha')
                                        <option value="Islam">Islam</option>
                                        <option value="Budha">Budha</option>
                                        <option value="Kristen">Kristen</option>
                                        <option value="Katolik">Katolik</option>
                                        <option value="Hindu">Hindu</option>
                                        <option value="Konghucu">Konghucu</option>
                                    @elseif($admin->religion == 'Konghucu')
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
                                <input type="date" class="form-control" name="date" value="{{ $admin->date }}"
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
    @foreach ($dataadmin as $admin)
        <div class="modal fade" id="password{{ $admin->id }}" data-bs-backdrop="static" data-bs-keyboard="false"
            tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="staticBackdropLabel">Edit Password</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="/updatepassadmin" method="POST">
                        @csrf
                        <div class="modal-body p-4">
                            <input type="hidden" name="id" value="{{ $admin->id }}">
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
    @foreach ($dataadmin as $admin)
        <div class="modal fade" id="hapus{{ $admin->id }}" data-bs-backdrop="static" data-bs-keyboard="false"
            tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="staticBackdropLabel">Hapus Admin {{ $admin->name }}</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="/hapusadmin" method="POST">
                        @csrf
                        <input type="hidden" name="id" value="{{ $admin->id }}">
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
