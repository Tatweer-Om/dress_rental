<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Size;
use App\Models\User;
use App\Models\Color;
use App\Models\Dress;
use App\Models\Account;
use App\Models\Booking;
use App\Models\Setting;
use App\Models\Category;
use App\Models\Customer;
use App\Models\DressImage;
use App\Models\BookingBill;
use App\Models\DressHistory;
use Illuminate\Http\Request;
use App\Models\BookingPayment;
use App\Models\DressAttribute;
use App\Models\DressAvailability;
use App\Http\Controllers\Controller;
use App\Models\BookingExtendHistory;
use Illuminate\Support\Facades\Auth;
use App\Models\BookingDressAttribute;


class BookingController extends Controller
{
    public function index(){
        $view_dress= Dress::all();
        $view_account = Account::where('account_type', 1)->get();

        if (!Auth::check()) {

            return redirect()->route('login_page')->with('error', 'Please LogIn first()');
        }

        $user = Auth::user();

        if (in_array(3, explode(',', $user->permit_type))) {

            return view ('booking.add_booking', compact('view_dress','view_account'));
        } else {

            return redirect()->route('home')->with( 'error', 'You dont have Permission');
        }
    }
    public function view_booking(){
        $view_account = Account::where('account_type', 1)->get();


        if (!Auth::check()) {

            return redirect()->route('login_page')->with('error', 'Please LogIn first()');
        }

        $user = Auth::user();

        if (in_array(3, explode(',', $user->permit_type))) {

            return view ('booking.view_booking', compact('view_account'));
        } else {

            return redirect()->route('home')->with( 'error', 'You dont have Permission');
        }
    }
    public function show_booking()
    {
        $sno=0;

        $view_booking= booking::all();
        if(count($view_booking)>0)
        {
            foreach($view_booking as $value)
            {

                $modal='<a class="btn btn-warning btn-sm edit" data-bs-toggle="modal" data-bs-target="#add_booking_info_modal" onclick=get_booking_detail("'.$value->id.'") title="info">
                            <i class="fas fa-info" title="info"></i>
                        </a>
                        <a onclick=delete_booking("'.$value->id.'") class="btn btn-primary btn-sm edit" title="delete booking">
                            <i class="fas fa-trash" title="delete booking"></i>
                        </a>';
                $add_data=get_date_only($value->created_at);
                $status = $value->status; // Assuming `status` is the field name for the status in the database
                $status_badge = '';

                if ($status == 1) {
                    // Status is "New"
                    $status_badge = '<span class="badge bg-warning">'.trans('messages.new_lang',[],session('locale')).'</span>';
                } elseif ($status == 2) {
                    // Status is "Rented"
                    $status_badge = '<span class="badge bg-primary">'.trans('messages.rent_lang',[],session('locale')).'</span>';
                } elseif ($status == 3) {
                    // Status is "Finished"
                    $status_badge = '<span class="badge bg-success">'.trans('messages.finish_lang',[],session('locale')).'</span>';
                } elseif ($status == 4) {
                    // Status is "Finished"
                    $status_badge = '<span class="badge bg-danger">'.trans('messages.cancel_lang',[],session('locale')).'</span>';
                }

                $sno++;
                $json[]= array(
                            $sno,
                            $value->booking_no,
                            $status_badge,
                            getColumnValue('customers','id',$value->customer_id,'customer_name'),
                            getColumnValue('dresses','id',$value->dress_id,'dress_name'),
                            $value->booking_date,
                            $value->rent_date,
                            $value->return_date,
                            $value->total_price,
                            $add_data,
                            $value->added_by,
                            $modal
                        );
            }
            $response = array();
            $response['success'] = true;
            $response['aaData'] = $json;
            echo json_encode($response);
        }
        else
        {
            $response = array();
            $response['sEcho'] = 0;
            $response['iTotalRecords'] = 0;
            $response['iTotalDisplayRecords'] = 0;
            $response['aaData'] = [];
            echo json_encode($response);
        }
    }
    public function get_dress_detail(Request $request)
	{
        $dress_id = $request['dress_id'];
        $rent_date = $request['rent_date'];  // Correct Carbon usage
        $return_date = $request['return_date']; // Correct Carbon usage
        // setting data
        $setting_data = Setting::first();
        // Query to check if any booking exists with matching dress_id and date ranges overlap
        if($setting_data->dress_available <= 0 || empty($setting_data->dress_available))
        {
            $days = 1;
        }
        else
        {
            $days = intval($setting_data->dress_available);
        }
        $rent_date_adjusted = Carbon::parse($rent_date)->subDays($days); // 2 days before rent date
        $return_date_adjusted = Carbon::parse($return_date)->addDays($days); // 2 days after return date

        $existingBooking = Booking::where(function ($query) use ($rent_date_adjusted, $return_date_adjusted, $dress_id) {
            $query->whereDate('rent_date', '>=', $rent_date_adjusted)
                ->whereDate('rent_date', '<=', $return_date_adjusted)
                ->where('dress_id', $dress_id);
        })
        ->orWhere(function ($query) use ($rent_date_adjusted, $return_date_adjusted, $dress_id) {
            $query->whereDate('return_date', '>=', $rent_date_adjusted)
                ->whereDate('return_date', '<=', $return_date_adjusted)
                ->where('dress_id', $dress_id);
        })
        ->first();

        if (!empty($existingBooking)) {
            // Handle case where a booking exists
            return response()->json(['status'=>2]);
            exit;
        }
        // validataion for maintanance

        // Validation for maintenance check
        $conflictingDresses = Dress::where('status', 2)
            ->where(function ($query) use ($rent_date, $return_date) {
                // Use the correct Carbon format and pass as values in whereBetween
                $query->whereBetween('start_date', [Carbon::parse($rent_date), Carbon::parse($return_date)])
                    ->orWhereBetween('end_date', [Carbon::parse($rent_date), Carbon::parse($return_date)]);
            })->exists();

        if ($conflictingDresses) {
            // Handle case where a booking exists
            return response()->json(['status'=>3]);
            exit;
        }
        // dress data
        $dress_data = Dress::where('id', $dress_id)->first();
		// dress attributes
        $dress_att_div='<div class="col-md-6">
                                <div class="row">
                                    <h2>'.trans('messages.attribute_lang',[],session('locale')).'</h2>
                                </div>
                                <div class="row accordion" id="accordionExample">';
        $dress_attribute = DressAttribute::where('dress_id', $dress_id)->get();
        if(!empty($dress_attribute))
        {
            foreach ($dress_attribute as $key => $value) {
                $dress_att_div.='<div class="col-md-6">
                                    <div class="accordion-item">
                                        <h2 class="accordion-header" id="heading'.$value->id.'">
                                            <button class="accordion-button fw-medium collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse'.$value->id.'" aria-expanded="false" aria-controls="collapse'.$value->id.'">
                                                '.$value->attribute.'
                                            </button>
                                        </h2>
                                        <div id="collapse'.$value->id.'" class="accordion-collapse collapse" aria-labelledby="headingOne" data-bs-parent="#accordionExample" style="">
                                            <div class="accordion-body">
                                                <div class="text-muted">
                                                    <strong class="text-dark">'.$value->notes.'
                                                    </strong>
                                                </div>
                                            </div>
                                        </div>
                                    </div><br>
                                </div>';
            }
        }
        else
        {
            $dress_att_div.='<div class="col-md-12">
                                <h2>'.trans('messages.no_attribute_lang',[],session('locale')).'</h2>
                            </div>';
        }
        $dress_att_div.='</div>
                            </div>';
        // dress images
        $dress_image_div='<div class="col-md-6">
                                <div class="row">
                                    <h2>'.trans('messages.image_lang',[],session('locale')).'</h2>
                                </div>
                                <div class="row">';
        $images = DressImage::where('dress_id', $dress_id)->get();
        if(!empty($images))
        {
            foreach($images as $rows)
            {

                // Generate the URL for the file
                $url = asset('custom_images/dress_image/' . basename($rows->dress_image));
                $dress_image_div .= '<div class="col-lg-3 col-sm-6">
                            <div class="mt-4">
                                <a href="'.$url.'" class="image-popup">
                                    <img src="'.$url.'" class="img-fluid" alt="work-thumbnail">
                                </a>
                            </div>
                        </div>';
            }
        }
        else
        {
            $dress_image_div.='<div class="col-md-12">
                    <h2>'.trans('messages.no_image_lang',[],session('locale')).'</h2>
                </div>';
        }
        $dress_image_div.='</div>
        </div>';

        return response()->json(['status'=>1,'dress_detail' => $dress_att_div.$dress_image_div,'price'=>$dress_data->price]);
	}

