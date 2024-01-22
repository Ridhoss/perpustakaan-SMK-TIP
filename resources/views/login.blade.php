<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- bootstrap -->
    <link rel="stylesheet" href="assets/bootstrap/css/bootstrap.min.css">

    <!-- vanilla css -->
    <link rel="stylesheet" href="assets/login/style.css">

    <!-- icon -->
    <script src="https://kit.fontawesome.com/904a972631.js" crossorigin="anonymous"></script>

    {{-- logo atas --}}
    <link rel="shortcut icon" type="image/png" href="assets/img/logo/buku.png" />

    <!-- title -->
    <title>Login - TIP Literation</title>
</head>

<body>


    <div class="icon-back">
        <a href="/"><i class="fa-solid fa-circle-chevron-left ms-4 mt-4"></i></a>
    </div>


    <!----------------------- Main Container -------------------------->

    <div class="container d-flex justify-content-center align-items-center min-vh-100">

        @if (session()->has('gallog'))
            <div class="alert alert-danger alert-dismissible fade show position-absolute top-0 end-0 mt-2 me-3"
                role="alert">
                <strong>Data Failed!</strong> Password Atau Username salah!
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        @if (session()->has('register'))
            <div class="alert alert-success alert-dismissible fade show position-absolute top-0 end-0 mt-2 me-3"
                role="alert">
                <strong>Data Successfully!</strong> Silahkan Login menggunakan akun anda!
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <!----------------------- Login Container -------------------------->

        <div class="row border rounded-5 p-3 bg-white shadow box-area">

            <!--------------------------- Left Box ----------------------------->

            <div class="left col-md-6 rounded-4 d-flex justify-content-center align-items-center flex-column left-box">
                <div class="featured-image mb-3">
                    <img src="assets/img/logo/logo2.png" class="img-fluid">
                </div>
                <!-- <p class="subheading text-wrap text-center d-none d-lg-block">"Berbahagialah untuk saat ini. Saat ini adalah hidupmu."</p> -->
            </div>

            <!-------------------- ------ Right Box ---------------------------->


            <div class="col-md-6 right-box">
                <div class="row align-items-center">

                    <form action="/loginadmin" method="post">
                        @csrf
                        <div class="header-text mb-4">
                            <h2>Hello,Again</h2>
                            <p>We are happy to have you back.</p>
                        </div>

                        <div class="input-group mb-3">
                            <input type="text" class="form-control form-control-lg bg-light fs-6"
                                placeholder="Username" id="username" name="username">
                        </div>

                        <div class="input-group mb-1">
                            <input type="password" class="form-control form-control-lg bg-light fs-6"
                                placeholder="Password" id="password" name="password">
                        </div>

                        <div class="input-group mb-5 d-flex justify-content-between">
                            <!-- <div class="form-check">
                        <input type="checkbox" class="form-check-input" id="formCheck">
                        <label for="formCheck" class="form-check-label text-secondary"><small>Remember Me</small></label>
                    </div> -->

                            <div class="forgot mt-2">
                                {{-- <a href="#">Forgot Password?</a> --}}
                            </div>

                        </div>

                        <div class="input-group mb-3">
                            <button class="btn-log btn btn-lg w-100 fs-6">Sign In</button>
                        </div>

                    </form>

                    <!-- <div class="input-group mb-3">
                    <button class="btn btn-lg btn-light w-100 fs-6"><img src="images/google.png" style="width:20px" class="me-2"><small>Sign In with Google</small></button>
                </div> -->

                    <div class="row">
                        {{-- <p>Don't have account ? <a href="/register">Sign Up</a></p> --}}
                    </div>
                </div>
            </div>

        </div>
    </div>

    <!-- js -->
    <script>

        const alertElement = document.querySelector('.alert');
        alertElement.classList.add('show');

        // Set timeout untuk menutup alert setelah 1 detik
        setTimeout(() => {
            // Tambahkan animasi fade out
            alertElement.classList.remove('show');
            alertElement.classList.add('fade');

            // Tunggu animasi fade out selesai, lalu hilangkan alert dari DOM
            setTimeout(() => {
                alertElement.remove();
            }, 3000); // Ubah angka timeout sesuai kebutuhan durasi animasi fade out
        }, 2000); // Ubah angka timeout sesuai kebutuhan waktu tampilan alert
    </script>

    <!-- bootstrap -->
    <script src="assets/bootstrap/js/bootstrap.min.js"></script>

</body>

</html>
