<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>TIP Literation</title>

    {{-- logo atas --}}
    <link rel="shortcut icon" type="image/png" href="assets/img/logo/buku.png" />

    <!-- bootstrap css -->
    <link rel="stylesheet" href="assets/bootstrap/css/bootstrap.min.css" />

    <!-- font -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
        href="https://fonts.googleapis.com/css2?family=Merriweather:wght@400;700;900&family=Open+Sans:wght@300;400&display=swap"
        rel="stylesheet" />

    <!-- icons -->
    <link rel="stylesheet" href="assets/icon/css/all.min.css">

    <!-- swipper -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />

    <!-- vanilla css -->
    <link rel="stylesheet" href="assets/landing/css/style.css" />
</head>

<body>

    <nav class="navbar navbar-expand-lg navbar-dark fixed-top py-3">
        <div class="container">
            <a class="navbar-brand" href="#"><span>TIP</span>Literation</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavAltMarkup"
                aria-controls="navbarNavAltMarkup" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse d-lg-flex justify-content-lg-end align-items-lg-center"
                id="navbarNavAltMarkup">
                <div class="navbar-nav me-5">
                    <a class="nav-link me-4 link-atas active" href="#home">Home</a>
                    <a class="nav-link me-4 link-atas" href="#service">Service</a>
                    <a class="nav-link me-4 link-atas" href="#product">Book</a>
                </div>
                <div class="icons-home mt-4 mt-lg-0">
                    <a href="/login"><i class="fa-solid fa-right-to-bracket ms-3 mt-1"></i></a>
                </div>
            </div>
        </div>
    </nav>

    <!-- home -->
    <section class="home" id="home">
        <div class="container" id="con">
            <div class="row" id="ro">
                <div class="col-lg-6 d-lg-flex flex-lg-column justify-content-lg-end content-left">
                    <h1 class="heading">TIP Literation</h1>
                    <p class="subheading text-white">
                        Intelligence, Diligence, Cleverness
                    </p>
                    {{-- <div class="btn-home mt-5">
                        <a href="" class="btn btn-learn py-2 px-3 px-lg-4 py-lg-3">Learn More</a>
                    </div> --}}
                </div>
                <div class="col-lg-6 position-relative d-none d-lg-block">
                    <img src="assets/img/logo/buku.png" class="img-fluid position-absolute" alt="" />
                </div>
            </div>
        </div>
    </section>

    <!-- service -->
    <section class="service section-margin" id="service">
        <div class="container">
            <div class="row">
                <div class="col text-center">
                    <h1 class="heading">Our Service</h1>
                    <p class="subheading">
                        Berikut Merupakan Kampus - Kampus SMK TI Pembangunan
                    </p>
                </div>
            </div>

            <div class="row mt-5">
                <div class="col-lg-6">
                    <div class="card-service rounded d-flex justify-content-between align-items-center p-4">
                        <div class="detail">
                            <h3 class="heading quote">SMK TI Pembangunan Kampus II</h3>
                            <p class="label mt-4">Jl. H. Bakar, Utama, Kec. Cimahi Sel., Kota Cimahi, Jawa Barat 40521
                            </p>
                            <a href="https://maps.app.goo.gl/n63GHK5eWx8spCva8" target="_blank"
                                class="btn-service btn mt-4">About</a>
                        </div>
                        <div class="img-service ms-4">
                            <img src="assets/img/logo/logo-ti.png" alt="" width="200" />
                        </div>
                    </div>
                </div>

                <div class="col-lg-6 my-3 my-lg-0">
                    <div class="card-service rounded d-flex justify-content-between align-items-center p-4">
                        <div class="detail">
                            <h3 class="heading quote">SMK TI Pembangunan Kampus III</h3>
                            <p class="label mt-4">Jl. Ciseupan No.269, Cibeber, Kec. Cimahi Sel., Kota Cimahi, Jawa
                                Barat 40531</p>
                            <a href="https://maps.app.goo.gl/7jpb9Lwgs83S4zyS7" target="_blank"
                                class="btn-service btn mt-4">About</a>
                        </div>
                        <div class="img-service ms-4">
                            <img src="assets/img/logo/logo-ti.png" alt="" width="200" />
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <!-- banner -->
    <section class="banner1 section-margin px-2">
        <div class="container rounded-3 pt-5 pt-lg-0 h-100">
            <div class="row text-white h-100 d-lg-flex align-items-lg-center">
                <div class="col-lg-6">
                    <p class="label">~Ki Hajar Dewantara</p>
                    <h3 class="heading quote">"Ing ngarsa sung tulada, Ing madya mangun karsa, Tut Wuri Handayani"</h3>
                </div>
                <div class="col-lg-6">
                    <img src="assets/img/logo/logo2.png" class="img-fluid ms-5" />
                </div>
            </div>
        </div>
    </section>

    <!-- product -->
    <section class="product section-margin" id="product">
        <div class="container">
            <div class="row text-center">
                <div class="col">
                    <h1 class="heading">Buku Kami</h1>
                    <p class="subheading">Kami Menyediakan Buku - Buku Yang Menarik</p>
                </div>
            </div>

            <div class="row mt-4">
                <div class="col populer d-lg-flex justify-content-lg-between">
                    <h3 class="heading">Buku Kami</h3>
                    <div class="toggle-slider d-flex d-lg-block justify-content-end">
                        <i class="fa-solid fa-circle-chevron-left kiri"></i>
                        <i class="fa-solid fa-circle-chevron-right kanan"></i>
                    </div>
                </div>
            </div>

            <div class="row mt-4 product-populer">
                <div class="col">
                    <!-- Swiper -->
                    <div class="swiper mySwiperPopuler">
                        <div class="swiper-wrapper">

                            @foreach ($bukus as $buku)
                                <div class="swiper-slide card-product">
                                    <div
                                        class="img-box rounded-3 d-flex justify-content-center align-items-center py-4 px-2">
                                        <img src="{{ Storage::url('public/buku/' . $buku->photo) }}" alt=""
                                            class="img-fluid img-book" />
                                    </div>
                                    <div class="detail-product mt-3 d-flex justify-content-between">
                                        <div class="info">
                                            <p class="label text-center py-2 rounded">{{ $buku->judul }}</p>
                                            <p>{{ $buku->jumlah }} Buku</p>
                                        </div>
                                        {{-- <div class="btn-card">
                                            <a href="" class="btn">
                                                <i class="fa-solid fa-book"></i>
                                            </a>
                                        </div> --}}
                                    </div>
                                </div>
                            @endforeach

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer
        class="footer sticky-footer d-flex justify-content-center text-center justify-content-center align-items-center section-margin rounded-top-4">
        <p class="mt-4">Copyright &copy; Ridho Sulistyo.S 2023</p>
    </footer>
    <!-- End of Footer -->

    <!-- bootstrap js -->
    <script src="assets/bootstrap/js/bootstrap.min.js"></script>
    <!-- Bootstrap core JavaScript-->
    <script src="assets/admin/vendor/jquery/jquery.min.js"></script>
    <script src="assets/admin/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

    <!-- Core plugin JavaScript-->
    <script src="assets/admin/vendor/jquery-easing/jquery.easing.min.js"></script>

    {{-- another script --}}
    <script>
        $(document).ready(function() {
            // Mendeteksi scroll
            $(window).scroll(function() {
                var scrollPosition = $(window).scrollTop();
    
                // Mendapatkan posisi elemen bagian
                var homePosition = $('#home').offset().top;
                var servicePosition = $('#service').offset().top;
                var bookPosition = $('#product').offset().top;
    
                // Tentukan offset yang diperlukan sebelum menambahkan kelas "active" ke elemen "book"
                var offset = 50; // Anda dapat menyesuaikan offset ini sesuai kebutuhan
    
                // Hapus kelas "active" dari semua elemen navbar
                $('.nav-link').removeClass('active');
    
                // Tambahkan kelas "active" sesuai dengan bagian yang sedang ditampilkan dan offset
                if (scrollPosition >= bookPosition - offset) {
                    $('a[href="#product"]').addClass('active');
                } else if (scrollPosition >= servicePosition - offset) {
                    $('a[href="#service"]').addClass('active');
                } else if (scrollPosition >= homePosition - offset) {
                    $('a[href="#home"]').addClass('active');
                }
            });
        });
    </script>
    

    <!-- Swiper JS -->
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>

    <!-- vanilla script -->
    <script src="assets/landing/js/script.js"></script>
</body>

</html>