    // add dress availability
    public function add_dress_availability(Request $request){

        $user_id = Auth::id();
        $data= User::find( $user_id)->first();
        $user= $data->user_name;

        $dress_avail = new DressAvailability();

        $dress_avail->contact = $request['number'];
        $dress_avail->dress_id = $request['dress_id'];
        $dress_avail->added_by = $user;
        $dress_avail->user_id = $user_id;
        $dress_avail->save();
    }


    public function add_booking(Request $request){

        $user_id = Auth::id();
        $data= User::where('id', $user_id)->first();
        $user= $data->user_name;


        $dress_id  = $request['dress_name'];

        $booking = new Booking();
        $booking_no  = get_booking_number();
        $booking->booking_no = $booking_no;
        $booking->customer_id = $request['customer_id'];
        $booking->booking_date = $request['booking_date'];
        $booking->rent_date = $request['rent_date'];
        $booking->return_date = $request['return_date'];
        $booking->duration = $request['duration'];
        $booking->dress_id = $request['dress_name'];
        $booking->price = $request['price'];
        $booking->discount = $request['discount'];
        $booking->total_price = $request['total_price'];
        $booking->notes = $request['notes'];
        $booking->added_by = $user;
        $booking->user_id = $user_id;
        $booking->save();
        $booking_id = $booking->id;

        // add dress booking history
        $dress_history = new DressHistory();
        $dress_history->booking_no = $booking_no;
        $dress_history->booking_id = $booking_id;
        $dress_history->customer_id = $request['customer_id'];
        $dress_history->dress_id = $request['dress_name'];
        $dress_history->rent_date = $request['rent_date'];
        $dress_history->return_date = $request['return_date'];
        $dress_history->type = 1;
        $dress_history->source = "booking";
        $dress_history->history_date = $request['booking_date'];
        $dress_history->notes = $request['notes'];
        $dress_history->added_by = $user;
        $dress_history->user_id = $user_id;
        $dress_history->save();


        // add attribute
        $booking_attribute = DressAttribute::where('dress_id', $dress_id)->get();

        if(!empty($booking_attribute))
        {
            foreach ($booking_attribute as $key => $value) {
                $booking_attribute = new BookingDressAttribute();
                $booking_attribute->booking_id = $booking_id;
                $booking_attribute->booking_no = $booking_no;
                $booking_attribute->dress_id = $request['dress_name'];
                $booking_attribute->attribute_id = $value->id;
                $booking_attribute->attribute_name = $value->attribute;
                $booking_attribute->attribute_notes = $value->notes;
                $booking_attribute->added_by = $user;
                $booking_attribute->user_id = $user_id;
                $booking_attribute->save();
            }
        }

        // add booking bill
        $total_price = $request['price'] * $request['duration'];

        $booking_bill = new BookingBill();
        $booking_bill->booking_id = $booking_id;
        $booking_bill->booking_no = $booking_no;
        $booking_bill->customer_id = $request['customer_id'];
        $booking_bill->total_price = $total_price;
        $booking_bill->total_discount = $total_price-$request['total_price'];
        $booking_bill->grand_total = $request['total_price'];
        $booking_bill->total_remaining = $request['total_price'];
        $booking_bill->added_by = $user;
        $booking_bill->user_id = $user_id;
        $booking_bill->save();
        $bill_id = $booking_bill->id;

        return response()->json(['booking_id' => $booking_id,'bill_id' => $bill_id]);

    }

    // search customer
    public function search_customer(Request $request) {
        $term = $request->input('term');

        $customers = Customer::where(function($query) use ($term) {
            $query->where('customer_name', 'like', '%' . $term . '%')
                  ->orWhere('customer_number', 'like', '%' . $term . '%')
                  ->orWhere('customer_email', 'like', '%' . $term . '%');
        })
        ->where('status', 1)
        ->get()
        ->toArray();
        $response = [];
        if(!empty($customers))
        {
            foreach ($customers as $customer) {

                $customer_name = $customer['customer_name'];

                $response[] = [
                    'label' => $customer['id'].'-'.$customer_name.'+'.$customer['customer_number'],
                    'value' => $customer['id'].'-'.$customer_name.'+'.$customer['customer_number'],
                    'customer_id' => $customer['id'],
                    'discount' => $customer['discount'],
                ];

            }
        }


        return response()->json($response);
    }

    public function add_booking_customer(Request $request){

        $user_id = Auth::id();
        $data= User::find( $user_id)->first();
        $user= $data->user_name;

        $customer = new Customer();
        $customer_data = Customer::where('customer_number', $request['customer_contact'])->first();
        if(!empty($customer_data))
        {
            return response()->json(['status' => 2]);
            exit;
        }
        $customer->customer_name = $request['customer_names'];
        $customer->customer_number = $request['customer_number'];
        $customer->customer_email = $request['customer_email'];
        $customer->dob = $request['dob'];
        $customer->gender = $request['gender'];
        $customer->discount = $request['customer_discount'];
        $customer->address = $request['address'];
        $customer->added_by = $user;
        $customer->user_id = $user_id;
        $customer->save();
        $customer_id = $customer->id;
        $customer_full_name = $customer_id.'-'.$request['customer_names'].'+'.$request['customer_number'];
        return response()->json(['status' => 1,'discount' => $request['customer_discount'],'customer_id' => $customer_id,'full_name' => $customer_full_name]);

    }


    public function get_payment(Request $request){

        $user_id = Auth::id();
        $data= User::find( $user_id)->first();
        $user= $data->user_name;

        $bill_id = $request['bill_id'];
        $booking_id = $request['booking_id'];
        $total_amount = 0;
        $remaining_total = 0;
        $bill_data = BookingBill::where('id', $bill_id)->first();
        if(!empty($bill_data))
        {
            $total_amount = $bill_data['grand_total'];
            $remaining_total = $bill_data['total_remaining'];
        }
        return response()->json(['total_amount' => $remaining_total,'remaining_total' => $remaining_total]);

    }

