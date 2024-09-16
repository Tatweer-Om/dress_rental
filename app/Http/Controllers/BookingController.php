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

class BookingController extends Controller
{
    public function index(){
        $view_dress= Dress::all();
        return view ('booking.add_booking', compact('view_dress'));
    }
    public function get_dress_detail(Request $request)
	{
        $dress_id = $request->input('dress_id');
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

        return response()->json(['dress_detail' => $dress_att_div.$dress_image_div,'price'=>$dress_data->price]);
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
        $booking_bill->total_price = $request['price'];
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
                ];
 
            }
        }
        

        return response()->json($response);
    }

    public function add_booking_customer(Request $request){

        // $user_id = Auth::id();
        // $data= User::find( $user_id)->first();
        // $user= $data->username;
        $user_id="";
        $user="";

        $customer = new Customer();
        
        $customer->customer_name = $request['customer_names'];
        $customer->customer_contact = $request['customer_contact'];
        $customer->customer_email = $request['customer_email'];
        $customer->dob = $request['dob'];
        $customer->gender = $request['gender'];
        $customer->discount = $request['discount'];
        $customer->address = $request['address'];
        $customer->added_by = $user;
        $customer->user_id = $user_id;
        $customer->save();
        return response()->json(['customer_id' => $customer->id]);

    }
}
