<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Laporan Peminjaman - TIP Literation</title>
    {{-- bootstrap --}}
    <link rel="stylesheet" href="assets/bootstrap/css/bootstrap.min.css">

    <style>

    </style>
</head>

<body onafterprint="window.location='/peminjaman'">
    <div class="container-fluid">
        <h1 class="text-center mt-5">TIP Literation</h1>
        <p class="text-center">Jl. Ciseupan No.269, Cibeber, Kec. Cimahi Sel., Kota Cimahi, Jawa
            Barat 40531</p>

        <div class="row mt-5">
            <p>Date : {{ \Carbon\Carbon::createFromFormat('Y-m-d', $start)->format('d F Y') }} -
                {{ \Carbon\Carbon::createFromFormat('Y-m-d', $end)->format('d F Y') }} </p>
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
                    </tr>
                </thead>
                <tbody>
                    @php
                        $no = 1;
                    @endphp
                    @foreach ($peminjamans as $pinjam)
                        <tr>
                            <td>{{ $no++ }}</td>
                            <td>{{ $pinjam->kode }}</td>
                            <td>{{ $pinjam->judul }}</td>
                            <td>{{ $pinjam->anggota }}</td>
                            <td>{{ \Carbon\Carbon::createFromFormat('Y-m-d', $pinjam->tgl_pinjam)->format('d-m-Y') }}
                            </td>
                            <td>{{ \Carbon\Carbon::createFromFormat('Y-m-d', $pinjam->tgl_kembali)->format('d-m-Y') }}
                            </td>
                            <td>{{ $pinjam->qty }}</td>
                            <td>{{ $pinjam->petugas }}</td>
                        </tr>
                    @endforeach
                </tbody>

            </table>
        </div>
    </div>


    <script>
        window.print();
    </script>

    {{-- bootstrap --}}
    <script src="assets/bootstrap/js/bootstrap.min.js"></script>

</body>

</html>
