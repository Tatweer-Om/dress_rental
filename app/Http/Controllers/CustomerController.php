<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Booking;
use App\Models\Customer;
use App\Models\BookingBill;
use Illuminate\Http\Request;
use App\Models\BookingPayment;

class CustomerController extends Controller
{
    public function index(){

        return view ('customer.customer');

    }

    public function show_customer()
    {
        $sno=0;

        $view_customer= Customer::all();
        if(count($view_customer)>0)
        {
            foreach($view_customer as $value)
            {

                $customer_name='<a href="customer_profile/' . $value->id . '">' . ($value->customer_name) . '</a>';

                $modal='<a class="btn btn-outline-secondary btn-sm edit" data-bs-toggle="modal" data-bs-target="#add_customer_modal" onclick=edit("'.$value->id.'") title="Edit">
                            <i class="fas fa-pencil-alt" title="Edit"></i>
                        </a>
                        <a class="btn btn-outline-secondary btn-sm edit" onclick=del("'.$value->id.'") title="Delete">
                            <i class="fas fa-trash" title="Edit"></i>
                        </a>';
                $add_data=get_date_only($value->created_at);



                $sno++;
                $json[] = array(
                    $sno,
                    '<span>اسم العميل: ' . $customer_name . '</span><br>' .
                    '<span>الجنس: ' . ($value->gender == 1 ? 'ذكر' : 'أنثى') . '</span><br>' .  // Add the dot here
                    '<span>تاريخ الميلاد: ' . $value->dob . '</span>',
                    $value->customer_number . '<br>' . $value->customer_email,
                    $value->address,
                    '<span>أضيف بواسطة: ' . $value->added_by . '</span><br>' .
                    '<span>تاريخ الإضافة: ' . $add_data . '</span>',
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

    public function add_customer(Request $request){


        // $user_id = Auth::id();
        // $data= User::where('id', $user_id)->first();

        // $user= $data->username;

        $customer = new Customer();


        $existingCustomer = Customer::where('customer_number', $request['customer_number'])->first();
        if ($existingCustomer) {

            return response()->json(['customer_id' => '', 'status' => 3]);
            exit;
        }


        $customer->customer_name = $request['customer_name'];
        $customer->customer_number = $request['customer_number'];
        $customer->customer_email = $request['customer_email'];
        $customer->dob = $request['dob'];
        $customer->gender = $request['gender'];
        $customer->discount = $request['customer_discount'];
        $customer->address = $request['address'];
        $customer->added_by = 'admin';
        $customer->user_id = 1;
        $customer->save();
        // customer add sms
        // $params = [
        //     'customer_id' => $customer->id,
        //     'sms_status' => 1
        // ];
        // $sms = get_sms($params);
        // sms_module($request['customer_phone'], $sms);

        //
        return response()->json(['customer_id' => $customer->id , 'status' => 1]);

    }

    public function edit_customer(Request $request){
        $customer = new Customer();
        $customer_id = $request->input('id');

        // Use the Eloquent where method to retrieve the customer by column name
        $customer_data = Customer::where('id', $customer_id)->first();

        if (!$customer_data) {
            return response()->json(['error' => trans('messages.customer_not_found', [], session('locale'))], 404);
        }


        $data = [
            'customer_id' => $customer_data->id,
            'customer_name' =>   $customer_data->customer_name,
            'customer_email' =>  $customer_data->customer_email,
            'customer_number' => $customer_data->customer_number,
            'discount'  =>  $customer_data->discount,
            'dob' => $customer_data->dob,
            'gender' => $customer_data->gender,
            'address' => $customer_data->address,


        ];

        return response()->json($data);
    }

    public function update_customer(Request $request){


        // $user_id = Auth::id();
        // $data= User::find( $user_id)->first();
        // $user= $data->username;

        $customer_id = $request->input('customer_id');
        $customer = Customer::where('id', $customer_id)->first();
        if (!$customer) {
            return response()->json(['error' => trans('messages.customer_not_found', [], session('locale'))], 404);
        }



        $customer->customer_name = $request['customer_name'];
        $customer->customer_number = $request['customer_number'];
        $customer->customer_email = $request['customer_email'];
        $customer->dob = $request['dob'];
        $customer->gender = $request['gender'];
        $customer->discount = $request['customer_discount'];
        $customer->address = $request['address'];
        $customer->updated_by = 'admin';
        $customer->save();
        return response()->json(['customer_id' => '', 'status' => 1]);
    }

    public function delete_customer(Request $request){
        $customer_id = $request->input('id');
        $customer = Customer::where('id', $customer_id)->first();
        if (!$customer) {
            return response()->json(['error' => trans('messages.customer_not_found', [], session('locale'))], 404);
        }
        $customer->delete();
        return response()->json([
            'success' => trans('messages.customer_deleted_lang', [], session('locale'))
        ]);


    }

    public function customer_profile($id){

        $customer= Customer::where('id', $id)->first();

        return view ('customer.customer_profile', compact('customer'));


    }

    public function customer_profile_data(Request $request)
    {

        $customer = Customer::find($request->customer_id);
        if (!$customer) {
            return response()->json(['message' => 'Customer not found'], 404);
        }
        $bookings = Booking::with([
            'bills',
            'payments',
            'dress.brand',
            'dress.category',
            'dress.color',
            'dress.size'
        ])->where('customer_id', $customer->id)->get();

        $upcoming_bookings = Booking::with([
            'bills',
            'payments',
            'dress.brand',
            'dress.category',
            'dress.color',
            'dress.size'
        ])
        ->where('customer_id', $customer->id)
        ->where('rent_date', '>', Carbon::now())
        ->get();

        $upcoming_bookings_count= $upcoming_bookings->count();
        $total_bookings= $bookings->count();
        $total_amount = 0;
        $total_panelty=0;
        foreach ($bookings as $booking) {

            foreach ($booking->payments as $payment) {
                $total_amount += $payment->paid_amount;
            }
        }
        
        foreach ($bookings as $booking) { 
            foreach ($booking->bills as $payment) {
                echo $payment->total_panelty;
                $total_panelty += $payment->total_penalty;
            }
        }
         

        $currentBookings = Booking::with([
            'bills',
            'payments',
            'dress.brand',
            'dress.category',
            'dress.color',
            'dress.size'
        ])
        ->where('customer_id', $customer->id)
        ->whereDate('rent_date', '<=', Carbon::now())
        ->whereDate('return_date', '>=', Carbon::now())
        ->get();

        return response()->json([
            'bookings' => $bookings,
            'up_bookings'=> $upcoming_bookings,
            'total_amount'=>$total_amount,
            'total_bookings'=>$total_bookings,
            'upcoming_bookings_count'=>$upcoming_bookings_count,
            'total_panelty'=>$total_panelty,
            'current_bookings'=>$currentBookings


        ]);
    }






}
