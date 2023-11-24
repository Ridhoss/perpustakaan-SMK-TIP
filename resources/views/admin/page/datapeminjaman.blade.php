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
        <h1 class="h3 mb-0 text-gray-800 font-primary">Data Peminjaman</h1>

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

        {{-- end alert --}}
        {{-- button add --}}
        <a class="mt-0 mt-sm-0 btn btn-sm btn-primary shadow-sm" data-bs-toggle="modal" data-bs-target="#laporan">Laporan</a>
    </div>

    {{-- cari --}}
    <form action="/peminjaman" method="get">
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
                    <th>Bukti</th>
                    <th>Action</th>
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
                        <td>{{ $peminjaman->anggota }}</td>
                        <td>{{ $peminjaman->tgl_pinjam }}</td>
                        <td>{{ $peminjaman->tgl_kembali }}</td>
                        <td>{{ $peminjaman->qty }}</td>
                        <td>{{ $peminjaman->petugas }}</td>
                        <td>
                            @if ($peminjaman->status == 'dipinjam')
                                <button class="btn btn-outline-secondary btn-sm mb-2" data-bs-toggle="modal"
                                    data-bs-target="#kembali{{ $peminjaman->id }}" disabled>Dipinjam</button>
                            @elseif($peminjaman->status == 'dikembalikan')
                                <button class="btn btn-outline-success btn-sm mb-2" data-bs-toggle="modal"
                                    data-bs-target="#kembali{{ $peminjaman->id }}" disabled>Dikembalikan</button>
                            @elseif($peminjaman->status == 'dihapus')
                                <button class="btn btn-outline-danger btn-sm mb-2" data-bs-toggle="modal"
                                    data-bs-target="#kembali{{ $peminjaman->id }}" disabled>Pengembalian Dihapus</button>
                            @endif
                        </td>
                        <td>
                            <form action="/pinjambuku" method="POST">
                                @csrf
                                <input type="hidden" name="kondisi" value="print">
                                <input type="hidden" name="role" value="admin">
                                <input type="hidden" name="kode" value="{{ $peminjaman->kode }}">
                                <input type="hidden" name="id_petugas" value="{{ $peminjaman->petid }}">
                                <input type="hidden" name="isbn" value="{{ $peminjaman->isbn }}">
                                <input type="hidden" name="id_anggota" value="{{ $peminjaman->agtid }}">
                                <input type="hidden" name="peminjaman" value="{{ $peminjaman->tgl_pinjam }}">
                                <input type="hidden" name="pengembalian" value="{{ $peminjaman->tgl_kembali }}">
                                <input type="hidden" name="old_qty" value="0">
                                <input type="hidden" name="qty" value="{{ $peminjaman->qty }}">
                                <button class="btn btn-success btn-sm" type="submit">Print</button>
                            </form>
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
                        <input type="hidden" name="kondisi" value="admin">
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

    <!-- Modal Laporan-->
    <div class="modal fade" id="laporan" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
        aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="staticBackdropLabel">Print Laporan Peminjaman</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
                        id="close2"></button>
                </div>
                <form action="/printlaporan" method="post">
                    @csrf
                    <div class="modal-body p-4">
                        <div class="row">
                            <label class="mb-2">Mulai Dari Tanggal</label>
                            <input type="date" class="form-control" name="Start" id="input">
                        </div>
                        <div class="row mt-3">
                            <label class="mb-2">Sampai Tanggal</label>
                            <input type="date" class="form-control" name="End" id="input2">
                        </div>
                        <div class="row mt-3">
                            <label class="mb-2">Laporan Berdasarkan Anggota</label>
                            {{-- <input type="text" class="form-control" name="username" id="username" placeholder="Search By Username"> --}}
                            <select name="username" id="" class="form-select">
                                <option value="">-- Print By User --</option>
                                @foreach ($anggotas as $anggota)
                                    <option value="{{ $anggota->name }}">{{ $anggota->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"
                            id="close">Close</button>
                        <button type="submit" class="btn btn-primary">Print</button>
                    </div>
                </form>
            </div>
        </div>
    </div>



    <script>
        const numberInputs = document.querySelectorAll('.jml');

        numberInputs.forEach(numberInput => {
            numberInput.addEventListener('input', function() {
                let value = parseFloat(this.value);
                const stok = parseInt(this.getAttribute('data-stok'));

                if (isNaN(value)) {
                    this.value = 1;
                } else if (value <= 0) {
                    this.value = 0;
                } else if (value > stok) {
                    this.value = stok;
                }
            });
        });
    </script>
@endsection
