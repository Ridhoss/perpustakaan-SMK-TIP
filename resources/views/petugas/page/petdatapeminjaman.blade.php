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
        <h1 class="h3 mb-0 text-gray-800">Data Peminjaman</h1>

        {{-- alerts --}}

        @if (session()->has('notifubah'))
            <div class="alert alert-success alert-dismissible fade show position-absolute top-0 end-0" role="alert">
                <strong>Data Successfully!</strong> Buku Telah Dikembalikan!
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        @if (session()->has('notifhapus'))
            <div class="alert alert-danger alert-dismissible fade show position-absolute top-0 end-0" role="alert">
                <strong>Data Successfully!</strong> Peminjaman Berhasil Di Hapus!
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        @error('isbn')
            <div class="alert alert-danger alert-dismissible fade show position-absolute top-0 end-0" role="alert">
                <strong>Data Failed!</strong> {{ $message }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @enderror

        {{-- end alert --}}
        {{-- button add --}}
        <a class="mt-0 mt-sm-0 btn btn-sm btn-primary shadow-sm" data-bs-toggle="modal" data-bs-target="#tambah">Laporan</a>
    </div>

    {{-- cari --}}
    <form action="/petdatapeminjaman" method="get">
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
                    <th>Kode Peminjaman</th>
                    <th>Buku</th>
                    <th>Peminjam</th>
                    <th>Tanggal Pinjam</th>
                    <th>Tanggal Kembali</th>
                    <th>Jumlah Pinjam</th>
                    <th>Petugas</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $no = 1;
                    $stok = 0;
                @endphp
                @foreach ($datapeminjaman as $peminjaman)
                    @php

                        $stok = $peminjaman->qty;

                    @endphp
                    <tr>
                        <td>{{ $no++ }}</td>
                        <td>{{ $peminjaman->kode }}</td>
                        <td>{{ $peminjaman->judul }}</td>
                        <td>{{ $peminjaman->anggota }}</td>
                        <td>{{ $peminjaman->tgl_pinjam }}</td>
                        <td>{{ $peminjaman->tgl_kembali }}</td>
                        <td>{{ $peminjaman->qty }}</td>
                        <td>{{ $peminjaman->petugas }}</td>
                        <td>
                            @if ($peminjaman->status == 'dipinjam')
                                <button class="btn btn-outline-secondary btn-sm mb-2" data-bs-toggle="modal"
                                    data-bs-target="#kembali{{ $peminjaman->id }}">Dipinjam</button>
                            @elseif($peminjaman->status == 'dikembalikan')
                                <button class="btn btn-outline-success btn-sm mb-2" data-bs-toggle="modal"
                                    data-bs-target="#kembali{{ $peminjaman->id }}" disabled>Dikembalikan</button>
                            @elseif($peminjaman->status == 'dihapus')
                                <button class="btn btn-outline-danger btn-sm mb-2" data-bs-toggle="modal"
                                    data-bs-target="#kembali{{ $peminjaman->id }}">Pengembalian Dihapus</button>
                            @endif
                        </td>
                        <td>
                            <button class="btn btn-danger btn-sm mb-2 w-100" data-bs-toggle="modal"
                                data-bs-target="#hapus{{ $peminjaman->id }}">Hapus</button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Modal Pengembalian -->
    @foreach ($datapeminjaman as $peminjaman)
        <div class="modal fade" id="kembali{{ $peminjaman->id }}" data-bs-backdrop="static" data-bs-keyboard="false"
            tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="staticBackdropLabel">Apakah anda yakin akan merubah status
                            peminjaman?</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="/ubahstatuspeminjaman" method="POST">
                        @csrf
                        <input type="hidden" name="kode" value="{{ $peminjaman->kode }}">
                        <input type="hidden" name="qtypinjam" value="{{ $peminjaman->qty }}">
                        <input type="hidden" name="id_petugas" value="{{ $user->id }}">
                        <input type="hidden" name="tgl_kembali" value="{{ $peminjaman->tgl_kembali }}">
                        <input type="hidden" name="stokbuku" value="{{ $peminjaman->stok }}">
                        <input type="hidden" name="idbuku" value="{{ $peminjaman->idbuku }}">
                        <input type="hidden" name="kondisi" value="petugas">
                        <div class="modal-body p-4">
                            <div class="row mb-3">
                                <label class="mb-2 fw-medium">Tanggal Kembali</label>
                                <input type="date" class="form-control" name="tgl_pengembalian"
                                    value="{{ date('Y-m-d') }}" required>
                            </div>
                            <div class="row mb-3">
                                <label class="mb-2 fw-medium">Jumlah Buku</label>
                                <input type="number" class="form-control" placeholder="Jumlah Buku Yang Dikembalikan"
                                    name="qtykembali" id="jml" required>
                            </div>
                            <div class="row mb-3">
                                <label class="mb-2 fw-medium">Keterangan</label>
                                <textarea name="keterangan" cols="30" rows="5" class="form-control" required></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-primary">Dikembalikan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endforeach

    <!-- Modal Hapus -->
    @foreach ($datapeminjaman as $peminjaman)
        <div class="modal fade" id="hapus{{ $peminjaman->id }}" data-bs-backdrop="static" data-bs-keyboard="false"
            tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="staticBackdropLabel">Hapus Peminjaman {{ $peminjaman->kode }}
                        </h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="/hapuspeminjaman" method="POST">
                        @csrf
                        <input type="hidden" name="kondisi" value="petugas">
                        <input type="hidden" name="id" value="{{ $peminjaman->kode }}">
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
        const numberInput = document.getElementById('jml');
        const stok = {{ $stok }};

        numberInput.addEventListener('input', function() {
            let value = parseFloat(this.value);

            if (isNaN(value)) {
                // Handle non-numeric input
                this.value = 1;
            } else if (value < 0) {
                this.value = 0;
            } else if (value > stok) {
                this.value = stok;
            }
        });
    </script>
@endsection
