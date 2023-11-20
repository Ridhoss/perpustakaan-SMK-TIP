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

    <!-- title -->
    <title>Register - TIP Literation</title>
</head>

<body>

    <div class="icon-back">
        <a href="/"><i class="fa-solid fa-circle-chevron-left ms-4 mt-4"></i></a>
    </div>


    <!----------------------- Main Container -------------------------->

    <div class="container d-flex justify-content-center align-items-center min-vh-100">

        @error('username')
            <div class="alert alert-danger alert-dismissible fade show position-absolute top-0 end-0 mt-2 me-3"
                role="alert">
                <strong>Data Failed!</strong> {{ $message }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @enderror
        @error('name')
            <div class="alert alert-danger alert-dismissible fade show position-absolute top-0 end-0 mt-2 me-3"
                role="alert">
                <strong>Data Failed!</strong> {{ $message }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @enderror
        @error('password')
            <div class="alert alert-danger alert-dismissible fade show position-absolute top-0 end-0 mt-2 me-3"
                role="alert">
                <strong>Data Failed!</strong> {{ $message }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @enderror
        @error('email')
            <div class="alert alert-danger alert-dismissible fade show position-absolute top-0 end-0 mt-2 me-3"
                role="alert">
                <strong>Data Failed!</strong> {{ $message }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @enderror


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

                    <form action="/regadmin" method="post">
                        @csrf
                        <div class="header-text mb-4">
                            <h2>Register</h2>
                            <p>Sign Up and join with us.</p>
                        </div>

                        <div class="input-group mb-3">
                            <input type="text" class="form-control form-control-lg bg-light fs-6"
                                placeholder="Username" id="username" name="username">
                        </div>

                        <div class="input-group mb-3">
                            <input type="text" class="form-control form-control-lg bg-light fs-6" placeholder="Nama"
                                id="nama" name="name">
                        </div>

                        <div class="input-group mb-3">
                            <input type="password" class="form-control form-control-lg bg-light fs-6"
                                placeholder="Password" id="password" name="password">
                        </div>

                        <div class="input-group mb-4">
                            <input type="text" class="form-control form-control-lg bg-light fs-6" placeholder="Email"
                                id="email" name="email">
                        </div>

                </div>

                <div class="input-group mb-3">
                    <button class="btn-log btn btn-lg w-100 fs-6">Sign Up</button>
                </div>

                </form>

                <!-- <div class="input-group mb-3">
                    <button class="btn btn-lg btn-light w-100 fs-6"><img src="images/google.png" style="width:20px" class="me-2"><small>Sign In with Google</small></button>
                </div> -->

                <div class="row">
                    <p>Already have an account ? <a href="/login">Sign In</a></p>
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