    public function add_payment(Request $request){

        $user_id = Auth::id();
        $data= User::find( $user_id)->first();
        $user= $data->user_name;

        $booking_payment = new BookingPayment();
        $booking_data = Booking::where('id', $request['booking_id'])->first();

        $booking_payment->booking_id = $request['booking_id'];
        $booking_payment->booking_no = $booking_data->booking_no;
        $booking_payment->bill_id = $request['bill_id'];
        $booking_payment->customer_id = $booking_data->customer_id;
        $booking_payment->total_amount = $request['bill_remaining_amount'];
        $booking_payment->paid_amount = $request['bill_paid_amount'];
        $booking_payment->remaining_amount = $request['bill_remaining_amount']-$request['bill_paid_amount'];
        $booking_payment->payment_date = $request['bill_payment_date'];
        $booking_payment->payment_method = $request['bill_payment_method'];
        $booking_payment->notes = $request['bill_notes'];
        $booking_payment->added_by = $user;
        $booking_payment->user_id = $user_id;
        $booking_payment->save();

        // bill update
        // amount addition
        $bill_data = BookingBill::where('id', $request['bill_id'])->first();
        if(!empty($bill_data))
        {
            $last_remaining = $bill_data->total_remaining;
            $new_remaining = $last_remaining - $request['bill_paid_amount'];
            $bill_data->total_remaining = $new_remaining;
            $bill_data->save();
        }
        // amount addition
        $account_data = Account::where('id', $request['bill_payment_method'])->first();
        if(!empty($account_data))
        {
            $last_income = $account_data->opening_balance;
            $new_income = $last_income + $request['bill_paid_amount'];
            $account_data->opening_balance = $new_income;
            $account_data->save();
        }
    }
    // get boooking detail
    public function get_booking_detail(Request $request)
	{
        $booking_id = $request['booking_id'];


        // Query to check if any booking exists with matching dress_id and date ranges overlap
        $booking_data = Booking::where('id', $booking_id)->first();
        $customer_data = Customer::where('id', $booking_data->customer_id)->first();
        $dress_data = Dress::where('id', $booking_data->dress_id)->first();
        $category_data = Category::where('id', $dress_data->category_name)->first();
        $size_data = Size::where('id', $dress_data->size_name)->first();
        $color_data = Color::where('id', $dress_data->color_name)->first();
        $bill_data = BookingBill::where('booking_id', $booking_id)->first();
        $payment_data = BookingPayment::where('booking_id', $booking_id)->get();

		// dress attributes
        $payment_detail="";
        if(!empty($payment_data))
        {
            $payment_detail.='
            <table class="table table-bordered dt-responsive  nowrap w-100" style="width:100%">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>'.trans('messages.total_price_lang',[],session('locale')).'</th>
                        <th>'.trans('messages.paid_amount_lang',[],session('locale')).'</th>
                        <th>'.trans('messages.remaining_lang',[],session('locale')).'</th>
                        <th>'.trans('messages.payment_date_lang',[],session('locale')).'</th>
                        <th>'.trans('messages.added_by_lang',[],session('locale')).'</th>
                        <th>'.trans('messages.action_lang',[],session('locale')).'</th>
                    </tr>
                </thead>
                <tbody>';
                $sno=1;
                foreach ($payment_data as $key => $value) {
                    if($sno==1)
                    {
                        $total_amount = $bill_data->grand_total;
                    }
                    $remaining_total = $total_amount - $value->paid_amount;
                    $payment_detail.='<tr id="ptr'.$value->id.'">
                        <th>'.$sno.'</th>
                        <th>'.$total_amount.'</th>
                        <th>'.$value->paid_amount.'</th>
                        <th>'.$remaining_total.'</th>
                        <th>'.$value->payment_date.'</th>
                        <th>'.$value->added_by.'</th>
                        <th><a class="btn btn-outline-secondary btn-sm edit" onclick=del_payment("'.$value->id.'") title="Delete">
                                <i class="fas fa-trash" title="delete"></i>
                            </a>
                        </th>
                    </tr>';
                    $total_amount = $bill_data->grand_total - $value->paid_amount;
                    $sno++;
                }
                $payment_detail.=' </tbody>
                </table>';
        }
        // dress attributes
        if($booking_data->status == 3)
        {
            $dress_att_div='<div class="col-md-12">
                                <div class="row">
                                    <h2>'.trans('messages.attribute_lang',[],session('locale')).'</h2>
                                </div>
                                <div class="row accordion" id="accordionExample">';
            $dress_attribute = BookingDressAttribute::where('dress_id', $dress_data->id)->where('booking_id', $booking_id)->get();
            if(!empty($dress_attribute))
            {
                foreach ($dress_attribute as $key => $value) {
                    if($value->status == 2)
                    {
                        $dress_status = '<span class="badge bg-success">'.trans('messages.clear_lang',[],session('locale')).'</span>';
                    }
                    else
                    {
                        $dress_status ='<span class="badge bg-danger">'.trans('messages.faulty_lang',[],session('locale')).'</span>';
                    }
                    $dress_att_div.='<div class="col-md-6">
                                        <div class="accordion-item">
                                            <h2 class="accordion-header" id="heading'.$value->id.'">
                                                <button class="accordion-button fw-medium collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse'.$value->id.'" aria-expanded="false" aria-controls="collapse'.$value->id.'">
                                                    '.$value->attribute_name.' ('.$dress_status.')
                                                </button>
                                            </h2>
                                            <div id="collapse'.$value->id.'" class="accordion-collapse collapse" aria-labelledby="headingOne" data-bs-parent="#accordionExample" style="">
                                                <div class="accordion-body">
                                                    <div class="text-muted">
                                                       <strong class="text-dark">'.trans('messages.price_lang',[],session('locale')).' '.$value->penalty_price.'
                                                        </strong>
                                                        <hr>
                                                        <strong class="text-dark">'.$value->fault_notes.'
                                                        </strong>
                                                    </div>
                                                </div>
                                            </div>
                                        </div><br>
                                    </div>';
                }
            }
            else
            {
                $dress_att_div.='<div class="col-md-12">
                                    <h2>'.trans('messages.no_attribute_lang',[],session('locale')).'</h2>
                                </div>';
            }
            $dress_att_div.='</div>
                                </div>';
        }
        else
        {

            $dress_att_div='<div class="col-md-12">
                                <div class="row">
                                    <h2>'.trans('messages.attribute_lang',[],session('locale')).'</h2>
                                </div>
                                <div class="row accordion" id="accordionExample">';
            $dress_attribute = DressAttribute::where('dress_id', $dress_data->id)->get();
            if(!empty($dress_attribute))
            {
                foreach ($dress_attribute as $key => $value) {
                    $dress_att_div.='<div class="col-md-6">
                                        <div class="accordion-item">
                                            <h2 class="accordion-header" id="heading'.$value->id.'">
                                                <button class="accordion-button fw-medium collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse'.$value->id.'" aria-expanded="false" aria-controls="collapse'.$value->id.'">
                                                    '.$value->attribute.'
                                                </button>
                                            </h2>
                                            <div id="collapse'.$value->id.'" class="accordion-collapse collapse" aria-labelledby="headingOne" data-bs-parent="#accordionExample" style="">
                                                <div class="accordion-body">
                                                    <div class="text-muted">
                                                        <strong class="text-dark">'.$value->notes.'
                                                        </strong>
                                                    </div>
                                                </div>
                                            </div>
                                        </div><br>
                                    </div>';
                }
            }
            else
            {
                $dress_att_div.='<div class="col-md-12">
                                    <h2>'.trans('messages.no_attribute_lang',[],session('locale')).'</h2>
                                </div>';
            }
            $dress_att_div.='</div>
                                </div>';

        }
        $tab_li_extend="";
        $tab_content_extend="";
        $extend_history = BookingExtendHistory::where('booking_id', $booking_id)->where('type', 2)->get();

        if ($extend_history->isNotEmpty())
        {

            $tab_li_extend='<li class="nav-item waves-effect waves-light">
                                <a class="nav-link" data-bs-toggle="tab" href="#extend_history_tab" role="tab">
                                    <span class="d-block d-sm-none"><i class="fas fa-home"></i></span>
                                    <span class="d-none d-sm-block">'.trans('messages.extend_history_lang',[],session('locale')).'</span>
                                </a>
                            </li>';
            $start_date =   Carbon::parse($booking_data->rent_date)->format('d F Y');
            $tab_content_extend.='<div class="tab-pane" id="extend_history_tab" role="tabpanel">
                                    <div class="row justify-content-center">
                                        <div class="col-xl-10">
                                            <div class="timeline">
                                                <div class="timeline-container">
                                                    <div class="timeline-launch">
                                                        <div class="timeline-box">
                                                            <div class="timeline-text">
                                                                <h3 class="font-size-18">'.$start_date.'</h3>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="timeline-end">
                                                        <p>'.trans('messages.start_lang',[],session('locale')).'</p>

                                                    </div>
                                                    <div class="timeline-continue">';
            $i=1;

            foreach ($extend_history as $key => $history) {
                if ($i % 2 == 1) {
                    $show_style='timeline-right';
                }
                else
                {
                    $show_style='timeline-left';
                }
                $extend_date_date =   Carbon::parse($history->rent_date)->format('d');
                $extend_date_month =   Carbon::parse($history->rent_date)->format('F');
                $tab_content_extend.='<div class="row '.$show_style.'">
                                        <div class="col-md-6">
                                            <div class="timeline-icon">
                                                <i class="bx bx-briefcase-alt-2 text-primary h2 mb-0"></i>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="timeline-box">
                                                <div class="timeline-date bg-primary text-center rounded">
                                                    <h3 class="text-white mb-0">'.$extend_date_date.'</h3>
                                                    <p class="mb-0 text-white-50">'.$extend_date_month.'</p>
                                                </div>
                                                <div class="event-content">
                                                    <div class="timeline-text">
                                                        <h3 class="font-size-18">'.trans('messages.extend_no_lang',[],session('locale')).' '.$i.'</h3>
                                                        <p class="mb-0 mt-2 pt-1 text-muted">'.$history->notes.'</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>';
                $i++;
            }
            $finish_data_div="";
            if($booking_data->status == 3)
            {
                $end_date =   Carbon::parse($booking_data->finish_date)->format('d F Y');
                $finish_data_div='<div class="timeline-start">
                                <p>'.trans('messages.end_lang',[],session('locale')).'</p>
                            </div>
                            <div class="timeline-launch">
                                <div class="timeline-box">
                                    <div class="timeline-text">
                                        <h3 class="font-size-18">'.$end_date.'</h3>
                                    </div>
                                </div>
                            </div>';
            }
            else
            {
                $end_date =   Carbon::parse($booking_data->return_date)->format('d F Y');
                $finish_data_div='<div class="timeline-start">

                                <p>'.trans('messages.end_lang',[],session('locale')).'</p>

                            </div>
                            <div class="timeline-launch">
                                <div class="timeline-box">
                                    <div class="timeline-text">
                                        <h3 class="font-size-18">'.$end_date.'</h3>
                                    </div>
                                </div>
                            </div>';
            }
            $tab_content_extend.='</div>'.$finish_data_div.'

                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>';
        }
        $booking_detail='<div class="col-md-10">
                            <ul class="nav nav-pills nav-justified" role="tablist">
                                <li class="nav-item waves-effect waves-light">
                                    <a class="nav-link active" data-bs-toggle="tab" href="#booking_tab" role="tab">
                                        <span class="d-block d-sm-none"><i class="fas fa-home"></i></span>
                                        <span class="d-none d-sm-block">'.trans('messages.booking_detail_lang',[],session('locale')).'</span>
                                    </a>
                                </li>
                                <li class="nav-item waves-effect waves-light">
                                    <a class="nav-link" data-bs-toggle="tab" href="#customer_tab" role="tab">
                                        <span class="d-block d-sm-none"><i class="fas fa-home"></i></span>
                                        <span class="d-none d-sm-block">'.trans('messages.customer_detail_lang',[],session('locale')).'</span>
                                    </a>
                                </li>
                                <li class="nav-item waves-effect waves-light">
                                    <a class="nav-link" data-bs-toggle="tab" href="#dress_tab" role="tab">
                                        <span class="d-block d-sm-none"><i class="fas fa-home"></i></span>
                                        <span class="d-none d-sm-block">'.trans('messages.dress_detail_lang',[],session('locale')).'</span>
                                    </a>
                                </li>
                                <li class="nav-item waves-effect waves-light">
                                    <a class="nav-link" data-bs-toggle="tab" href="#payment_tab" role="tab">
                                        <span class="d-block d-sm-none"><i class="fas fa-home"></i></span>
                                        <span class="d-none d-sm-block">'.trans('messages.payment_detail_lang',[],session('locale')).'</span>
                                    </a>
                                </li>
                                '.$tab_li_extend.'
                            </ul>
                            <div class="tab-content p-3 text-muted">
                                <div class="tab-pane active" id="booking_tab" role="tabpanel">
                                    <div class="table-responsive">
                                        <table class="table table-borderless">
                                            <tbody>
                                                <tr>
                                                    <td>
                                                        <strong>'.trans('messages.booking_no_lang', [], session('locale')).':</strong>
                                                        '.$booking_data->booking_no.'
                                                    </td>
                                                    <td>
                                                        <strong>'.trans('messages.booking_date_lang', [], session('locale')).':</strong>
                                                        '.$booking_data->booking_date.'
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <strong>'.trans('messages.rent_date_lang', [], session('locale')).':</strong>
                                                        '.$booking_data->rent_date.'
                                                    </td>
                                                    <td>
                                                        <strong>'.trans('messages.return_date_lang', [], session('locale')).':</strong>
                                                        '.$booking_data->return_date.'
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <strong>'.trans('messages.total_price_lang', [], session('locale')).':</strong>
                                                        '.$bill_data->total_price.'
                                                    </td>
                                                    <td>
                                                        <strong>'.trans('messages.total_panelty_lang', [], session('locale')).':</strong>
                                                        '.$bill_data->total_penalty.'
                                                    </td>

                                                </tr>
                                                <tr>
                                                    <td>
                                                        <strong>'.trans('messages.discount_lang', [], session('locale')).':</strong>
                                                        '.$bill_data->total_discount.'
                                                    </td>
                                                    <td>
                                                        <strong>'.trans('messages.grand_total_lang', [], session('locale')).':</strong>
                                                        '.$bill_data->grand_total.'
                                                    </td>

                                                </tr>
                                                <tr>
                                                    <td>
                                                        <strong>'.trans('messages.remaining_lang', [], session('locale')).':</strong>
                                                        '.$bill_data->total_remaining.'
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>

                                </div>
                                <div class="tab-pane" id="customer_tab" role="tabpanel">
                                    <div class="table-responsive">
                                        <table class="table table-borderless">
                                            <tbody>
                                                <tr>
                                                    <td>
                                                        <strong>'.trans('messages.customer_name_lang', [], session('locale')).' :</strong> '.$customer_data->customer_name.'
                                                    </td>
                                                    <td>
                                                        <strong>'.trans('messages.customer_contact_lang', [], session('locale')).' :</strong> '.$customer_data->customer_number.'
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <strong>'.trans('messages.customer_email_lang', [], session('locale')).' :</strong> '.$customer_data->customer_email.'
                                                    </td>
                                                    <td>
                                                        <strong>'.trans('messages.dob_lang', [], session('locale')).' :</strong> '.$customer_data->dob.'
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>

                                </div>
                                <div class="tab-pane" id="dress_tab" role="tabpanel">
                                    <div class="table-responsive">
                                        <table class="table table-borderless">
                                            <tbody>
                                                <tr>
                                                    <td>
                                                        <strong>'.trans('messages.dress_name_lang', [], session('locale')).' :</strong> '.$dress_data->dress_name.'
                                                    </td>
                                                    <td>
                                                        <strong>'.trans('messages.category_name_lang', [], session('locale')).' :</strong> '.$category_data->category_name.'
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <strong>'.trans('messages.size_name_lang', [], session('locale')).' :</strong> '.$size_data->size_name.'
                                                    </td>
                                                    <td>
                                                        <strong>'.trans('messages.color_name_lang', [], session('locale')).' :</strong> '.$color_data->color_name.'
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td colspan="2">
                                                        '.$dress_att_div.'
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>

                                </div>
                                <div class="tab-pane" id="payment_tab" role="tabpanel">
                                    <div class="row table-responsive">
                                        '.$payment_detail.'
                                    </div>
                                </div>
                                '.$tab_content_extend.'
                            </div>
                        </div>';
        $edit_btn="";
        $cancel_btn="";
        $extend_btn="";
        $finish_btn="";
        if($booking_data->status==1)
        {
            $edit_btn='<div class="row">
                            <a href="' . url('edit_booking/' . $booking_data->id) . '" class="btn btn-primary btn-sm edit" title="a4 bill">
                                <i class=" fa-2x fas fa-edit"></i>
                            </a>
                        </div>
                        <br>';

        }
        if($booking_data->status==1 || $booking_data->status==2)
        {
            $cancel_btn='<div class="row">
                                <a  class="btn btn-primary btn-sm edit" onclick=cancel_booking("'.$booking_data->booking_no.'") title="cancel bill">
                                    <i class=" fa-2x fas fa-window-close"></i>
                                </a>
                            </div>
                            <br>';
        }
        if($booking_data->status==2)
        {
            $extend_btn='<div class="row">
                            <a class="btn btn-primary btn-sm edit" data-bs-toggle="modal" data-bs-target="#extend_booking_modal" onclick=extend_booking("'.$booking_data->id.'") title="payment">
                                <i class="fa-2x fas fa-chart-line"></i>
                            </a>
                        </div>
                        <br>';
            $finish_btn='<div class="row">
                        <a class="btn btn-primary btn-sm edit" data-bs-toggle="modal" data-bs-target="#finish_booking_modal" onclick=finish_booking("'.$booking_data->id.'") title="finish booking">
                            <i class="fa-2x fas fa-check-circle"></i>
                        </a>
                    </div>
                    <br>';

        }

        $booking_detail.= '<div class="col-md-2">
            '.$edit_btn.'
            <div class="row">
                <a href="' . url('a4_bill/' . $booking_data->booking_no) . '" target="_blank" class="btn btn-primary btn-sm edit" title="a4 bill">
                    <i class=" fa-2x fas fa-file-invoice-dollar"></i>
                </a>
            </div>
            <br>
            <div class="row">
                <a href="' . url('receipt_bill/' . $booking_data->booking_no) . '" target="_blank" class="btn btn-primary btn-sm edit" title="a4 bill">
                    <i class=" fa-2x fas fa-receipt"></i>
                </a>
            </div>
            <br>
            '.$cancel_btn.'
            '.$extend_btn.'
            '.$finish_btn.'
            <div class="row">
                <a class="btn btn-primary btn-sm edit" data-bs-toggle="modal" data-bs-target="#add_payment_modal" onclick=get_payment("'.$bill_data->id.'","'.$booking_data->booking_no.'") title="payment">
                    <i class=" fa-2x fas fa-money-bill"></i>
                </a>
            </div>
        </div>';
        $status = $booking_data->status; // Assuming `status` is the field name for the status in the database
        $status_badge = '';

        if ($status == 1) {
            // Status is "New"
            $status_badge = '<span class="badge bg-warning">'.trans('messages.new_lang',[],session('locale')).'</span>';
        } elseif ($status == 2) {
            // Status is "Rented"
            $status_badge = '<span class="badge bg-primary">'.trans('messages.rent_lang',[],session('locale')).'</span>';
        } elseif ($status == 3) {
            // Status is "Finished"
            $status_badge = '<span class="badge bg-success">'.trans('messages.finish_lang',[],session('locale')).'</span>';
        } elseif ($status == 4) {
            // Status is "Finished"
            $status_badge = '<span class="badge bg-danger">'.trans('messages.cancel_lang',[],session('locale')).'</span>';
        }


        return response()->json(['booking_detail' => $booking_detail,'booking_no'=>$booking_data->booking_no,'status'=>$status_badge]);
	}
    // delete paymen
    public function delete_payment(Request $request){
        $payment_id = $request->input('id');
        // bill update
        // amount addition
        $bill_id="";
        $paid_amount=0;
        $payment_data = BookingPayment::where('id', $payment_id)->first();
        if(!empty($payment_data))
        {
            $bill_id = $payment_data->bill_id;
            $paid_amount = $payment_data->paid_amount;
            $payment_method = $payment_data->payment_method;
        }
        $bill_data = BookingBill::where('id', $bill_id)->first();
        if(!empty($bill_data))
        {
            $last_remaining = $bill_data->total_remaining;
            $new_remaining = $last_remaining + $paid_amount;
            $bill_data->total_remaining = $new_remaining;
            $bill_data->save();
        }
        // amount addition
        $account_data = Account::where('id', $payment_method)->first();
        if(!empty($account_data))
        {
            $last_income = $account_data->opening_balance;
            $new_income = $last_income - $paid_amount;
            $account_data->opening_balance = $new_income;
            $account_data->save();
        }
        // delete payment
        $payment_data->delete();
    }

