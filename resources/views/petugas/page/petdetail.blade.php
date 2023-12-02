@extends('petugas.layout.petlayout')

@section('css')
    <style>
        .cari {
            width: 150px !important;
        }

        .pre-book {
            background-color: #ddc190;
            color: #002939;
        }

        .pre-book:hover {
            background-color: #002939;
            color: #ddc190;
            transition: 0.4s
        }

        .col-12 {
            background-color: #ddc190;
        }

        .pengarang,
        .title {
            color: #002939;
        }

        .container-right {
            background-color: #fff;
        }

        .dtl-pmj {
            font-size: 2rem;
            font-weight: 700;
        }

        @media screen and (min-width: 768px) {
            .cari {
                width: 200px !important;
            }
        }
    </style>
@endsection

@section('content')
    <!-- Page Heading -->
    <div class="d-flex position-relative align-items-center justify-content-between p-4">
        <h1 class="h3 mb-0 text-gray-800 font-primary">Peminjaman Buku</h1>

    </div>

    <div class="col-12 mt-4 rounded-3 p-4 d-block d-lg-flex shadow">
        <div class="col-lg-4 d-flex justify-content-start align-items-start">
            @foreach ($datbuku as $buku)
                <div class="card container-left shadow p-2" style="width: 18rem;">
                    <img src="{{ Storage::url('public/buku/' . $buku->photo) }}" class="card-img-top">
                    <div class="card-body">
                        <p class="text-secondary">ISBN : {{ $buku->isbn }}</p>
                        <h4 class="title">{{ $buku->judul }}</h4>
                        <h6 class="pengarang border-bottom pb-2">Pengarang : {{ $buku->pengarang }}</h6>
                        <h6 class="stok text-secondary">Stok : {{ $buku->jumlah }} Pcs</h6>
                    </div>
                </div>
            @endforeach
        </div>
        <div class="col-lg-8">
            <div class="container-right shadow mt-4 mt-lg-0 p-4 rounded">
                <h1 class="dtl-pmj text-secondary">Detail Peminjaman :</h1>
                <div class="right-body p-4">
                    @php
                        $today = date('Y-m-d');
                        $todays = date('Ymd');

                        foreach ($datbuku as $buku) {
                            $idbuku = $buku->isbn;

                            $oldqty = $buku->jumlah;
                        }

                    @endphp
                    <form action="/pinjambuku" method="POST">
                        @csrf
                        <input type="hidden" name="id_petugas" value="{{ $user->id }}">
                        <input type="hidden" name="old_qty" value="{{ $oldqty }}">
                        <input type="hidden" name="isbn" value="{{ $idbuku }}">
                        <input type="hidden" name="kode" value="{{ $custom }}">
                        <input type="hidden" name="kondisi" value="peminjaman">
                        <input type="hidden" name="role" value="petugas">
                        <div class="row mb-2 px-2">
                            <label class="mb-2 fw-medium">Kode Peminjaman</label>
                            <input type="text" class="form-control text-center" placeholder="Kode" name=""
                                value="{{ $custom }}" required disabled>
                        </div>
                        <div class="row mb-2 px-2">
                            <label class="mb-2 fw-medium">Buku</label>
                            @foreach ($datbuku as $buku)
                                @php
                                    $stok = $buku->jumlah;
                                @endphp

                                <input type="text" class="form-control text-center" placeholder="buku" name=""
                                    value="{{ $buku->isbn }} - {{ $buku->judul }} - {{ $buku->pengarang }}" required
                                    disabled>
                            @endforeach
                        </div>
                        <div class="row mb-2">
                            <label class="mb-2 fw-medium ms-2">Anggota Peminjam</label>
                            <select class="form-select" id="select_box" name="id_anggota">
                                <option value="">Pilih Anggota</option>
                                @foreach ($datanggota as $anggota)
                                    <option value="{{ $anggota->nisn }}">{{ $anggota->nisn }} - {{ $anggota->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="row mb-2 px-2">
                            <label class="mb-2 fw-medium">Tanggal Peminjaman</label>
                            <input type="date" class="form-control text-center" name="peminjaman"
                                value="{{ $today }}" min="{{ $today }}" required>
                        </div>
                        <div class="row mb-2 px-2">
                            <label class="mb-2 fw-medium">Tanggal Pengembalian</label>
                            <input type="date" class="form-control text-center" name="pengembalian"
                                min="{{ $today }}" required>
                        </div>
                        <div class="row mb-2 px-2">
                            <label class="mb-2 fw-medium">Jumlah Buku</label>
                            <input type="number" class="form-control text-center" name="qty"
                                placeholder="Jumlah Buku Yang Akan Dipinjam, Maximal {{ $buku->jumlah }}" min="1"
                                max="{{ $stok }}" id="jml" required>
                        </div>
                        <div class="d-flex justify-content-end mt-4">
                            <a href="/petpeminjaman" type="button" class="btn btn-secondary me-2">Batal</a>
                            <button type="submit" class="btn btn-success">Pinjam</button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>

    <script>
        const numberInput = document.getElementById('jml');
        const stok = {{ $stok }};

        numberInput.addEventListener('input', function() {
            let value = parseFloat(this.value);

            if (isNaN(value)) {
                // Handle non-numeric input
                this.value = 1;
            } else if (value < 1) {
                this.value = 1;
            } else if (value > stok) {
                this.value = stok;
            }
        });
    </script>
@endsection
