@extends('petugas.layout.petlayout')

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
    <div class="alert d-flex position-relative align-items-center justify-content-between">
        <h1 class="h3 mb-0 text-gray-800">Data Buku</h1>

        {{-- alerts --}}
        @if (session()->has('notifadd'))
            <div class="alert alert-success alert-dismissible fade show position-absolute top-0 end-0" role="alert">
                <strong>Data Successfully!</strong> Buku Berhasil Di Tambahkan!
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        @if (session()->has('notifupdate'))
            <div class="alert alert-success alert-dismissible fade show position-absolute top-0 end-0" role="alert">
                <strong>Data Successfully!</strong> Buku Berhasil Di Update!
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        @if (session()->has('notifhapus'))
            <div class="alert alert-danger alert-dismissible fade show position-absolute top-0 end-0" role="alert">
                <strong>Data Successfully!</strong> Buku Berhasil Di Hapus!
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        @error('tanggal')
            <div class="alert alert-danger alert-dismissible fade show position-absolute top-0 end-0" role="alert">
                <strong>Data Failed!</strong> {{ $message }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @enderror
        @error('isbn')
            <div class="alert alert-danger alert-dismissible fade show position-absolute top-0 end-0" role="alert">
                <strong>Data Failed!</strong> {{ $message }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @enderror
        @error('pengarang')
            <div class="alert alert-danger alert-dismissible fade show position-absolute top-0 end-0" role="alert">
                <strong>Data Failed!</strong> {{ $message }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @enderror
        @error('judul')
            <div class="alert alert-danger alert-dismissible fade show position-absolute top-0 end-0" role="alert">
                <strong>Data Failed!</strong> {{ $message }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @enderror
        @error('eks')
            <div class="alert alert-danger alert-dismissible fade show position-absolute top-0 end-0" role="alert">
                <strong>Data Failed!</strong> {{ $message }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @enderror
        @error('thn_inv')
            <div class="alert alert-danger alert-dismissible fade show position-absolute top-0 end-0" role="alert">
                <strong>Data Failed!</strong> {{ $message }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @enderror
        @error('asl_id')
            <div class="alert alert-danger alert-dismissible fade show position-absolute top-0 end-0" role="alert">
                <strong>Data Failed!</strong> {{ $message }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @enderror
        @error('ktg_id')
            <div class="alert alert-danger alert-dismissible fade show position-absolute top-0 end-0" role="alert">
                <strong>Data Failed!</strong> {{ $message }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @enderror
        @error('bhs_id')
            <div class="alert alert-danger alert-dismissible fade show position-absolute top-0 end-0" role="alert">
                <strong>Data Failed!</strong> {{ $message }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @enderror
        @error('tahun_terbit')
            <div class="alert alert-danger alert-dismissible fade show position-absolute top-0 end-0" role="alert">
                <strong>Data Failed!</strong> {{ $message }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @enderror
        @error('sinopsis')
            <div class="alert alert-danger alert-dismissible fade show position-absolute top-0 end-0" role="alert">
                <strong>Data Failed!</strong> {{ $message }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @enderror
        @error('ket')
            <div class="alert alert-danger alert-dismissible fade show position-absolute top-0 end-0" role="alert">
                <strong>Data Failed!</strong> {{ $message }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @enderror
        @error('jumlah')
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

        {{-- end alert --}}
        {{-- button add --}}
        <div class="pre-btn d-flex flex-column flex-sm-row">
            {{-- <a class="btn btn-sm btn-success shadow-sm me-2 mb-2 mb-sm-0 w-100" data-bs-toggle="modal"
                data-bs-target="#import">Import</a> --}}
            <a class="btn btn-sm btn-primary shadow-sm me-2 mb-2 mb-sm-0 w-100" data-bs-toggle="modal"
                data-bs-target="#tambah">Tambah</a>
        </div>
    </div>

    {{-- cari --}}
    <form action="/petdatabuku" method="get">
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
                    <th>ISBN</th>
                    <th>Pengarang</th>
                    <th>Judul</th>
                    <th>Tahun Inventaris</th>
                    <th>Asal Buku</th>
                    <th>Jenis Buku</th>
                    <th>Bahasa</th>
                    <th>Tahun Terbit</th>
                    <th>Keterangan</th>
                    <th>Sinopsis</th>
                    <th>Jumlah Buku</th>
                    <th>Gambar</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $no = 1;
                @endphp
                @foreach ($databuku as $buku)
                    <tr>
                        <td>{{ $no++ }}</td>
                        <td>{{ $buku->isbn }}</td>
                        <td>{{ $buku->pengarang }}</td>
                        <td>{{ $buku->judul }}</td>
                        <td>{{ $buku->thn_inv }}</td>
                        <td>{{ $buku->asal }}</td>
                        <td>{{ $buku->kategori }}</td>
                        <td>{{ $buku->bahasa }}</td>
                        <td>{{ $buku->tahun_terbit }}</td>
                        <td>{{ $buku->ket }}</td>
                        <td>{{ $buku->sinopsis }}</td>
                        <td>{{ $buku->jumlah }}</td>
                        <td><img src="{{ Storage::url('public/buku/' . $buku->photo) }}" width="100" class="rounded">
                        </td>
                        <td>
                            <button class="btn btn-success btn-sm mb-2 w-100" data-bs-toggle="modal"
                                data-bs-target="#edit{{ $buku->isbn }}">Edit</button>
                            <button class="btn btn-danger btn-sm mb-2 w-100" data-bs-toggle="modal"
                                data-bs-target="#hapus{{ $buku->isbn }}">Hapus</button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Modal Tambah -->
    <div class="modal fade" id="tambah" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
        aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-sm-md">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="staticBackdropLabel">Tambah Buku</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="/addbuku" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="role" value="petugas">
                    <div class="modal-body ps-4 pe-5 py-3 d-block d-sm-flex">
                        <div class="col-12 col-sm-6">
                            <div class="row mb-3">
                                <label class="mb-2 fw-medium">Tanggal</label>
                                <input type="date" class="form-control" name="tanggal" required>
                            </div>
                            <div class="row mb-3">
                                <label class="mb-2 fw-medium">ISBN</label>
                                <input type="text" class="form-control" placeholder="ISBN" name="isbn" required>
                            </div>
                            <div class="row mb-3">
                                <label class="mb-2 fw-medium">Pengarang</label>
                                <input type="text" class="form-control" placeholder="Pengarang" name="pengarang"
                                    required>
                            </div>
                            <div class="row mb-3">
                                <label class="mb-2 fw-medium">Judul</label>
                                <input type="text" class="form-control" placeholder="Judul" name="judul" required>
                            </div>
                            <div class="row mb-3">
                                <label class="mb-2 fw-medium">Eksemplar</label>
                                <input type="number" class="form-control" id="eks" name="eks"
                                    placeholder="Eksemplar" min="1" required>
                            </div>
                            <div class="row mb-3">
                                <label class="mb-2 fw-medium">Tahun Inventaris</label>
                                <input type="number" class="form-control" id="thn_inv" name="thn_inv"
                                    placeholder="Tahun Inventaris" required>
                            </div>
                            <div class="row mb-3">
                                <label class="mb-2 fw-medium">Asal Buku</label>
                                <select name="asl_id" class="form-control" required>
                                    @foreach ($dataasal as $asal)
                                        <option value="{{ $asal->id }}">{{ $asal->id }} - {{ $asal->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="row mb-3">
                                <label class="mb-2 fw-medium">Kategori / Jenis Buku</label>
                                <select name="ktg_id" class="form-control" required>
                                    @foreach ($datakat as $kategori)
                                        <option value="{{ $kategori->id }}">{{ $kategori->id }} - {{ $kategori->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="row mb-3">
                                <label class="mb-2 fw-medium">Bahasa Buku</label>
                                <select name="bhs_id" class="form-control" required>
                                    @foreach ($databahasa as $bahasa)
                                        <option value="{{ $bahasa->id }}">{{ $bahasa->id }} - {{ $bahasa->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="ol-12 col-sm-6 ms-0 ms-sm-3">
                            <div class="row mb-3">
                                <label class="mb-2 fw-medium">Tahun Terbit</label>
                                <input type="number" class="form-control" name="tahun_terbit"
                                    placeholder="Tahun Terbit" required id="thn_ter">
                            </div>
                            <div class="row mb-3">
                                <label class="mb-2 fw-medium">Sinopsis</label>
                                <textarea name="sinopsis" cols="30" rows="5" class="form-control"></textarea>
                            </div>
                            <div class="row mb-3">
                                <label class="mb-2 fw-medium">Keterangan <span class="text-danger">(*Beri Tanda - Jika Tidak Ada)</span></label>
                                <input type="text" class="form-control" placeholder="Keterangan" name="ket"
                                    value="-" required>
                            </div>
                            <div class="row mb-3">
                                <label class="mb-2 fw-medium">Foto</label>
                                <input type="file" class="form-control" name="photo">
                            </div>
                            <div class="row mb-3">
                                <label class="mb-2 fw-medium">Jumlah Buku</label>
                                <input type="number" class="form-control" name="jumlah" id="jml"
                                    placeholder="Jumlah Buku" required>
                            </div>
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

    <!-- Modal Import -->
    <div class="modal fade" id="import" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
        aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="staticBackdropLabel">Import Data Buku</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="" method="POST" enctype="multipart/form-data">
                    <div class="modal-body p-4">
                        <div class="row mb-3">
                            <label class="mb-2 fw-medium">Import Data <span class="text-danger">(*Excel)</span></label>
                            <input type="file" name="file" class="form-control">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-success">Import</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Edit -->
    @foreach ($databuku as $buku)
        <div class="modal fade" id="edit{{ $buku->isbn }}" data-bs-backdrop="static" data-bs-keyboard="false"
            tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-sm-md">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="staticBackdropLabel">Edit Buku</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="/updatebukus" method="POST" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="oldphoto" value="{{ $buku->photo }}">
                        <input type="hidden" name="role" value="petugas">
                        <input type="hidden" name="id" value="{{ $buku->id }}">

                        <div class="modal-body ps-4 pe-5 py-3 d-block d-sm-flex">
                            <div class="col-12 col-sm-6">
                                <div class="row mb-3">
                                    <label class="mb-2 fw-medium">ISBN</label>
                                    <input type="text" class="form-control" placeholder="ISBN" name="isbn"
                                        required value="{{ $buku->isbn }}">
                                </div>
                                <div class="row mb-3">
                                    <label class="mb-2 fw-medium">Pengarang</label>
                                    <input type="text" class="form-control" placeholder="Pengarang" name="pengarang"
                                        required value="{{ $buku->pengarang }}">
                                </div>
                                <div class="row mb-3">
                                    <label class="mb-2 fw-medium">Judul</label>
                                    <input type="text" class="form-control" placeholder="Judul" name="judul"
                                        required value="{{ $buku->judul }}">
                                </div>
                                <div class="row mb-3">
                                    <label class="mb-2 fw-medium">Tahun Inventaris</label>
                                    <input type="number" class="form-control" id="thn_inv" name="thn_inv"
                                        placeholder="Tahun Inventaris" required value="{{ $buku->thn_inv }}">
                                </div>
                                <div class="row mb-3">
                                    <label class="mb-2 fw-medium">Asal Buku</label>
                                    <select name="asl_id" class="form-control" required>
                                        @foreach ($dataasal as $asal)
                                            <option value="{{ $asal->id }}"
                                                {{ $buku->asl_id == $asal->id ? 'selected' : '' }}>{{ $asal->id }} -
                                                {{ $asal->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="row mb-3">
                                    <label class="mb-2 fw-medium">Kategori / Jenis Buku</label>
                                    <select name="ktg_id" class="form-control" required>
                                        @foreach ($datakat as $kategori)
                                            <option value="{{ $kategori->id }}"
                                                {{ $buku->ktg_id == $kategori->id ? 'selected' : '' }}>{{ $kategori->id }}
                                                - {{ $kategori->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="row mb-3">
                                    <label class="mb-2 fw-medium">Bahasa Buku</label>
                                    <select name="bhs_id" class="form-control" required>
                                        @foreach ($databahasa as $bahasa)
                                            <option value="{{ $bahasa->id }}"
                                                {{ $buku->bhs_id == $bahasa->id ? 'selected' : '' }}>{{ $bahasa->id }} -
                                                {{ $bahasa->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="ol-12 col-sm-6 ms-0 ms-sm-3">
                                <div class="row mb-3">
                                    <label class="mb-2 fw-medium">Tahun Terbit</label>
                                    <input type="number" class="form-control" name="tahun_terbit"
                                        placeholder="Tahun Terbit" required id="thn_ter"
                                        value="{{ $buku->tahun_terbit }}">
                                </div>
                                <div class="row mb-3">
                                    <label class="mb-2 fw-medium">Sinopsis</label>
                                    <textarea name="sinopsis" cols="30" rows="5" class="form-control">{{ $buku->sinopsis }}</textarea>
                                </div>
                                <div class="row mb-3">
                                    <label class="mb-2 fw-medium">Keterangan <span class="text-danger">(*Beri Tanda - Jika
                                            Tidak
                                            Ada)</span></label>
                                    <input type="text" class="form-control" placeholder="Keterangan" name="ket"
                                        value="{{ $buku->ket }}" required>
                                </div>
                                <div class="row mb-3">
                                    <label class="mb-2 fw-medium">Foto</label>
                                    <input type="file" class="form-control" name="photo">
                                </div>
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
    @foreach ($databuku as $buku)
        <div class="modal fade" id="hapus{{ $buku->isbn }}" data-bs-backdrop="static" data-bs-keyboard="false"
            tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="staticBackdropLabel">Hapus Buku {{ $buku->judul }} -
                            {{ $buku->isbn }}</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="/hapusbukus" method="POST">
                        @csrf
                        <input type="hidden" name="id" value="{{ $buku->isbn }}">
                        <input type="hidden" name="role" value="petugas">
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
        const numberInput1 = document.getElementById('eks');
        const numberInput2 = document.getElementById('thn_inv');
        const numberInput3 = document.getElementById('thn_ter');
        const numberInput4 = document.getElementById('jml');

        numberInput1.addEventListener('input', function() {
            let value = parseFloat(this.value);

            if (isNaN(value)) {
                // Handle non-numeric input
                this.value = 1;
            } else if (value < 1) {
                this.value = 1;
            }
        });

        numberInput2.addEventListener('input', function() {
            let value = parseFloat(this.value);

            if (isNaN(value)) {
                // Handle non-numeric input
                this.value = 1;
            } else if (value < 1) {
                this.value = 1;
            }
        });

        numberInput3.addEventListener('input', function() {
            let value = parseFloat(this.value);

            if (isNaN(value)) {
                // Handle non-numeric input
                this.value = 1;
            } else if (value < 1) {
                this.value = 1;
            }
        });

        numberInput4.addEventListener('input', function() {
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