    // delete booking
    // delete paymen
    public function delete_booking(Request $request){
        $booking_id = $request->input('id');
        $booking_data = Booking::where('id', $booking_id)->first();
        // bill update
        // amount addition
        $bill_id="";
        $paid_amount=0;
        $payment_data = BookingPayment::where('booking_id', $booking_id)->get();
        if(!empty($payment_data))
        {
            foreach ($payment_data as $key => $value) {
                if(!empty($value))
                {
                    $bill_id = $value->bill_id;
                    $paid_amount = $value->paid_amount;
                    $payment_method = $value->payment_method;
                }
                $bill_data = BookingBill::where('id', $bill_id)->first();
                if(!empty($bill_data))
                {
                    $last_remaining = $bill_data->total_remaining;
                    $new_remaining = $last_remaining + $paid_amount;
                    $bill_data->total_remaining = $new_remaining;
                    $bill_data->save();
                }
                // amount addition
                $account_data = Account::where('id', $payment_method)->first();
                if(!empty($account_data))
                {
                    $last_income = $account_data->opening_balance;
                    $new_income = $last_income - $paid_amount;
                    $account_data->opening_balance = $new_income;
                    $account_data->save();
                }
                // delete payment
                $value->delete();
            }
        }
        // delete data
        BookingBill::where('booking_id', $booking_id)->delete();
        Booking::where('id', $booking_id)->delete();
        BookingDressAttribute::where('booking_id', $booking_id)->delete();
        DressHistory::where('booking_id', $booking_id)->delete();
    }
    // edit booking
    public function edit_booking($id){
        $view_dress= Dress::all();
        $view_account = Account::where('account_type', 1)->get();
        // get booking
        $booking_data= Booking::where('id', $id)->first();
        $customer_data= Customer::where('id', $booking_data->customer_id)->first();
        $dress_data= Dress::where('id', $booking_data->dress_id)->first();
        $dress_id =$dress_data->id;
        // dress attributes
        $dress_att_div='<div class="col-md-6">
                                <div class="row">
                                    <h2>'.trans('messages.attribute_lang',[],session('locale')).'</h2>
                                </div>
                                <div class="row accordion" id="accordionExample">';
        $dress_attribute = DressAttribute::where('dress_id', $dress_id)->get();
        if(!empty($dress_attribute))
        {
            foreach ($dress_attribute as $key => $value) {
                $dress_att_div.='<div class="col-md-6">
                                    <div class="accordion-item">
                                        <h2 class="accordion-header" id="heading'.$value->id.'">
                                            <button class="accordion-button fw-medium collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse'.$value->id.'" aria-expanded="false" aria-controls="collapse'.$value->id.'">
                                                '.$value->attribute.'
                                            </button>
                                        </h2>
                                        <div id="collapse'.$value->id.'" class="accordion-collapse collapse" aria-labelledby="headingOne" data-bs-parent="#accordionExample" style="">
                                            <div class="accordion-body">
                                                <div class="text-muted">
                                                    <strong class="text-dark">'.$value->notes.'
                                                    </strong>
                                                </div>
                                            </div>
                                        </div>
                                    </div><br>
                                </div>';
            }
        }
        else
        {
            $dress_att_div.='<div class="col-md-12">
                                <h2>'.trans('messages.no_attribute_lang',[],session('locale')).'</h2>
                            </div>';
        }
        $dress_att_div.='</div>
                            </div>';
        // dress images
        $dress_image_div='<div class="col-md-6">
                                <div class="row">
                                    <h2>'.trans('messages.image_lang',[],session('locale')).'</h2>
                                </div>
                                <div class="row">';
        $images = DressImage::where('dress_id', $dress_id)->get();
        if(!empty($images))
        {
            foreach($images as $rows)
            {

                // Generate the URL for the file
                $url = asset('custom_images/dress_image/' . basename($rows->dress_image));
                $dress_image_div .= '<div class="col-lg-3 col-sm-6">
                            <div class="mt-4">
                                <a href="'.$url.'" class="image-popup">
                                    <img src="'.$url.'" class="img-fluid" alt="work-thumbnail">
                                </a>
                            </div>
                        </div>';
            }
        }
        else
        {
            $dress_image_div.='<div class="col-md-12">
                    <h2>'.trans('messages.no_image_lang',[],session('locale')).'</h2>
                </div>';
        }
        $dress_image_div.='</div>
        </div>';
        $dress_attr= $dress_att_div.$dress_image_div;
        // get sum of paid amount
        $total_paid_payment = BookingPayment::where('booking_id', $booking_data->id)->sum('paid_amount');
        return view ('booking.edit_booking', compact('view_dress','view_account','booking_data','customer_data','dress_data','total_paid_payment','dress_attr'));
    }


