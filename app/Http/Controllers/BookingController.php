<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Dress;
use App\Models\DressAttribute;
use App\Models\DressImage;
use App\Models\Customer;
use App\Models\Booking;
use App\Models\BookingBill;
use App\Models\BookingDressAttribute;
use App\Models\Account;
use App\Models\BookingPayment;
use App\Models\DressHistory;
use App\Models\DressAvailability;
use Carbon\Carbon;


class BookingController extends Controller
{
    public function index(){
        $view_dress= Dress::all();
        $view_account = Account::where('account_type', 1)->get();
        return view ('booking.add_booking', compact('view_dress','view_account'));
    }
    public function get_dress_detail(Request $request)
	{
        $dress_id = $request['dress_id'];
        $rent_date = $request['rent_date'];  // Correct Carbon usage
        $return_date = $request['return_date']; // Correct Carbon usage

        // Query to check if any booking exists with matching dress_id and date ranges overlap
        $existingBooking = Booking::whereDate('rent_date', '>=', $rent_date)
                            ->whereDate('rent_date', '<=', $return_date)
                            ->where('dress_id', $dress_id)
                            ->first();

        if (!empty($existingBooking)) {
            // Handle case where a booking exists
            return response()->json(['status'=>2]);
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

        // $user_id = Auth::id();
        // $data= User::find( $user_id)->first();
        // $user= $data->username;
        $user_id="1";
        $user="admin";
        $dress_avail = new DressAvailability();
        
        $dress_avail->contact = $request['number'];
        $dress_avail->dress_id = $request['dress_id'];
        $dress_avail->added_by = $user;
        $dress_avail->user_id = $user_id;
        $dress_avail->save();
    }


    public function add_booking(Request $request){

        // $user_id = Auth::id();
        // $data= User::find( $user_id)->first();
        // $user= $data->username;
        $user_id="1";
        $user="admin";

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

        // $user_id = Auth::id();
        // $data= User::find( $user_id)->first();
        // $user= $data->username;
        $user_id="1";
        $user="admin";
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

        // $user_id = Auth::id();
        // $data= User::find( $user_id)->first();
        // $user= $data->username;
        $user_id="1";
        $user="admin";
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

        // $user_id = Auth::id();
        // $data= User::find( $user_id)->first();
        // $user= $data->username;
        $user_id="1";
        $user="admin";
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
}
