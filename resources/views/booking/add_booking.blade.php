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
                                        <h2>{{ trans('messages.customer_detail_lang',[],session('locale')) }}</h2>
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
                                            <input class="form-control customer_email" name="customer_email" type="text" id="customer_email">
                                        </div>
                                    </div> 
                                </div>
                                <div class="row">
                                    <div class="col-md-4">
                                        <h2>{{ trans('messages.booking_detail_lang',[],session('locale')) }}</h2>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="mb-3">
                                            <label for="booking_date" class="form-label">{{ trans('messages.booking_date_lang',[],session('locale')) }}</label>
                                            <input class="form-control booking_date datepick" name="booking_date" type="text" id="booking_date">
                                        </div>
                                    </div> 
                                    <div class="col-md-3">
                                        <div class="mb-3">
                                            <label for="rent_date" class="form-label">{{ trans('messages.rent_date_lang',[],session('locale')) }}</label>
                                            <input class="form-control rent_date" name="rent_date" type="text" id="rent_date">
                                        </div>
                                    </div> 
                                    <div class="col-md-3">
                                        <div class="mb-3">
                                            <label for="return_date" class="form-label">{{ trans('messages.return_date_lang',[],session('locale')) }}</label>
                                            <input class="form-control return_date" name="return_date" type="text" id="return_date">
                                        </div>
                                    </div> 
                                    <div class="col-md-3">
                                        <div class="mb-3">
                                            <label for="duration" class="form-label">{{ trans('messages.duration_lang',[],session('locale')) }}</label>
                                            <input class="form-control duration isnumber" name="duration" type="text" id="duration">
                                        </div>
                                    </div> 
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <h2>{{ trans('messages.dress_detail_lang',[],session('locale')) }}</h2>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="mb-3">
                                            <label for="dress_name" class="form-label">{{ trans('messages.dress_name_lang',[],session('locale')) }}</label>
                                            <select class="form-control dress_name" data-trigger name="dress_name"
                                            id="dress_name">
                                                <option value="">{{ trans('messages.choose_lang',[],session('locale')) }}</option>
                                                @foreach ($view_dress as $dress) {
                                                    <option value="{{$dress->id}}">{{$dress->dress_name.' - '.$dress->sku}}</option>';
                                                }
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="mb-3">
                                            <label for="price" class="form-label">{{ trans('messages.price_lang',[],session('locale')) }}</label>
                                            <input class="form-control price isnumber" name="price" type="text" id="price">
                                        </div>
                                    </div> 
                                    <div class="col-md-3">
                                        <div class="mb-3">
                                            <label for="discount" class="form-label">{{ trans('messages.discount_lang',[],session('locale')) }} %</label>
                                            <input class="form-control discount isnumber" name="discount" value="0" type="text" id="discount">
                                        </div>
                                    </div> 
                                    <div class="col-md-3">
                                        <div class="mb-3">
                                            <label for="total_price" class="form-label">{{ trans('messages.total_price_lang',[],session('locale')) }} %</label>
                                            <input class="form-control total_price isnumber" name="total_price" type="text" id="total_price" readonly>
                                        </div>
                                    </div> 
                                </div>
                                <div class="row" id="dress_detail">
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="notes" class="form-label">{{ trans('messages.notes_lang',[],session('locale')) }}</label>
                                            <textarea class="form-control notes" name="notes" rows="5"></textarea>
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
        
         