    // update_booking
    public function update_booking(Request $request){

         $user_id = Auth::id();
        $data= User::find( $user_id)->first();
        $user= $data->user_name;

        $booking_id  = $request['booking_id'];
        $dress_id  = $request['dress_name'];
        $total_paid_payment  = $request['total_paid_payment'];

        $booking= Booking::where('id', $booking_id)->first();
        $booking->customer_id = $request['customer_id'];
        $booking->booking_date = $request['booking_date'];
        $booking->rent_date = $request['rent_date'];
        $booking->return_date = $request['return_date'];
        $booking->duration = $request['duration'];
        $booking->dress_id = $request['dress_name'];
        $booking->price = $request['price'];
        $booking->discount = $request['discount'];
        $booking->total_price = $request['total_price'];
        $booking->notes = $request['notes'];
        $booking->added_by = $user;
        $booking->user_id = $user_id;
        $booking->save();

        DressHistory::where('booking_id', $booking_id)->delete();
        // add dress booking history
        $dress_history = new DressHistory();
        $dress_history->booking_no = $booking->booking_no;
        $dress_history->booking_id = $booking_id;
        $dress_history->customer_id = $request['customer_id'];
        $dress_history->dress_id = $request['dress_name'];
        $dress_history->rent_date = $request['rent_date'];
        $dress_history->return_date = $request['return_date'];
        $dress_history->type = 1;
        $dress_history->source = "booking";
        $dress_history->history_date = $request['booking_date'];
        $dress_history->notes = $request['notes'];
        $dress_history->added_by = $user;
        $dress_history->user_id = $user_id;
        $dress_history->save();



        // add attribute
        BookingDressAttribute::where('booking_id', $booking_id)->delete();
        $booking_attribute = DressAttribute::where('dress_id', $dress_id)->get();

        if(!empty($booking_attribute))
        {
            foreach ($booking_attribute as $key => $value) {
                $booking_attribute = new BookingDressAttribute();
                $booking_attribute->booking_id = $booking_id;
                $booking_attribute->booking_no = $booking->booking_no;
                $booking_attribute->dress_id = $request['dress_name'];
                $booking_attribute->attribute_id = $value->id;
                $booking_attribute->attribute_name = $value->attribute;
                $booking_attribute->attribute_notes = $value->notes;
                $booking_attribute->added_by = $user;
                $booking_attribute->user_id = $user_id;
                $booking_attribute->save();
            }
        }

        // add booking bill
        $total_price = $request['price'] * $request['duration'];
        $remaining_total = $request['total_price'] - $total_paid_payment;
        $booking_bill= BookingBill::where('booking_id', $booking_id)->first();
        $booking_bill->booking_id = $booking_id;
        $booking_bill->booking_no = $booking->booking_no;
        $booking_bill->total_price = $total_price;
        $booking_bill->total_discount = $total_price-$request['total_price'];
        $booking_bill->grand_total = $request['total_price'];
        $booking_bill->total_remaining = $remaining_total;
        $booking_bill->added_by = $user;
        $booking_bill->user_id = $user_id;
        $booking_bill->save();
        $bill_id = $booking_bill->id;

        // add previous payments
        $payment_data = BookingPayment::where('booking_id', $booking_id)->get();
        $sno=1;
        $total_amount=0;
        foreach ($payment_data as $key => $value) {
            $booking_payment = new BookingPayment();
            if($sno==1)
            {
                $total_amount = $request['total_price'];
            }
            $booking_payment->booking_id = $booking_id;
            $booking_payment->booking_no = $booking->booking_no;
            $booking_payment->bill_id = $value->bill_id;
            $booking_payment->customer_id = $booking->customer_id;
            $booking_payment->total_amount = $total_amount;
            $booking_payment->paid_amount = $value->paid_amount;
            $booking_payment->remaining_amount = $total_amount-$value->paid_amount;
            $booking_payment->payment_date = $value->payment_date;
            $booking_payment->payment_method = $value->payment_method;
            $booking_payment->notes = $value->notes;
            $booking_payment->added_by = $user;
            $booking_payment->user_id = $user_id;
            $booking_payment->save();
            $total_amount = $total_amount-$value->paid_amount;
            $sno++;
            BookingPayment::where('id', $value->id)->delete();
        }


        return response()->json(['booking_id' => $booking_id,'bill_id' => $bill_id]);

    }

