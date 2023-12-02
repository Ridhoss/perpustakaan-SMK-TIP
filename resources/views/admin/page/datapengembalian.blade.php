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
        <h1 class="h3 mb-0 text-gray-800 font-primary">Data Pengembalian</h1>

        {{-- alerts --}}

        @if (session()->has('notifhapus'))
            <div class="alert alert-danger alert-dismissible fade show position-absolute top-0 end-0" role="alert">
                <strong>Data Successfully!</strong> Pengembalian Berhasil Di Hapus!
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        @if (session()->has('notifubah'))
            <div class="alert alert-success alert-dismissible fade show position-absolute top-0 end-0" role="alert">
                <strong>Data Successfully!</strong> Buku Telah Dikembalikan!
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
        <div class="pre-btn d-flex flex-column flex-sm-row">
            {{-- <button class="btn btn-success btn-sm me-0 me-sm-2 mb-2 mb-sm-0" data-bs-toggle="modal"
                data-bs-target="#print">Print</button> --}}
            <a class="mt-0 mt-sm-0 btn btn-sm btn-primary shadow-sm" data-bs-toggle="modal"
                data-bs-target="#pengembalian">Pengembalian</a>
        </div>
    </div>

    {{-- cari --}}
    <form action="/petdatapengembalian" method="get">
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
                    <th>Tanggal Wajib Kembali</th>
                    <th>Tanggal Kembali</th>
                    <th>Jumlah Denda</th>
                    <th>Jumlah Buku Kembali</th>
                    <th>Keterangan</th>
                    <th>Petugas</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $no = 1;
                @endphp
                @foreach ($datapengembalian as $pengembalian)
                    <tr>
                        <td>{{ $no++ }}</td>
                        <td>{{ $pengembalian->kode }}</td>
                        <td>{{ $pengembalian->kembaliwajib }}</td>
                        <td>{{ $pengembalian->tgl_kembali }}</td>
                        <td>Rp. {{ number_format($pengembalian->denda, 0, ',', '.') }}</td>
                        <td>{{ $pengembalian->qty }}</td>
                        <td>{{ $pengembalian->keterangan }}</td>
                        <td>{{ $pengembalian->petugas }}</td>
                        </td>
                        <td>
                            <button class="btn btn-danger btn-sm mb-2 w-100" data-bs-toggle="modal"
                                data-bs-target="#hapus{{ $pengembalian->id }}">Hapus</button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Modal Hapus -->
    @foreach ($datapengembalian as $pengembalian)
        <div class="modal fade" id="hapus{{ $pengembalian->id }}" data-bs-backdrop="static" data-bs-keyboard="false"
            tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="staticBackdropLabel">Hapus Pengembalian {{ $pengembalian->kode }}
                        </h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="/hapuspengembalian" method="POST">
                        @csrf
                        <input type="hidden" name="kondisi" value="admin">
                        <input type="hidden" name="id" value="{{ $pengembalian->kode }}">
                        <input type="hidden" name="qtykembali" value="{{ $pengembalian->qty }}">
                        <input type="hidden" name="isbn" value="{{ $pengembalian->isbn }}">
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-danger">Hapus</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endforeach

    <!-- Modal Pengembalian -->
    @foreach ($datapeminjaman as $peminjaman)
        <div class="modal fade" id="pengembalian" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
            aria-labelledby="staticBackdropLabel" aria-hidden="true">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="staticBackdropLabel">Pengembalian Buku</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="/ubahstatuspeminjaman" method="POST">
                        <div class="modal-body p-4">
                            <table id="myTable" class="display">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Kode Peminjaman</th>
                                        <th>Buku</th>
                                        <th>NISN</th>
                                        <th>Peminjam</th>
                                        <th>Tanggal Pinjam</th>
                                        <th>Tanggal Kembali</th>
                                        <th>Jumlah Pinjam</th>
                                        <th>Petugas</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $no = 1;
                                    @endphp
                                    @foreach ($datapeminjaman as $peminjaman)
                                        <tr>
                                            <td>{{ $no++ }}</td>
                                            {{-- <td>{!! DNS1D::getBarcodeHTML("$peminjaman->kode",'C39',1,50) !!}</td> --}}
                                            <td>{{ $peminjaman->kode }}</td>
                                            <td>{{ $peminjaman->judul }}</td>
                                            <td>{{ $peminjaman->nisn }}</td>
                                            <td>{{ $peminjaman->anggota }}</td>
                                            <td>{{ $peminjaman->tgl_pinjam }}</td>
                                            <td>{{ $peminjaman->tgl_kembali }}</td>
                                            <td>{{ $peminjaman->qty }}</td>
                                            <td>{{ $peminjaman->petugas }}</td>
                                            <td>
                                                <form action="/ubahstatuspeminjaman" method="POST">
                                                    @csrf
                                                    <input type="hidden" name="kode" value="{{ $peminjaman->kode }}">
                                                    <input type="hidden" name="qtypinjam"
                                                        value="{{ $peminjaman->qty }}">
                                                    <input type="hidden" name="id_petugas" value="{{ $user->id }}">
                                                    <input type="hidden" name="tgl_kembali"
                                                        value="{{ $peminjaman->tgl_kembali }}">
                                                    <input type="hidden" name="isbn"
                                                        value="{{ $peminjaman->isbn }}">
                                                    <input type="hidden" name="nisn"
                                                        value="{{ $peminjaman->nisn }}">
                                                    <input type="hidden" name="kondisi" value="admin">


                                                    @if ($peminjaman->status == 'dipinjam')
                                                        <button class="btn btn-primary btn-sm mb-2"
                                                            type="submit">Kembali</button>
                                                    @elseif($peminjaman->status == 'dihapus')
                                                        <button class="btn btn-outline-danger btn-sm mb-2"
                                                            type="submit">Pengembalian
                                                            Dihapus</button>
                                                    @endif
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                            {{-- <button type="submit" class="btn btn-primary">Dikembalikan</button> --}}
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endforeach

    <script>
        $(document).ready(function() {
            new DataTable("#myTable");
        });
    </script>
@endsection
