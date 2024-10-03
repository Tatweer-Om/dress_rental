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
                                {{-- <div class="row">
                                    <div class="col-md-2">
                                        <div>
                                            <div class="form-check mb-3">
                                                <input class="form-check-input" type="radio" name="booking_type" value="1" id="booking_t" checked="">
                                                <label class="form-check-label" for="booking_t">
                                                    Booking
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="booking_type" value="2" id="reservation_t">
                                                <label class="form-check-label" for="reservation_t">
                                                    Reservation
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div> --}}
                                <div class="row">
                                    <div class="col-md-4">
                                        <h2>{{ trans('messages.customer_detail_lang',[],session('locale')) }}</h2>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label for="customer_name" class="form-label">{{ trans('messages.customer_name_lang',[],session('locale')) }} </label>
                                            <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#add_customer_modal"><i class="fas fa-plus"></i></button>
                                            <input class="form-control customer_name" name="customer_name" type="text" id="customer_name">
                                            <input class="form-control customer_id" name="customer_id" type="hidden" id="customer_id">
                                        </div>
                                    </div> 
                                    {{-- <div class="col-md-4">
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
                                    </div>  --}}
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
                                            id="dress_name" onchange="get_dress_detail()">
                                                <option value="">{{ trans('messages.choose_lang',[],session('locale')) }}</option>
                                                @foreach ($view_dress as $dress) {
                                                    <option value="{{$dress->id}}">{{$dress->dress_name.' -'.$dress->sku}}</option>';
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
                                            <label for="total_price" class="form-label">{{ trans('messages.total_price_lang',[],session('locale')) }} </label>
                                            <input class="form-control total_price isnumber" name="total_price" type="text" id="total_price" readonly>
                                        </div>
                                    </div> 
                                </div>
                                <div class="row" id="dress_detail">
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
                                            <input class="form-control booking_date datepick" readonly name="booking_date" type="text" id="booking_date">
                                        </div>
                                    </div> 
                                    <div class="col-md-3">
                                        <div class="mb-3">
                                            <label for="rent_date" class="form-label">{{ trans('messages.rent_date_lang',[],session('locale')) }}</label>
                                            <input class="form-control rent_date" name="rent_date" readonly type="text" id="rent_date">
                                        </div>
                                    </div> 
                                    <div class="col-md-3">
                                        <div class="mb-3">
                                            <label for="return_date" class="form-label">{{ trans('messages.return_date_lang',[],session('locale')) }}</label>
                                            <input class="form-control return_date" name="return_date" readonly type="text" id="return_date">
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
                                        <div class="mb-3">
                                            <label for="notes" class="form-label">{{ trans('messages.notes_lang',[],session('locale')) }}</label>
                                            <textarea class="form-control notes" name="notes" rows="5"></textarea>
                                        </div>
                                    </div> 
                                </div>
                                <div class="row">
                                    <button type="submit" class="btn btn-primary submit_form" id="add_booking_btn">{{ trans('messages.submit_lang',[],session('locale')) }}</button>
                                </div>
                            </form>        
                        </div>
                    </div>
                </div> <!-- end col -->
            </div> <!-- end row -->
        </div> <!-- container-fluid -->
    </div>
    <!-- End Page-content -->

    <!-- Static Backdrop Modal -->
    <div class="modal fade" id="add_payment_modal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="staticBackdropLabel">{{ trans('messages.add_data_lang',[],session('locale')) }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="{{ url('add_payment') }}" class="add_payment" method="POST" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" class="bill_id" name="bill_id">
                        <input type="hidden" class="bill_booking_id" name="booking_id">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="bill_total_amount" class="form-label">{{ trans('messages.total_price_lang',[],session('locale')) }}</label>
                                    <input class="form-control bill_total_amount" name="bill_total_amount" type="text" id="bill_total_amount">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="bill_remaining_amount" class="form-label">{{ trans('messages.remaining_lang',[],session('locale')) }}</label>
                                    <input class="form-control bill_remaining_amount" name="bill_remaining_amount" type="text" id="bill_remaining_amount">
                                </div>
                            </div> 
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="bill_paid_amount" class="form-label">{{ trans('messages.paid_amount_lang',[],session('locale')) }}</label>
                                    <input class="form-control bill_paid_amount" name="bill_paid_amount" type="text" id="bill_paid_amount">
                                </div>
                            </div> 
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="bill_payment_date" class="form-label">{{ trans('messages.paid_amount_lang',[],session('locale')) }}</label>
                                    <input class="form-control bill_payment_date datepick" readonly value="<?php echo date('Y-m-d'); ?>" name="bill_payment_date" type="text" id="bill_payment_date">
                                </div>
                            </div> 
                        </div> 
                        <div class="row">
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="bill_payment_method" class="form-label">{{ trans('messages.total_price_lang',[],session('locale')) }}</label>
                                    <select class="form-control bill_payment_method" name="bill_payment_method">
                                        @foreach($view_account as $account)
                                            <option value="{{ $account->id }}">{{ $account->account_name}}</option>
                                        @endforeach
                                    </select>
                                 </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="bill_notes" class="form-label">{{ trans('messages.notes_lang',[],session('locale')) }}</label>
                                    <textarea class="form-control bill_notes" name="bill_notes" id="bill_notes" rows="3"></textarea>
                                </div>
                            </div>
                        </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">{{ trans('messages.close_lang',[],session('locale')) }}</button>
                    <button type="submit" class="btn btn-primary submit_form">{{ trans('messages.submit_lang',[],session('locale')) }}</button>
                </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Static Backdrop Modal -->
    <div class="modal fade" id="add_customer_modal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="staticBackdropLabel">{{ trans('messages.add_data_lang',[],session('locale')) }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="{{ url('add_customer') }}" class="add_customer" method="POST" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" class="customers_id" name="customer_id">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="customer_names" class="form-label">{{ trans('messages.customer_name_lang',[],session('locale')) }}</label>
                                    <input class="form-control customer_names" name="customer_names" type="text" id="customer_names">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="customer_number" class="form-label">{{ trans('messages.customer_number_lang',[],session('locale')) }}</label>
                                    <input class="form-control customer_number isnumber" name="customer_number" type="text" id="customer_number">
                                </div>
                            </div> 
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="customer_email" class="form-label">{{ trans('messages.customer_email_lang',[],session('locale')) }}</label>
                                    <input class="form-control customer_email" name="customer_email" type="text" id="customer_email">
                                </div>
                            </div> 
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="dob" class="form-label">{{ trans('messages.dob_lang',[],session('locale')) }}</label>
                                    <input class="form-control dob datepick" name="dob" type="text" id="dob">
                                </div>
                            </div> 
                        </div>
                        <div class="row">
                            <div class="col-md-3">
                                <div>
                                     <div class="form-check mb-3">
                                        <input class="form-check-input" type="radio" value="1" name="gender" id="male" checked="">
                                        <label class="form-check-label" for="male">
                                            {{ trans('messages.male_lang',[],session('locale')) }}
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" value="2" name="gender" id="female">
                                        <label class="form-check-label" for="female">
                                            {{ trans('messages.female_lang',[],session('locale')) }}
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="customer_discount" class="form-label">{{ trans('messages.discount_lang',[],session('locale')) }}</label>
                                    <input class="form-control customer_discount isnumber" name="customer_discount" type="text" id="customer_discount">
                                </div>
                            </div>
                        </div> 
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="address" class="form-label">{{ trans('messages.address_lang',[],session('locale')) }}</label>
                                    <textarea class="form-control address" name="address" id="address" rows="3"></textarea>
                                </div>
                            </div>
                        </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">{{ trans('messages.close_lang',[],session('locale')) }}</button>
                    <button type="submit" class="btn btn-primary submit_form">{{ trans('messages.submit_lang',[],session('locale')) }}</button>
                </div>
                </form>
            </div>
        </div>
    </div>
   
    

    @include('layouts.footer_content')
</div>
<!-- end main content-->

</div>
<!-- END layout-wrapper -->
@include('layouts.footer')
@endsection
        
         