    // cancel booking
    public function cancel_booking(Request $request){
        $booking_id = $request->input('id');
        $user_id = Auth::id();
        $data= User::find( $user_id)->first();
        $user= $data->user_name;
        // bill update
        // amount addition
        $bill_id="";
        $paid_amount=0;
        $booking_data = Booking::where('id', $booking_id)->first();

        $booking_data->status = 4;
        $booking_data->cancel_by = $user_id;
        $booking_data->cancel_date = date('Y-m-d');
        $booking_data->status = 4;
        $booking_data->save();
    }

    // extend booking
    public function get_booking_data(Request $request){
        $booking_id = $request->input('booking_id');
        $user_id = Auth::id();
        $data= User::find( $user_id)->first();
        $user= $data->user_name;
        // bill update
        $booking_data = Booking::where('id', $booking_id)->first();
        return response()->json(['old_rent_date' => $booking_data->rent_date,'return_date' => $booking_data->return_date,'price' => $booking_data->price,'discount' => $booking_data->discount,'dress_id' => $booking_data->dress_id]);

    }

    public function get_extend_dress_detail(Request $request)
	{
        $dress_id = $request['dress_id'];
        $rent_date = $request['rent_date'];  // Correct Carbon usage
        $return_date = $request['return_date']; // Correct Carbon usage
        // setting data
        $setting_data = Setting::first();
        // Query to check if any booking exists with matching dress_id and date ranges overlap
        if($setting_data->dress_available <= 0 || empty($setting_data->dress_available))
        {
            $days = 1;
        }
        else
        {
            $days = intval($setting_data->dress_available);
        }
        $rent_date_adjusted = Carbon::parse($rent_date)->addDays(1); // 2 days before rent date
        // $return_date_adjusted = Carbon::parse($return_date)->addDays($days); // 2 days after return date
        // $rent_date_adjusted = Carbon::parse($rent_date); // 2 days before rent date
        $return_date_adjusted = Carbon::parse($return_date); // 2 days after re
        $existingBooking = Booking::where(function ($query) use ($rent_date_adjusted, $return_date_adjusted, $dress_id) {
            $query->whereDate('rent_date', '>=', $rent_date_adjusted)
                ->whereDate('rent_date', '<=', $return_date_adjusted)
                ->where('dress_id', $dress_id);
        })
        ->orWhere(function ($query) use ($rent_date_adjusted, $return_date_adjusted, $dress_id) {
            $query->whereDate('return_date', '>=', $rent_date_adjusted)
                ->whereDate('return_date', '<=', $return_date_adjusted)
                ->where('dress_id', $dress_id);
        })
        ->first();

        if (!empty($existingBooking)) {
            // Handle case where a booking exists
            return response()->json(['status'=>2]);
            exit;
        }
        // validataion for maintanance

        // Validation for maintenance check
        $conflictingDresses = Dress::where('status', 2)
            ->where(function ($query) use ($rent_date, $return_date) {
                // Use the correct Carbon format and pass as values in whereBetween
                $query->whereBetween('start_date', [Carbon::parse($rent_date), Carbon::parse($return_date)])
                    ->orWhereBetween('end_date', [Carbon::parse($rent_date), Carbon::parse($return_date)]);
            })->exists();

        if ($conflictingDresses) {
            // Handle case where a booking exists
            return response()->json(['status'=>3]);
            exit;
        }
        // dress data
        $dress_data = Dress::where('id', $dress_id)->first();

        return response()->json(['status'=>1,'price'=>$dress_data->price]);
	}

