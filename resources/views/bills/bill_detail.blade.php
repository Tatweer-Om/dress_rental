@extends('layouts.header')

@section('main')
@push('title')
<title> {{ trans('messages.booking_bill',[],session('locale')) }}</title>
@endpush


<div class="main-content">

    <div class="page-content">
        <div class="container-fluid">

            <!-- start page title -->
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0 font-size-18">Invoice Detail</h4>

                        <div class="page-title-right">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item"><a href="javascript: void(0);">Invoices</a></li>
                                <li class="breadcrumb-item active">Invoice Detail</li>
                            </ol>
                        </div>

                    </div>
                </div>
            </div>
            <!-- end page title -->

            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="row">

                                <div class="col-lg-3">
                                    <div class="invoice-title">
                                        <div class="d-flex align-items-start">
                                            <div class="flex-grow-1">
                                                <div class="mb-4">
                                                    <img src="{{ $setting->log ? asset('images/logo/' . $setting->log) : asset('images/logo-sm.svg') }}"
                                                         alt="Logo" height="24">
                                                    <span class="logo-txt">{{ $setting->company_name ?? 'Company Name' }}</span>
                                                </div>


                                            </div>

                                        </div>


                                        <p class="mb-1">{{ $setting->company_address ?? '' }}</p>
                                        <p class="mb-1"><i class="mdi mdi-email align-middle me-1"></i> {{ $setting->company_email ?? '' }}</p>
                                        <p><i class="mdi mdi-phone align-middle me-1"></i> {{ $setting->company_phone ?? '' }}</p>
                                    </div>
                                </div>
                                <div class="col-lg-3">
                                    <div class="invoice-title">
                                        <div class="d-flex align-items-start">
                                            <div class="flex-grow-1">
                                                <div class="mb-4">
                                                    <img src="{{ asset('images/logo-sm.svg') }}" alt="" height="24"><span class="logo-txt">Customer Detail</span>
                                                </div>
                                            </div>

                                        </div>

                                        <p class="mb-1">{{ $customer->customer_name ?? '' }}</p>
                                        <p class="mb-1">{{ $customer->address ?? '' }}</p>
                                        <p class="mb-1"><i class="mdi mdi-email align-middle me-1"></i> {{ $customer->customer_email ?? '' }}</p>
                                        <p><i class="mdi mdi-phone align-middle me-1"></i> {{ $customer->customer_number ?? '' }}</p>
                                    </div>
                                </div>
                                <div class="col-lg-3">
                                    <div class="invoice-title">
                                        <div class="d-flex align-items-start">
                                            <div class="flex-grow-1">
                                                <div class="mb-4">
                                                    <img src="{{ asset('images/logo-sm.svg') }}" alt="" height="24"><span class="logo-txt">Dress Detail</span>
                                                </div>
                                            </div>

                                        </div>


                                        <p class="mb-1">Dress Name: {{ $booking->dress->dress_name ?? '' }}</p>
                                        <p class="mb-1">Color: {{ $booking->dress->color->color_name ?? '' }}</p>
                                        <p class="mb-1">Brand: {{ $booking->dress->brand->brand_name ?? '' }}</p>
                                        <p class="mb-1">Size: {{ $booking->dress->size->size_name ?? '' }}</p>
                                        <p class="mb-1">
                                            Condition: {{ $booking->dress->condition == 1 ? 'New' : 'Used' }}
                                        </p>

                                    </div>
                                </div>
                                <div class="col-lg-3">
                                    <div class="invoice-title">
                                        <div class="d-flex align-items-start">
                                            <div class="flex-grow-1">
                                                <div class="mb-4">
                                                    <img src="{{ asset('images/logo-sm.svg') }}" alt="" height="24"><span class="logo-txt">Booking Detail</span>
                                                </div>
                                            </div>

                                        </div>


                                        <p class="mb-1"><span>Booking # {{ $booking->booking_no ?? '' }}</p>
                                        <p class="mb-1"><span>Booking Date: </span>{{$booking->booking_date  }}</p>
                                        <p class="mb-1">Rent Date: </span>{{$booking->rent_date  }}</p>
                                        <p class="mb-1">Return Date: </span>{{$booking->return_date  }}</p>
                                        <p class="mb-1">Rent Price: </span>{{ $booking->price ?? '' }}</p>
                                        <p class="mb-1">Duration: </span>{{$booking->duration ?? ''  }} days</p>


                                    </div>
                                </div>

                            </div>


                            <hr class="my-4">
                            @if($extention->isNotEmpty())
                            <div class="py-2 mt-3">
                                <h5 class="font-size-15">Extention History</h5>
                            </div>
                            <div class="p-4 border rounded">
                                <div class="table-responsive">
                                    <table class="table table-nowrap align-middle mb-0">
                                        <thead>
                                            <tr>
                                                <th style="width: 70px;">Rent Date</th>
                                                <th>Return Date</th>
                                                <th class="text-end" style="width: 120px;">Price</th>
                                                <th class="text-end" style="width: 120px;">Duration</th>
                                                <th class="text-end" style="width: 120px;">Total Price</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @if($extention->isNotEmpty())
                                                @foreach($extention as $ext)
                                                    <tr> <!-- Each row should start with <tr> -->
                                                        <td>
                                                            <h5 class="font-size-14">Rent Date: {{ $ext->rent_date ?? 'No Rent Date' }}</h5>

                                                        </td>
                                                        <td>

                                                            <h5 class="font-size-14">Return Date: {{ $ext->return_date ?? 'No Return Date' }}</h5>
                                                        </td>
                                                       <!-- Adjust based on your extension model -->
                                                        <td class="text-end">{{ $ext->price ?? '0.00' }}</td>
                                                        <td class="text-end">{{ $ext->duration ?? 'N/A' }} days</td>
                                                        <td class="text-end">{{ $ext->total_price ?? '0.00' }}</td> <!-- Adjust based on your extension model -->
                                                    </tr>
                                                @endforeach
                                            @else
                                                <tr>
                                                    <td colspan="3" class="text-center">No extensions found for this booking.</td> <!-- Center message in the table -->
                                                </tr>
                                            @endif
                                        </tbody>
                                    </table>

                                </div>
                            </div>
                            @endif
                            <div class="py-2 mt-3">
                                <h5 class="font-size-15">Payment History</h5>
                            </div>
                            <div class="p-4 border rounded">
                                <div class="table-responsive">
                                    <table class="table table-nowrap align-middle mb-0">
                                        <thead>
                                            <tr>
                                                <th style="width: 70px;">Payment Date</th>
                                                <th>Grand Total</th>
                                                <th class="text-end" style="width: 120px;">Paid Amount</th>
                                                <th class="text-end" style="width: 120px;">Remainig Amount</th>
                                                <th class="text-end" style="width: 120px;">Payment Method</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @if($booking->payments->isNotEmpty())
                                            @php $sno=1;
                                                $total_amount=0;
                                            @endphp
                                            @foreach($booking->payments as $payment)
                                                @php
                                                    if($sno==1)
                                                    {
                                                        $total_amount = $booking->bill->grand_total;
                                                    }
                                                    $remaining_total = $total_amount - $payment->paid_amount;
                                                @endphp
                                                    <tr> <!-- Each row should start with <tr> -->
                                                        <td>
                                                            <h5 class="font-size-14"> {{ $payment->payment_date ?? '' }}</h5>

                                                        </td>
                                                        <td>

                                                            <h5 class="font-size-14">{{ $total_amount ?? '' }}</h5>
                                                        </td>
                                                       <!-- Adjust based on your extension model -->
                                                        <td class="text-end">{{ $payment->paid_amount ?? 'No Payment' }}</td>
                                                        <td class="text-end">{{ $remaining_total ?? '' }}</td>
                                                        <td class="text-end">{{ $account->account_name ?? 'N/A' }}</td> <!-- Adjust based on your extension model -->
                                                    </tr>
                                                    @php
                                                        $total_amount = $booking->bill->grand_total - $payment->paid_amount;
                                                        $sno++;
                                                    @endphp
                                                @endforeach
                                            @else
                                                <>
                                                    <td colspan="3" class="text-center">No Payment History</td> <!-- Center message in the table -->
                                                </tr>
                                            @endif
                                        </tbody>
                                    </table>

                                </div>
                            </div>

                            <div class="p-4 border rounded">
                                <div class="table-responsive">
                                    <table class="table table-nowrap align-middle mb-0">
                                        <tbody>
                                            <tr>
                                                <td class="text-end"><strong>Total Price:</strong></td>
                                                <td class="text-end">{{ $booking->total_price ?? '' }}</td>
                                            </tr>
                                            <tr>
                                                <td class="text-end"><strong>Discount:</strong></td>
                                                <td class="text-end">{{ $booking->bill->total_discount ?? '0.000' }}</td>
                                            </tr>
                                            <tr>
                                                <td class="text-end"><strong>Total Penalty:</strong></td>
                                                <td class="text-end">{{ $booking->bill->total_panelty ?? '0.000' }}</td>
                                            </tr>
                                            <tr>
                                                <td class="text-end"><strong>Grand Total:</strong></td>
                                                <td class="text-end">{{ $booking->bill->grand_total ?? '' }}</td>
                                            </tr>
                                            <tr>
                                                <td class="text-end"><strong>Paid Amount:</strong></td>
                                                <td class="text-end">{{ $sumPaidAmount ?? '' }}</td>
                                            </tr>
                                            <tr>
                                                <td class="text-end" colspan="2"><h5 class="font-size-18" >Remaining Amount: <span class="text-danger">{{ $remain ?? '' }} </span> </h5></td>

                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>



                            <div class="d-print-none mt-3">
                                <div class="float-end">
                                    <a href="javascript:window.print()" class="btn btn-success waves-effect waves-light me-1"><i class="fa fa-print"></i></a>
                                    <a href="#" class="btn btn-primary w-md waves-effect waves-light">Send</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- end row -->
        </div> <!-- container-fluid -->
    </div>




    @include('layouts.footer')
@endsection
