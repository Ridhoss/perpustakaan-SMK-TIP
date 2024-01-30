<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>ID Card - TIP Literation</title>

    {{-- logo atas --}}
    <link rel="shortcut icon" type="image/png" href="assets/img/logo/buku.png" />

    <link
        href="https://fonts.googleapis.com/css2?family=Merriweather:wght@400;700;900&family=Open+Sans:wght@300;400&display=swap"
        rel="stylesheet" />

    <style>
        :root {
            /* color */

            --primary: #002939;
            --secondary: #ddc190;

            /* font */

            --mainfont: "Open Sans", sans-serif;
            --headingfont: "Merriweather", serif;
        }

        * {
            margin: 00px;
            padding: 00px;
        }

        .container {
            height: 80vh;
            width: 100%;
            display: flex;
            /* align-items: center; */
            /* justify-content: space-around; */
            flex-wrap: wrap;
            box-sizing: border-box;
            flex-direction: row;
        }

        .padding {
            margin: 5px;
        }

        .font {
            height: 375px;
            width: 225px;
            position: relative;
            border-radius: 10px;
            background-image: url("assets/kartuanggota/bg.png");
            background-size: 225px 375px;
            background-repeat: no-repeat;
        }

        .companyname {
            color: White;

            padding: 10px;
            font-size: 25px;
        }

        .tab {
            padding-right: 30px;
        }

        .top img {
            height: 90px;
            width: 90px;
            background-color: var(--primary);
            border-radius: 57px;
            position: absolute;
            top: 75px;
            left: 68px;
            object-fit: content;
            border: 2px solid rgba(255, 255, 255, 0.2);
        }

        .ename {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 200px;
            height: auto;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            font-family: var(--headingfont);
        }

        .p1 {
            font-weight: bold;
            font-size: 16px;
        }

        .p2 {
            font-size: 10px;

        }


        .ename p {
            text-align: center;
            margin: 0;
            color: var(--primary);
            text-shadow: 2px 2px 4px var(--secondary);
        }

        .edetails {
            position: absolute;
            top: 225px;
            text-transform: capitalize;
            font-size: 12px;
            text-emphasis: spacing;
            margin-left: 10px;
            font-family: var(--mainfont);
        }

        .qr {
            position: absolute;
            top: 70%;
            height: 30px;
            width: 120px;
            margin: 20px 0px 0px 10px;
        }

        .top img {
            width: 90px;
            height: 90px;
            object-fit: cover;
        }

        .qr p {
            font-size: 10px;
            margin-bottom: 2px;
        }

        .edetails .Address {
            width: 60%;
            text-align: justify;
        }
    </style>
</head>

<body onafterprint="{{ $role == 'admin' ? "window.location='/anggota'" : "window.location='/petdataanggota'" }}">

    <div class="container">

        {{-- foreach here --}}

        @foreach ($anggotas as $anggota)
            <div class="padding">
                <div class="font">
                    <div class="top">
                        <img src="{{ Storage::url('public/anggota/' . $anggota->photo) }}">
                    </div>
                    <div class="">
                        <div class="ename">
                            <p class="p1">{{ $anggota->name }}</p>
                            <p class="p2">NIS : {{ $anggota->nisn }}</p>
                        </div>
                        <div class="edetails">
                            <p><b>Tanggal Lahir :</b>
                                {{ \Carbon\Carbon::createFromFormat('Y-m-d', $anggota->date)->format('d F Y') }}</p>
                            <p><b>Jenis Kelamin :</b>
                                @if ($anggota->gender == "P")
                                    Perempuan
                                @else
                                    Laki - Laki
                                @endif
                            </p>
                            <p><b>Nomor :</b> {{ $anggota->phone }}</p>
                        </div>

                        <div class="qr">
                            <p>Barcode :</p>
                            {!! DNS1D::getBarcodeHTML($anggota->nisn, 'C39', 1, 30) !!}
                        </div>
                    </div>
                </div>
            </div>
        @endforeach

        {{-- end foreach --}}

    </div>

    <script>
        window.print();
    </script>

</body>

</html>