    // add extend booking
    public function add_extend_booking(Request $request){

        $user_id = Auth::id();
        $data= User::find( $user_id)->first();
        $user= $data->user_name;

        $booking_id = $request->input('booking_id');
        $dress_id = $request->input('dress_id');
        $old_rent_date = $request->input('old_rent_date');
        $return_date = $request->input('return_date');
        $new_return_date = $request->input('new_return_date');
        $duration = $request->input('duration');
        $price = $request->input('price');
        $discount = $request->input('discount');
        $total_price = $request->input('total_price');
        $extend_notes = $request->input('extend_notes');

        $booking_data = Booking::where('id', $booking_id)->first();
        //add history extend
        $extend_history = new BookingExtendHistory();
        $extend_history->booking_no = $booking_data->booking_no;
        $extend_history->booking_id = $booking_id;
        $extend_history->customer_id = $booking_data->customer_id;
        $extend_history->dress_id = $dress_id;
        $extend_history->rent_date = $old_rent_date;
        $extend_history->return_date = $return_date;
        $extend_history->duration = $booking_data->duration;
        $extend_history->price = $booking_data->price;
        $extend_history->discount = $booking_data->discount;
        $extend_history->total_price = $booking_data->total_price;
        $extend_history->type = 1;
        $extend_history->notes = $extend_notes;
        $extend_history->added_by = $user;
        $extend_history->user_id = $user_id;
        $extend_history->save();

        $new_duration = calculateDays($return_date, $new_return_date);
        $new_discount = ($new_duration*$price) /100 * $discount;
        $new_total_price = ($new_duration*$price) - $new_discount;

        $extend_history = new BookingExtendHistory();
        $extend_history->booking_no = $booking_data->booking_no;
        $extend_history->booking_id = $booking_id;
        $extend_history->customer_id = $booking_data->customer_id;
        $extend_history->dress_id = $dress_id;
        $extend_history->rent_date = $return_date;
        $extend_history->return_date = $new_return_date;
        $extend_history->duration = $new_duration;
        $extend_history->price = $price;
        $extend_history->discount = $discount;
        $extend_history->total_price = $new_total_price;
        $extend_history->type = 2;
        $extend_history->notes = $extend_notes;
        $extend_history->added_by = $user;
        $extend_history->user_id = $user_id;
        $extend_history->save();


        // add dress booking history
        $dress_history = new DressHistory();
        $dress_history->booking_no = $booking_data->booking_no;
        $dress_history->booking_id = $booking_id;
        $dress_history->customer_id = $booking_data->customer_id;
        $dress_history->dress_id = $dress_id;
        $dress_history->rent_date = $return_date;
        $dress_history->return_date = $new_return_date;
        $dress_history->type = 1;
        $dress_history->source = "extend_booking";
        $dress_history->history_date = date('Y-m-d');
        $dress_history->notes =  $extend_notes;
        $dress_history->added_by = $user;
        $dress_history->user_id = $user_id;
        $dress_history->save();

        // update booking
        $booking_data = Booking::where('id', $booking_id)->first();

        $booking_data->return_date = $new_return_date;
        $booking_data->total_price = $total_price;
        $booking_data->save();

        // update bill
        $bill_data = BookingBill::where('booking_id', $booking_id)->first();
        // sum paid amoun t
        $totalPaid = BookingPayment::where('booking_id', $booking_id)->sum('paid_amount');
        $remaining_total = $total_price - $totalPaid;
        $before_discount_price = $duration * $price;
        $final_discount = ($before_discount_price) /100 * $discount;

        $bill_data->total_remaining = $remaining_total;
        $bill_data->grand_total = $total_price;
        $bill_data->total_price = $before_discount_price;
        $bill_data->total_discount = $final_discount;
        $bill_data->save();
    }

