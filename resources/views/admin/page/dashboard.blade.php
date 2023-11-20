@extends('admin.layout.layout')

@section('content')
    <!-- Page Heading -->
    <div class="d-flex position-relative align-items-center justify-content-between p-4">
        <h1 class="h3 mb-0 text-gray-800 font-primary">Dashboard</h1>
        {{-- <a href="#" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm"><i
                                class="fas fa-download fa-sm text-white-50"></i> Generate Report</a> --}}
    </div>

    <!-- Content Row -->
    <div class="row p-2">

        <!-- Data Buku -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col">
                            <div class="font-weight-bold text-primary text-uppercase mb-1 ms-2">
                                Data Buku</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800 ms-2">{{ $jumlahbuku }}</div>
                        </div>
                        <div class="col-auto me-3">
                            <i class="fa-solid fa-book fa-2xl text-secondary"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Data Peminjaman -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col">
                            <div class="font-weight-bold text-info text-uppercase mb-1 ms-2">
                                Data Peminjaman</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800 ms-2">{{ $jumlahpeminjaman }}</div>
                        </div>
                        <div class="col-auto me-3">
                            <i class="fa-solid fa-book-bookmark fa-2xl text-secondary"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Data Anggota -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col">
                            <div class="font-weight-bold text-success text-uppercase mb-1 ms-2">
                                Data Anggota</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800 ms-2">{{ $jumlahanggota }}</div>
                        </div>
                        <div class="col-auto me-3">
                            <i class="fa-solid fa-user fa-2xl text-secondary"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Data Admin -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-danger shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col">
                            <div class="font-weight-bold text-danger text-uppercase mb-1 ms-2">
                                Data Admin</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800 ms-2">{{ $jumlahadmin }}</div>
                        </div>
                        <div class="col-auto me-3">
                            <i class="fa-solid fa-chalkboard-user fa-2xl text-secondary"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Content Row -->

        <div class="row">


        </div>

    </div>
@endsection
