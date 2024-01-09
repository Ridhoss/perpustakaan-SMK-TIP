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
                    <th>Peminjam</th>
                    <th>Tanggal Wajib Kembali</th>
                    <th>Tanggal Kembali</th>
                    <th>Jumlah Denda</th>
                    <th>Jumlah Buku Kembali</th>
                    <th>Keterangan</th>
                    <th>Petugas</th>
                    <th>Detail</th>
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
                        <td>{{ $pengembalian->anggota }}</td>
                        <td>{{ \Carbon\Carbon::createFromFormat('Y-m-d', $pengembalian->tglwajib)->format('d F Y') }}</td>
                        <td>{{ \Carbon\Carbon::createFromFormat('Y-m-d', $pengembalian->tgl_kembali)->format('d F Y') }}</td>
                        <td>Rp. {{ number_format($pengembalian->denda, 0, ',', '.') }}</td>
                        <td>{{ $pengembalian->qty }}</td>
                        <td>{{ $pengembalian->keterangan }}</td>
                        <td>{{ $pengembalian->petugas }}</td>
                        </td>
                        <td>
                            <form action="/printinvoice" method="GET">
                                @csrf
                                <input type="hidden" name="kode" value="{{ $pengembalian->kode }}">
                                <input type="hidden" name="role" value="petugas">
                                <input type="hidden" name="where" value="petpengembalian">
                                <button class="btn btn-warning text-white btn-sm" type="submit">Print</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Modal Hapus -->
    {{-- @foreach ($datapengembalian as $pengembalian)
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
                        <input type="hidden" name="kondisi" value="petugas">
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
    @endforeach --}}

    <script>
        window.onload = function() {

            var aktif = document.getElementById("data");
            var datgot = document.getElementById('datpeng')
            aktif.classList.add('aktif');
            datgot.classList.add('active');

        };
    </script>
@endsection