    public function get_finish_booking_detail(Request $request)
	{
        $booking_id = $request['booking_id'];

        // dress data
        $booking_data = Booking::where('id', $booking_id)->first();
		// dress attributes
        $dress_att_div='';
        $dress_attribute = DressAttribute::where('dress_id', $booking_data->dress_id)->get();
        $all_attributes_id=[];
        if(!empty($dress_attribute))
        {

            foreach ($dress_attribute as $key => $value) {
                $dress_att_div.='<div class="col-md-3">
                                    <div class="form-check mb-3">
                                        <input type="hidden" value="'.$value->id.'" name="attribute_hidden_id[]">
                                        <input class="form-check-input attributes_id" type="checkbox" name="attributes_id[]" value="'.$value->id.'" id="formCheck'.$value->id.'">
                                        <label class="form-check-label" for="formCheck'.$value->id.'">
                                            '.$value->attribute.'
                                        </label>
                                    </div>
                                 </div>
                                 <div class="col-md-2">
                                    <input type="text" class="form-control panelty_price isnumber" name="panelty_price[]" value="0">
                                 </div>
                                 <div class="col-md-7">
                                    <textarea class="form-control fault_notes" name="fault_notes[]" rows="3" placeholder="'.trans('messages.notes_lang',[],session('locale')).'"></textarea>
                                 </div><br>';
                $all_attributes_id[]=$value->id;
            }
        }
        else
        {
            $dress_att_div.='<div class="col-md-12">
                                <h2>'.trans('messages.no_attribute_lang',[],session('locale')).'</h2>
                            </div>';
        }
        $all_attributes_ids = implode(',',$all_attributes_id);
        $dress_att_div.='<input type="hidden" name="all_attributes" value="'.$all_attributes_ids.'">';

        return response()->json(['status'=>1,'detail' => $dress_att_div,'booking_no' => $booking_data->booking_no]);
	}


    // add finish booking
    public function add_finish_booking(Request $request){

        $user_id = Auth::id();
        $data= User::find( $user_id)->first();
        $user= $data->user_name;

        $booking_id = $request->input('booking_id');
        $all_attributes  = explode(',',$request->input('all_attributes'));
        $panelty_price = $request->input('panelty_price');

        $fault_notes = $request->input('fault_notes');
        $checked_attributes = $request->input('attributes_id');

        $checked_attributes = $request->input('attributes_id');

        $total_penalty =0;
        foreach ($all_attributes as $index => $attribute_id) {
            $is_checked = in_array($attribute_id, $checked_attributes);



            // Set the penalty price based on whether the checkbox is checked or not
            $penalty_price = isset($panelty_price[$index]) ? $panelty_price[$index] : 0;

            // Add to total penalty if the penalty price is greater than 0
            if ($penalty_price > 0) {
                $total_penalty += $penalty_price;
            }
            //

            // Fetch the booking attributes record
            $booking_attributes = BookingDressAttribute::where('booking_id', $booking_id)
                                ->where('attribute_id', $attribute_id)
                                ->first();

            // Update the status based on whether the checkbox is checked or not
            $booking_attributes->status = $is_checked ? 2 : 3;

            // Update the penalty price (it's already a scalar value, not an array)
            $booking_attributes->penalty_price = $penalty_price;

            $booking_attributes->fault_notes = $fault_notes[$index];




            // Save the updated attributes
            $booking_attributes->save();
        }


        $booking_data = Booking::where('id', $booking_id)->first();

        // update booking
        $booking_data->status = 3;

        // update booking
        $booking_data->status = $user;

        $booking_data->finish_by = $user_id;
        $booking_data->finish_date = date('Y-m-d H:i:s');
        $booking_data->save();

        // add dress booking history
        $dress_history = new DressHistory();
        $dress_history->booking_no = $booking_data->booking_no;
        $dress_history->booking_id = $booking_id;
        $dress_history->customer_id = $booking_data->customer_id;
        $dress_history->dress_id = $booking_data->dress_id;
        $dress_history->rent_date = $booking_data->rent_date;
        $dress_history->return_date =$booking_data->return_date;
        $dress_history->type = 2;
        $dress_history->source = "finish_booking";
        $dress_history->history_date = date('Y-m-d');
        $dress_history->added_by = $user;
        $dress_history->user_id = $user_id;
        $dress_history->save();


        // update bill
        $bill_data = BookingBill::where('booking_id', $booking_id)->first();
        // sum paid amoun t
        $totalPaid = BookingPayment::where('booking_id', $booking_id)->sum('paid_amount');
        $remaining_total = $booking_data->total_price + $total_penalty - $totalPaid;
        $before_discount_price = $booking_data->duration * $booking_data->price;
        $grand_total = $before_discount_price + $total_penalty;

        $bill_data->total_remaining = $remaining_total;
        $bill_data->grand_total = $grand_total - $bill_data->total_discount;
        $bill_data->total_penalty = $total_penalty;
        $bill_data->save();
    }





    public function a4_bill($id){


        $booking = Booking::with([
            'bill',
            'payments',
            'extention',
            'dress.brand',
            'dress.category',
            'dress.color',
            'dress.size'
        ])->where('booking_no', $id)->first();

        $setting= Setting::first();

        $account = $booking && $booking->payments->isNotEmpty()
        ? Account::where('id', $booking->payments->first()->payment_method)->first()
        : null;



        $extention = BookingExtendHistory::where('booking_no', $id)->get();
        $sumPaidAmount = BookingPayment::where('booking_no', $id)->sum('paid_amount');
            $total = $booking->bill->grand_total;
            $remain = $total - $sumPaidAmount;




        $customer= Customer::where('id', $booking->customer_id)->first();

        return view('bills.bill_detail', compact('booking', 'customer', 'setting', 'account', 'remain', 'sumPaidAmount', 'extention'));
    }



    // receipt bill
    public function receipt_bill($booking_no)
    {

        $booking_data = Booking::where('booking_no', $booking_no)->first();
        $dress_data = Dress::where('id', $booking_data->dress_id)->first();
        $payment_data = BookingPayment::where('booking_no', $booking_no)->get();
        $bill_data = BookingBill::where('booking_no', $booking_no)->first();



        $setting_data = Setting::first();

        $account_names = [];
        foreach ($payment_data as $key => $pay) {
            $acc = Account::where('id', $pay->payment_method)->first();
            if($acc)
            {
                $account_names[]= $acc->account_name;
            }
        }
        $account_name = implode(',',array_unique($account_names));
        $total_paid = BookingPayment::where('booking_no', $booking_no)->sum('paid_amount');


        $user = User::where('id', $booking_data->user_id)->first();

        return view('booking.receipt_bill', compact('total_paid','bill_data','dress_data' , 'booking_data','setting_data', 'payment_data','user', 'account_name' ));
    }
}
