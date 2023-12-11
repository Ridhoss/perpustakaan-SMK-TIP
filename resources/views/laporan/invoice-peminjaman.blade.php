<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8" />
    <title>Bukti Peminjaman - TIP Literation</title>

    <style>
        .invoice-box {
            max-width: 800px;
            margin: auto;
            padding: 30px;
            border: 1px solid #eee;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.15);
            font-size: 16px;
            line-height: 24px;
            font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif;
            color: #555;
        }

        .invoice-box table {
            width: 100%;
            line-height: inherit;
            text-align: left;
        }

        .invoice-box table td {
            padding: 5px;
            vertical-align: top;
        }

        .invoice-box table tr td:nth-child(2) {
            text-align: right;
        }

        .invoice-box table tr.top table td {
            padding-bottom: 20px;
        }

        .invoice-box table tr.top table td.title {
            font-size: 45px;
            line-height: 45px;
            color: #333;
        }

        .invoice-box table tr.information table td {
            padding-bottom: 40px;
        }

        .invoice-box table tr.heading td {
            background: #eee;
            border-bottom: 1px solid #ddd;
            font-weight: bold;
        }

        .invoice-box table tr.details td {
            padding-bottom: 20px;
        }

        .invoice-box table tr.item td {
            border-bottom: 1px solid #eee;
        }

        .invoice-box table tr.item.last td {
            border-bottom: none;
        }

        .invoice-box table tr.total td:nth-child(2) {
            /* border-top: 2px solid #eee; */
            font-weight: bold;
        }

        .bold {
            font-weight: bold;
        }

        .pe {
            display: flex;
            flex-direction: column;
        }

        .barcode {
            margin-left: 200px;
        }

        @media only screen and (max-width: 600px) {
            .invoice-box table tr.top table td {
                width: 100%;
                display: block;
                text-align: center;
            }

            .invoice-box table tr.information table td {
                width: 100%;
                display: block;
                text-align: center;
            }
        }

        /** RTL **/
        .invoice-box.rtl {
            direction: rtl;
            font-family: Tahoma, 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif;
        }

        .invoice-box.rtl table {
            text-align: right;
        }

        .invoice-box.rtl table tr td:nth-child(2) {
            text-align: left;
        }
    </style>
</head>

<body onafterprint="{{ $role == 'admin' ? "window.location='/peminjaman'" : "window.location='/petdatapeminjaman'" }}">
    <div class="invoice-box">
        <table cellpadding="0" cellspacing="0">
            <tr class="top">
                <td colspan="2">
                    <table>
                        <tr>
                            <td class="title">
                                <img src="assets/img/logo/logo.png" style="width: 100%; max-width: 300px" />
                            </td>

                            <td class="pe">
                                <span class="bold">No Peminjaman : </span>{{ $kode }}<br />
                                <span class="bold">Tanggal Peminjaman :
                                </span>{{ \Carbon\Carbon::createFromFormat('Y-m-d', $tgl_pinjam)->format('d F Y') }}<br />
                                <span class="bold">Batas Peminjaman :
                                </span>{{ \Carbon\Carbon::createFromFormat('Y-m-d', $tgl_kembali)->format('d F Y') }}
                                {{-- <span class="barcode">{!! DNS1D::getBarcodeHTML("$kode",'C39',1,50) !!}</span> --}}
                            </td>

                        </tr>
                    </table>
                </td>
            </tr>

            <tr class="information">
                <td colspan="2">
                    <table>
                        <tr>
                            <td>
                                Jl. Ciseupan No.269,
                                <br>Cibeber, Kec. Cimahi Sel.,
                                <br>Kota Cimahi, Jawa Barat 40531
                            </td>

                            @foreach ($anggota as $user)
                                <td>
                                    <span class="bold">{{ $user->name }}</span>
                                    <br><span class="bold">NIS : </span>{{ $user->nisn }}
                                </td>
                            @endforeach
                        </tr>
                    </table>
                </td>
            </tr>

            {{-- <tr class="heading">
					<td>Payment Method</td>

					<td>Check #</td>
				</tr>

				<tr class="details">
					<td>Check</td>

					<td>1000</td>
				</tr> --}}

            <tr class="heading">
                <td>Item</td>

                <td>Qty</td>
            </tr>

            <tr class="item">
                @foreach ($buku as $bukus)
                    <td>{{ $bukus->judul }}</td>
                @endforeach

                <td>{{ $qty }}</td>
            </tr>

            @foreach ($petugas as $pet)
                <tr class="total">
                    <td></td>

                    <td>Petugas: {{ $pet->name }}</td>
                </tr>
            @endforeach
        </table>
        <div class="">
            <p class="bold">Barcode : </p>
            {!! DNS1D::getBarcodeHTML("$kode", 'C39', 1, 50) !!}
        </div>
    </div>

    <script>
        window.print();
    </script>

</body>

</html>
