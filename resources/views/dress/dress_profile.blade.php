@extends('layouts.header')

@section('main')
@push('title')
<title> {{ $dress_data->dress_name ?? '' }}</title>
@endpush


    <div class="main-content">

        <div class="page-content">
            <div class="container-fluid">

                <!-- start page title -->
                <div class="row">
                    <div class="col-12">
                        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                            <h4 class="mb-sm-0 font-size-18">{{ $dress_data->dress_name ?? '' }}  </h4>

                            <div class="page-title-right">
                                <ol class="breadcrumb m-0">
                                    <li class="breadcrumb-item"><a href="#">Dress Profile  </a></li>
                                    <li class="breadcrumb-item active"><a href="{{ url('dress') }}">Customers</a></li>
                                </ol>
                            </div>

                        </div>
                    </div>
                </div>
                <!-- end page title -->

                <div class="row">
                    <div class="col-lg-8">
                        <div class="card">
                            <div class="card-body">
                                <div class="invoice-title">
                                    <div class="d-flex align-items-start">
                                        <div class="flex-grow-1">
                                            <div class="mb-4">
                                                <img src="{{ asset('images/logo-sm.svg') }}" alt=""
                                                    height="24"><span class="logo-txt">
                                                        {{ $dress_data->dress_name ?? '' }}</span>
                                            </div>
                                        </div>
                                        <input type="hidden" id="dress_id" value="{{ $dress_data->id ?? '' }}">
                                        <div class="flex-shrink-0">
                                            <div class="mb-4">
                                                <h4 class="float-end font-size-16">Dress Code - {{ $dress_data->sku ?? '' }} </h4>
                                            </div>
                                        </div>
                                    </div>

                                    <p class="mb-1">{{ $dress_data->address ?? '' }}</p>
                                    <p class="mb-1"><i class="fas fa-copyright"></i>
                                        {{ $category_data->category_name ?? '' }}</p>
                                    <p><i class="fas fa-palette"></i> {{ $color_data->color_name ?? '' }}
                                    </p>
                                </div>
                                <hr class="my-4">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xl-12">
                                <div class="card">
                                    <div class="card-header align-items-center d-flex">
                                        <h4 class="card-title mb-0 flex-grow-1">Dress Booking Details  </h4>
                                        <div class="flex-shrink-0">
                                            <ul class="nav justify-content-end nav-tabs-custom rounded card-header-tabs" role="tablist">
                                                <li class="nav-item">
                                                    <a class="nav-link active" data-bs-toggle="tab" href="#profile3" role="tab">
                                                        <span class="d-block d-sm-none"><i class="far fa-user"></i></span>
                                                        <span class="d-none d-sm-block"> Bokings</span>
                                                    </a>
                                                </li>
                                                <li class="nav-item">
                                                    <a class="nav-link" data-bs-toggle="tab" href="#profile2" role="tab">
                                                        <span class="d-block d-sm-none"><i class="far fa-user"></i></span>
                                                        <span class="d-none d-sm-block"> Booking upcoming</span>
                                                    </a>
                                                </li>
                                            </ul>
                                        </div>
                                    </div><!-- end card header -->

                                    <div class="card-body">
                                        <!-- Tab panes -->
                                        <div class="tab-content">
                                            <!-- First Tab Pane -->
                                            <div class="tab-pane fade show active" id="profile3" role="tabpanel">
                                                {{-- <a href="#" class="btn btn-success">إضافة مستندات</a> --}}
                                                <div class="table-responsive">
                                                    <table class="table align-middle dt-responsive table-check nowrap" style="border-collapse: collapse; border-spacing: 0 8px; width: 100%;" id="all_profile_docs_1">
                                                        <thead>
                                                            <tr class="bg-transparent">
                                                                <th style="width: 120px; text-align:center;">Sr#</th>
                                                                <th style="text-align:center;">Dress Detail</th>
                                                                <th style="text-align:center;">Booking Detail</th>
                                                                <th style="text-align:center;">Rent Details</th>
                                                                <th style="text-align:center;">Return Date</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody id="all_profile_docs_1">

                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>

                                            <!-- Second Tab Pane -->
                                            <div class="tab-pane fade" id="profile2" role="tabpanel">
                                                {{-- <a href="#" class="btn btn-success">إضافة مستندات</a> --}}
                                                <div class="table-responsive">
                                                    <table class="table align-middle dt-responsive table-check nowrap" style="border-collapse: collapse; border-spacing: 0 8px; width: 100%;" id="all_profile_docs_2">
                                                        <thead>
                                                            <tr class="bg-transparent">
                                                                <th style="width: 120px; text-align:center;">Sr#</th>
                                                                <th style="text-align:center;">Dress Name</th>
                                                                <th style="text-align:center;">Booking Date</th>
                                                                <th style="text-align:center;">Return Date</th>
                                                                <th style="text-align:center;">Added By</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody id="all_profile_docs_2">
                                                            <!-- Data for documents will go here -->
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- end card-body -->

                                </div><!-- end card -->
                            </div><!-- end col -->
                        </div>
                    </div>

                    <div class="col-xl-4">
                        <div class="card">
                            <div class="card-body">

                                <div class="mt-5">
                                    <h5 class="mb-3">Dress's Other Details</h5>
                                    <ul class="list-unstyled fw-medium px-2">
                                        <li>
                                            <a href="javascript: void(0);" class="text-body pb-3 d-block border-bottom">
                                                Total Bookings
                                                <span id="booking_count" class="badge bg-primary-subtle text-primary rounded-pill ms-1 float-end font-size-12"></span>
                                            </a>
                                        </li>
                                        <li>
                                            <a href="javascript: void(0);" class="text-body py-3 d-block border-bottom">
                                                 Upcoming Bookings
                                                <span id="up_booking" class="badge bg-primary-subtle text-primary rounded-pill ms-1 float-end font-size-12"></span>
                                            </a>
                                        </li>
                                        <li>
                                            <a href="javascript: void(0);" class="text-body py-3 d-block border-bottom">
                                                 Total Pyments
                                                <span id="total_payment" class="badge bg-primary-subtle text-primary rounded-pill float-end ms-1 font-size-12"></span>
                                            </a>
                                        </li>
                                        <li>
                                            <a href="javascript: void(0);" class="text-body py-3 d-block border-bottom">
                                                 Total Panelty
                                                <span id="total_panelty" class="badge bg-primary-subtle text-primary rounded-pill float-end ms-1 font-size-12"></span>
                                            </a>
                                        </li>


                                    </ul>
                                </div>

                                <div class="mt-5">
                                    <h5 class="mb-3">Current Bookings</h5>
                                    <div id="current_bookings" class="list-group list-group-flush">
                                        <!-- Bookings will be appended here via JavaScript -->
                                    </div>
                                </div>

                            </div>
                        </div> <!-- end card -->
                    </div>

                </div>

            </div>
            <!-- end row -->
            <!-- end row -->
        </div> <!-- container-fluid -->
    </div>

    

    @include('layouts.footer')
@endsection
