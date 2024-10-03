<?php

namespace App\Http\Controllers;

use App\Models\Size;
use App\Models\User;
use App\Models\Brand;
use App\Models\Color;
use App\Models\Dress;
use App\Models\Maintgo;
use App\Models\Category;
use App\Models\DressImage;
use Illuminate\Http\Request;
use App\Models\DressAttribute;
use App\Models\Booking;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Carbon\Carbon;
use App\Models\Customer;
use App\Models\BookingBill;
use App\Models\BookingPayment;
use Illuminate\Support\Facades\Storage;

class DressController extends Controller
{
    public function index(){
        $view_color= Color::all();
        $view_size= Size::all();
        $view_category= Category::all();
        $view_brand= Brand::all();

        if (!Auth::check()) {

            return redirect()->route('login_page')->with('error', 'Please LogIn first()');
        }

        $user = Auth::user();

        if (in_array(2, explode(',', $user->permit_type))) {

            return view ('dress.dress', compact('view_color','view_size','view_category','view_brand'));

        } else {

            return redirect()->route('home')->with( 'error', 'You dont have Permission');
        }
    }
    public function show_dress()
    {
        $sno=0;

        $view_dress= dress::all();
        if(count($view_dress)>0)
        {
            foreach($view_dress as $value)
            {
                $category = getColumnValue('categories','id',$value->category_name,'category_name');
                $modal = '<a class="btn btn-outline-secondary btn-sm edit" data-bs-toggle="modal" data-bs-target="#add_dress_modal" onclick="edit(' . $value->id . ')" title="Edit">
                <i class="fas fa-pencil-alt" title="Edit"></i>
                </a>
                <a class="btn btn-outline-secondary btn-sm delete" onclick="del(' . $value->id . ')" title="Delete">
                        <i class="fas fa-trash" title="Delete"></i>
                </a>';

                // Conditional rendering based on dress status
                if ($value->status == 1) {
                    $modal .= ' <a class="btn btn-outline-secondary btn-sm" data-bs-toggle="modal" data-bs-target="#return_maint_modal" onclick="maint(' . $value->id . ')" title="Maintenance">
                                    <i class="fa-solid fa-gears"> Maintenance</i>
                            </a>';
                } else {
                    $modal .= ' <a class="btn btn-outline-secondary btn-sm" title="Under Maintenance">
                                    <i class="fa-solid fa-gears"> Under Maint</i>
                            </a>';
                }
                $add_data=get_date_only($value->created_at);
                if($value->condition == 1)
                {
                    $condition = trans('messages.new_lang',[],session('locale'));
                }
                else
                {
                    $condition = trans('messages.used_lang',[],session('locale'));
                }
                $sno++;
                $json[]= array(
                            $sno,
                            $value->sku,
                            $value->dress_name,
                            $category,
                            $value->price,
                            $condition,
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

    public function add_dress(Request $request){

        $user_id = Auth::id();
        $data= User::find( $user_id)->first();
        $user= $data->user_name;


        $dress = new dress();
        $dress_img="";
        if ($request->hasFile('dress_image')) {
            $folderPath = public_path('custom_images/dress_image');

            // Check if the folder doesn't exist, then create it
            if (!File::isDirectory($folderPath)) {
                File::makeDirectory($folderPath, 0777, true, true);
            }
            $dress_img = time() . '.' . $request->file('dress_image')->extension();
            $request->file('dress_image')->move(public_path('custom_images/dress_image'), $dress_img);
        }

        $dress->dress_image = $dress_img;
        $dress->dress_name = $request['dress_name'];
        $dress->color_name = $request['color_name'];
        $dress->size_name = $request['size_name'];
        $dress->category_name = $request['category_name'];
        $dress->brand_name = $request['brand_name'];
        $dress->sku = $request['sku'];
        $dress->price = $request['price'];
        $dress->condition = $request['condition'];
        $dress->notes = $request['notes'];
        $dress->added_by = $user;
        $dress->user_id = $user_id;
        $dress->save();
        $dress_id = $dress->id;

        // multiple images
        // Check if path exists and get the list of files from the directory
        // Check if path exists and get the list of files from the directory
        $sourcePath = public_path('custom_images/temp_data'); // Source directory
        $destinationDir = public_path('custom_images/dress_image/'); // Destination directory

        if (is_dir($sourcePath)) {
            $files = File::files($sourcePath); // Fetch files using File facade

            // Create the destination directory if it doesn't exist
            if (!File::isDirectory($destinationDir)) {
                File::makeDirectory($destinationDir, 0777, true, true);
            }

            foreach ($files as $file) {
                // Get the file extension
                $ext = pathinfo($file, PATHINFO_EXTENSION);

                // Generate the new file name
                $newFileName = 'dress_' . time() . '_' . rand(1000, 9999) . '.' . $ext;

                // Define the destination path
                $destinationPath = $destinationDir . $newFileName;

                // Move the file to the new folder
                if (File::move($file->getPathname(), $destinationPath)) {
                    // Generate the URL for the new file location
                    $url = asset('custom_images/dress_image/' . $newFileName);

                    // Save the file information in the DressImage model
                    $dressImage = new DressImage();
                    $dressImage->dress_id = $dress_id; // Assuming $dress_id is provided
                    $dressImage->dress_image = $newFileName; // Save the image URL
                    $dressImage->save();
                } else {
                    // Handle the error if the file could not be moved
                    return response()->json(['success' => false, 'message' => 'Failed to move file: ' . $file->getFilename()]);
                }
            }
        }

        // add attribute
        $attribute_id = $request->input('attribute_id', []);
        $attribute_name = $request->input('attribute_name', []);
        $attribute_notes = $request->input('attribute_notes', []);

        for ($i=0; $i < count($attribute_name) ; $i++) {
            if(!empty($attribute_id[$i]))
            {
                $dress_attribute = new DressAttribute();
                $dress_attribute->id = $attribute_id[$i];
                $dress_attribute->dress_id = $dress_id;
                $dress_attribute->attribute = $attribute_name[$i];
                $dress_attribute->notes = $attribute_notes[$i];
                $dress_attribute->added_by = $user;
                $dress_attribute->user_id = $user_id;
                $dress_attribute->save();
            }
            else
            {
                $dress_attribute = new DressAttribute();
                $dress_attribute->dress_id = $dress_id;
                $dress_attribute->attribute = $attribute_name[$i];
                $dress_attribute->notes = $attribute_notes[$i];
                $dress_attribute->added_by = $user;
                $dress_attribute->user_id = $user_id;
                $dress_attribute->save();
            }

        }
        return response()->json(['dress_id' => $dress_id]);

    }

    public function edit_dress(Request $request){
        $dress = new dress();
        $dress_id = $request->input('id');

        // Use the Eloquent where method to retrieve the dress by column name
        $dress_data = dress::where('id', $dress_id)->first();

        // images
        $images = null;
        $dress_image = DressImage::where('dress_id', $dress_id)->get();
        if(!empty($dress_image))
        {
            foreach($dress_image as $rows)
            {
                // Generate the URL for the file
                $url = asset('custom_images/dress_image/' . basename($rows->dress_image));
                $images .= '<div class="col-xl-4 col-lg-4 col-md-4 col-sm-6 col-xs-12">
                                        <img class="img-thumbnail mb-1" src="'.$url.'" style="max-height:60px !important;min-height:60px !important;max-width:60px;min-width:60px;">
                                        <p class="text-center">
                                        <a href="#" class="card-link e-rmv-attachment" id="'.$rows->id.'">
                                            <i class="fa fa-times"></i>
                                        </a>
                                        </p>
                                </div>';
            }
        }
        // attributes
        $attributes = null;
        $dress_attribute = DressAttribute::where('dress_id', $dress_id)->get();
        if(!empty($dress_attribute))
        {
            foreach($dress_attribute as $att)
            {

                $attributes .= '<div class="row attribute_div">
                                    <div class="col-md-4">
                                        <input type="hidden" class="attribute_id" name="attribute_id[]" value="'.$att->id.'">
                                        <div class="mb-3">
                                            <label for="attribute_name" class="form-label">'.trans('messages.attribute_name_lang', [], session('locale')).'</label>
                                            <input class="form-control attribute_name" name="attribute_name[]" value="'.$att->attribute.'" type="text">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="attribute_notes" class="form-label">'.trans('messages.notes_lang', [], session('locale')).'</label>
                                            <textarea class="form-control attribute_notes" rows="3" name="attribute_notes[]">'.$att->notes.'</textarea>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="mb-3">
                                            <button type="button" style="margin-top: 40px;" class="btn btn-primary del_attribute"><i class="fas fa-trash"></i></button>
                                        </div>
                                    </div>
                                </div>';
            }
        }
        // Add more attributes as needed
        $data = [
            'dress_id' => $dress_data->id,
            'dress_name' => $dress_data->dress_name,
            'category_name' => $dress_data->category_name,
            'brand_name' => $dress_data->brand_name,
            'size_name' => $dress_data->size_name,
            'color_name' => $dress_data->color_name,
            'sku' => $dress_data->sku,
            'price' => $dress_data->price,
            'condition' => $dress_data->condition,
            'notes' => $dress_data->notes,
            'dress_image' => $dress_data->dress_image,
            'all_images' => $images,
            'attributes' => $attributes
            // Add more attributes as needed
        ];

        return response()->json($data);
    }

    public function update_dress(Request $request){

        $user_id = Auth::id();
        $data= User::find( $user_id)->first();
        $user= $data->user_name;

        $dress_id = $request->input('dress_id');
        $dress = dress::where('id', $dress_id)->first();
        $dress_img="";
        if ($request->hasFile('dress_image')) {
            $folderPath = public_path('custom_images/dress_image');

            // Check if the folder doesn't exist, then create it
            if (!File::isDirectory($folderPath)) {
                File::makeDirectory($folderPath, 0777, true, true);
            }
            $dress_img = time() . '.' . $request->file('dress_image')->extension();
            $request->file('dress_image')->move(public_path('custom_images/dress_image'), $dress_img);
        }

        $dress->dress_image = $dress_img;
        $dress->dress_name = $request['dress_name'];
        $dress->color_name = $request['color_name'];
        $dress->size_name = $request['size_name'];
        $dress->category_name = $request['category_name'];
        $dress->brand_name = $request['brand_name'];
        $dress->sku = $request['sku'];
        $dress->price = $request['price'];
        $dress->condition = $request['condition'];
        $dress->notes = $request['notes'];
        $dress->added_by = $user;
        $dress->user_id = $user_id;
        $dress->save();

        // add attribute
        $attribute_id = $request->input('attribute_id');
        $attribute_name = $request->input('attribute_name');
        $attribute_notes = $request->input('attribute_notes');
        // attribute delete
        DressAttribute::where('dress_id', $dress_id)->delete();
        for ($i=0; $i < count($attribute_name) ; $i++) {
            if(!empty($attribute_id[$i]))
            {
                $dress_attribute = new DressAttribute();
                $dress_attribute->id = $attribute_id[$i];
                $dress_attribute->dress_id = $dress_id;
                $dress_attribute->attribute = $attribute_name[$i];
                $dress_attribute->notes = $attribute_notes[$i];
                $dress_attribute->added_by = $user;
                $dress_attribute->user_id = $user_id;
                $dress_attribute->save();
            }
            else
            {
                $dress_attribute = new DressAttribute();
                $dress_attribute->dress_id = $dress_id;
                $dress_attribute->attribute = $attribute_name[$i];
                $dress_attribute->notes = $attribute_notes[$i];
                $dress_attribute->added_by = $user;
                $dress_attribute->user_id = $user_id;
                $dress_attribute->save();
            }

        }

    }



    public function delete_dress(Request $request)
    {
        // Get the dress ID from the request
        $dress_id = $request->input('id');

        // Find the dress by ID
        $dress = Dress::where('id', $dress_id)->first();

        // Check if the dress exists
        if ($dress) {
            // Retrieve related DressImage records
            $images = DressImage::where('dress_id', $dress_id)->get();

            // Loop through each image record
            foreach ($images as $image) {
                // Define the image path
                $imagePath = public_path('custom_images/dress_image/' . $image->dress_image);

                // Check if the image file exists and delete it
                if (File::exists($imagePath)) {
                    File::delete($imagePath);
                }

                // Delete the image record from the DressImage table
                $image->delete();
            }

            // Define the image path
            $imagePath = public_path('custom_images/dress_image/' . $dress->dress_image);

            // Check if the image file exists and delete it
            if (File::exists($imagePath)) {
                File::delete($imagePath);
            }
            // Delete the dress record
            $dress->delete();
        }

        // You can return a response or redirect as needed
        return response()->json(['message' => 'Dress and associated images deleted successfully.']);
    }

    public function upload_attachments(Request $request)
    {
        $user_id = Auth::id();
        $data= User::find( $user_id)->first();
        $user= $data->user_name;

        $dress_id      = $request->input('dress_id');
		$msg=null;

        if(!empty($dress_id))
        {
            // Check if the request contains files
            if ($request->hasFile('attachments')) {
                $files = $request->file('attachments');
                $folderPath = public_path('custom_images/dress_image');

                // Check if the folder doesn't exist, then create it
                if (!File::isDirectory($folderPath)) {
                    File::makeDirectory($folderPath, 0777, true, true);
                }

                foreach ($files as $file) {
                    $fileExtension = $file->extension();
                    $fileName = 'dress_' . time() . '_' . rand(1000, 9999) . '.' . $fileExtension;
                    $file->move($folderPath, $fileName);
                }
                $dress_image = new DressImage();


                $dress_image->dress_image = $fileName;
                $dress_image->dress_id = $dress_id;
                $dress_image->added_by = $user;
                $dress_image->user_id = $user_id;
                $dress_image->save();
                // Assuming image_preview is a helper function to preview images
                $images = DressImage::where('dress_id', $dress_id)->get();
                if(!empty($images))
                {
                    foreach($images as $rows)
                    {
                        // Generate the URL for the file
                        $url = asset('custom_images/dress_image/' . basename($rows->dress_image));
                        $msg .= '
                                        <div class="col-xl-4 col-lg-4 col-md-4 col-sm-6 col-xs-12">
                                            <img class="img-thumbnail mb-1" src="'.$url.'" style="max-height:60px !important;min-height:60px !important;max-width:60px;min-width:60px;">
                                            <p class="text-center">
                                                <a href="#" class="card-link e-rmv-attachment" id="'.$rows->dress_id.'">
                                                <i class="fa fa-times"></i>
                                                </a>
                                            </p>
                                        </div>';
                    }
                }
            }
        }
        else
        {
            // Check if the request contains files
            if ($request->hasFile('attachments')) {
                $files = $request->file('attachments');
                $folderPath = public_path('custom_images/temp_data');

                // Check if the folder doesn't exist, then create it
                if (!File::isDirectory($folderPath)) {
                    File::makeDirectory($folderPath, 0777, true, true);
                }

                foreach ($files as $file) {
                    $fileExtension = $file->extension();
                    $fileName = 'att_' . rand(100000, 999999) . '_' . date('His_dmY') . '.' . $fileExtension;
                    $file->move($folderPath, $fileName);
                }

                // Assuming image_preview is a helper function to preview images
                $msg = image_preview($folderPath);
            }
        }

        return response()->json(['images' => $msg]);
    }

    public function remove_attachments(Request $request)
    {
        $filePath = $request->input('img');
        $fileName = basename($filePath); // Extract the file name from the file path
        $path = public_path('custom_images/temp_data/') . $fileName; // Full path to the file

        // Check if the file exists
        if (file_exists($path)) {
            // Delete the file
            unlink($path);
            return response()->json(['success' => true]);
        }

        // Return file not found error
        return response()->json(['success' => false, 'message' => 'File not found.']);
    }

    // delete edit attachments
    public function e_remove_attachments(Request $request)
	{
        $msg="";
		$image_id = $request->input('image_id');
        $dress_id = $request->input('dress_id');
		$path = public_path('custom_images/dress_image/');
		$img = $request->input('img');
		$img = explode('/',$img);
		$img = end($img);
		if(unlink($path.$img))
		{
            $dress_image = DressImage::where('id', $image_id)->first();
            $dress_image->delete();
		}
        $images = DressImage::where('dress_id', $dress_id)->get();
        if(!empty($images))
        {
            foreach($images as $rows)
            {
                // Generate the URL for the file
                $url = asset('custom_images/dress_image/' . basename($rows->dress_image));
                $msg .= '<div class="col-xl-4 col-lg-4 col-md-4 col-sm-6 col-xs-12">
                            <img class="img-thumbnail mb-1" src="'.$url.'" style="max-height:60px !important;min-height:60px !important;max-width:60px;min-width:60px;">
                            <p class="text-center">
                                <a href="#" class="card-link e-rmv-attachment" id="'.$rows->id.'">
                                <i class="fa fa-times"></i>
                                </a>
                            </p>
                        </div>';
            }
        }
        return response()->json(['images' => $msg]);
	}



    public function maint_dress_all(){

        if (!Auth::check()) {

            return redirect()->route('login_page')->with('error', 'Please LogIn first()');
        }

        $user = Auth::user();

        if (in_array(7, explode(',', $user->permit_type))) {

            return view('maintenance.all_dress_under_maint');
        } else {

            return redirect()->route('home')->with( 'error', 'You dont have Permission');
        }
    }



    public function maint_dress(Request $request){

        $dress_id= $request->input('maint_id');
        $dress= Dress::where('id', $dress_id)->first();

        $user_id = Auth::id();
        $data= User::find( $user_id)->first();
        $user= $data->user_name;


        if (!$dress) {
            return response()->json(['error' => 'Dress not found'], 404);
        }

        $maint = new Maintgo();
        $maint->dress_id = $dress_id;
        $maint->maint_issue = $request->input('maint_name');
        $maint->issue_notes = $request->input('notes');
        $maint->start_date = $request->input('start_date');
        $maint->end_date = $request->input('end_date');
        $maint->status = 1;
        $maint->added_by = $user;
        $maint->user_id =  $user_id;
        $maint->save();


        $dress_status= Dress::where('id',   $dress_id)->first();
        $dress_status->status= 2;
        $dress_status->start_date = $request->input('start_date');
        $dress_status->end_date = $request->input('end_date');
        $dress_status->save();

        // return response()->json([
        //     'maint_item' => $maint_item,
        // ]);


    }

    public function show_maint_dress()
    {
        $sno=0;

        $view_dress= Maintgo::all();
        if(count($view_dress)>0)
        {
            foreach($view_dress as $value)
            {
               $dress= Dress::with('color', 'size', 'brand', 'category')->where('id', $value->dress_id)->first();
               $dress_name= $dress->dress_name;
               $cat= $dress->category->category_name;
               $clr= $dress->color->color_name;
               $size= $dress->size->size_name;
               $brnd= $dress->brand->brand_name ?? 'null';
               if ($value->status == 1) {
                $modal = '<a class="btn btn-outline-secondary btn-sm" data-bs-toggle="modal" data-bs-target="#maint_complete_modal" onclick="comp_maint(' . $value->id . ')" title="Maintenance">
                            <i class="fa-solid fa-gears"></i> Finish Maintenance
                          </a>';
            } else {
                $modal = '<a class="btn btn-outline-secondary btn-sm" title="Maintenance Completed">
                            <i class="fa-solid fa-gears"></i> Completed
                          </a>';
            }



                // Conditional rendering based on dress status

                $add_data=get_date_only($value->created_at);

                $sno++;
                $json[]= array(
                            $sno,
                            $dress_name .'<br>'.  $cat .'<br>'. $clr .'<br>'. $size .'<br>'. $brnd,
                            $value->maint_issue,
                            $value->issue_notes,
                            $add_data .'<br>'. $value->added_by,

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


    public function maint_dress_comp(Request $request){

        $maint_id= $request->input('maint_id');


        $maint= Maintgo::where('id',  $maint_id)->first();

        if (!$maint) {
            return response()->json(['error' => 'Data not found'], 404);
        }

        $maint->maint_cost = $request->input('maint_cost');
        $maint->maint_comp_notes = $request->input('notes');
        $maint->status = 2;
        $maint->save();

        // update dress
        $dress_status= Dress::where('id',   $maint->dress_id)->first();
        $dress_status->status= 1;
        $dress_status->start_date = NULL;
        $dress_status->end_date = NULL;
        $dress_status->save();

        return response()->json([
            'success' => 'Maintenance completed successfully',
        ]);


    }

    public function dress_profile($id){

        $dress_data= Dress::where('id', $id)->first();
        $category_data= Category::where('id', $dress_data->category_name)->first();
        $color_data= Color::where('id', $dress_data->color_name)->first();
        $size_data= Size::where('id', $dress_data->size_name)->first();

        return view ('dress.dress_profile', compact('dress_data','category_data','color_data','size_data'));


    }

    public function dress_profile_data(Request $request)
    {

        $dress = dress::find($request->dress_id);
        if (!$dress) {
            return response()->json(['message' => 'dress not found'], 404);
        }
        $bookings = Booking::with([
            'bills',
            'payments',
            'dress.brand',
            'dress.category',
            'dress.color',
            'dress.size'
        ])->where('dress_id', $dress->id)->get();

        $upcoming_bookings = Booking::with([
            'bills',
            'payments',
            'dress.brand',
            'dress.category',
            'dress.color',
            'dress.size'
        ])
        ->where('dress_id', $dress->id)
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
        ->where('dress_id', $dress->id)
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
