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
        <h1 class="h3 mb-0 text-gray-800 font-primary">Pengembalian</h1>

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
        {{-- <a class="mt-0 mt-sm-0 btn btn-sm btn-primary shadow-sm" data-bs-toggle="modal" data-bs-target="#tambah">Laporan</a> --}}
    </div>

    {{-- cari --}}
    <form action="/petpengembalian" method="get">
        <div class="d-flex justify-content-end mb-4">
            <input type="search" name="cari" class="cari form-control me-3" placeholder="Cari"
                value="{{ $lastquery }}" id="cari">
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
                    <th>Tanggal Pinjam</th>
                    <th>Tanggal Kembali</th>
                    <th>Jumlah Buku</th>
                    <th>Petugas</th>
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
                        <td>{{ $peminjaman->anggota }}</td>
                        <td>{{ $peminjaman->tgl_pinjam }}</td>
                        <td>{{ $peminjaman->tgl_kembali }}</td>
                        <td>{{ $peminjaman->jumlah }}</td>
                        <td>{{ $peminjaman->petugas }}</td>
                        <td>
                            <form action="/petdetailpengembalian" method="POST">
                                @csrf
                                <input type="hidden" name="kode" value="{{ $peminjaman->kode }}">
                                <button class="btn btn-primary text-white btn-sm" type="submit">Kembalikan</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <script>
        window.onload = function() {

            // focus cari
            var inputElement = document.getElementById("cari");
            inputElement.focus();

            var aktif = document.getElementById("pengem");
            aktif.classList.add('aktif');

        };
    </script>


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
