@extends('layouts.header')

@section('main')
@push('title')
<title> {{ trans('messages.menu_booking_lang',[],session('locale')) }}</title>
@endpush
<!-- ============================================================== -->
<!-- Start right Content here -->
<!-- ============================================================== -->
<div class="main-content">

    <div class="page-content">
        <div class="container-fluid">

            <!-- start page title -->
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0 font-size-18">{{ trans('messages.menu_booking_lang',[],session('locale')) }}</h4>

                        <div class="page-title-right">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item"><a href="javascript: void(0);">{{ trans('messages.pages_lang',[],session('locale')) }}</a></li>
                                <li class="breadcrumb-item active">{{ trans('messages.menu_booking_lang',[],session('locale')) }}</li>
                            </ol>
                        </div>

                    </div>
                </div>
            </div>

            <!-- end page title -->
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <form action="{{ url('add_booking') }}" class="add_booking" method="POST" enctype="multipart/form-data">
                                @csrf
                                <input type="hidden" class="booking_id" name="booking_id">
                                <div class="row">
                                    <div class="col-md-2">
                                        <div>
                                            <div class="form-check mb-3">
                                                <input class="form-check-input" type="radio" name="booking_type" id="booking_t" checked="">
                                                <label class="form-check-label" for="booking_t">
                                                    Booking
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="booking_type" id="reservation_t">
                                                <label class="form-check-label" for="reservation_t">
                                                    Reservation
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label for="customer_name" class="form-label">{{ trans('messages.customer_name_lang',[],session('locale')) }}</label>
                                            <input class="form-control customer_name" name="customer_name" type="text" id="customer_name">
                                        </div>
                                    </div> 
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label for="customer_contact" class="form-label">{{ trans('messages.customer_contact_lang',[],session('locale')) }}</label>
                                            <input class="form-control customer_contact" name="customer_contact" type="text" id="customer_contact">
                                        </div>
                                    </div> 
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label for="customer_email" class="form-label">{{ trans('messages.customer_email_lang',[],session('locale')) }}</label>
                                            <input class="form-control customer_email isnumber" name="customer_email" type="text" id="customer_email">
                                        </div>
                                    </div> 
                                </div>
                            </form>        
                        </div>
                    </div>
                </div> <!-- end col -->
            </div> <!-- end row -->
        </div> <!-- container-fluid -->
    </div>
    <!-- End Page-content -->

   
    

    @include('layouts.footer_content')
</div>
<!-- end main content-->

</div>
<!-- END layout-wrapper -->
@include('layouts.footer')
@endsection
        
         