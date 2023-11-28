<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Laporan Buku - TIP Literation</title>
    {{-- bootstrap --}}
    <link rel="stylesheet" href="assets/bootstrap/css/bootstrap.min.css">

    <style>

    </style>
</head>

<body onafterprint="window.location='/buku'">
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
                        <th>Tanggal</th>
                        <th>ISBN</th>
                        <th>Pengarang</th>
                        <th>Judul</th>
                        <th>Eksemplar</th>
                        <th>Tahun Inventaris</th>
                        <th>Asal Buku</th>
                        <th>Jenis Buku</th>
                        <th>Bahasa</th>
                        <th>No Inventaris</th>
                        <th>Tahun Terbit</th>
                        <th>Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $no = 1;
                    @endphp
                    @foreach ($databuku as $buku)
                        <tr>
                            <td>{{ $no++ }}</td>
                            <td>{{ $buku->tanggal }}</td>
                            <td>{{ $buku->isbn }}</td>
                            <td>{{ $buku->pengarang }}</td>
                            <td>{{ $buku->judul }}</td>
                            <td>{{ $buku->eks }}</td>
                            <td>{{ $buku->thn_inv }}</td>
                            <td>{{ $buku->asal }}</td>
                            <td>{{ $buku->kategori }}</td>
                            <td>{{ $buku->bahasa }}</td>
                            <td>{{ $buku->no_inv }}</td>
                            <td>{{ $buku->tahun_terbit }}</td>
                            <td>-</td>
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
