<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;

class BrandController extends Controller
{
    public function index(){
        return view ('dress.brand');
    }
    public function show_brand()
    {
        $sno=0;

        $view_brand= brand::all();
        if(count($view_brand)>0)
        {
            foreach($view_brand as $value)
            {

                $modal='<a class="btn btn-outline-secondary btn-sm edit" data-bs-toggle="modal" data-bs-target="#add_brand_modal" onclick=edit("'.$value->id.'") title="Edit">
                            <i class="fas fa-pencil-alt" title="Edit"></i>
                        </a>
                        <a class="btn btn-outline-secondary btn-sm edit" onclick=del("'.$value->id.'") title="Delete">
                            <i class="fas fa-trash" title="Edit"></i>
                        </a>';
                $add_data=get_date_only($value->created_at);

                $sno++;
                $json[]= array(
                            $sno,
                            $value->brand_name,
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

    public function add_brand(Request $request){

        $user_id = Auth::id();
        $data= User::find( $user_id)->first();
        $user= $data->user_name;


        $brand = new brand();
        $brand->brand_name = $request['brand_name'];
        $brand->added_by = $user;
        $brand->user_id = $user_id;
        $brand->save();
        return response()->json(['brand_id' => $brand->id]);

    }

    public function edit_brand(Request $request){
        $brand = new brand();
        $brand_id = $request->input('id');

        // Use the Eloquent where method to retrieve the brand by column name
        $brand_data = brand::where('id', $brand_id)->first();
        // Add more attributes as needed
        $data = [
            'brand_id' => $brand_data->id,
            'brand_name' => $brand_data->brand_name,
            // Add more attributes as needed
        ];

        return response()->json($data);
    }

    public function update_brand(Request $request){

        $user_id = Auth::id();
        $data= User::find( $user_id)->first();
        $user= $data->user_name;
        $brand_id = $request->input('brand_id');
        $brand = brand::where('id', $brand_id)->first();
        $brand->brand_name = $request->input('brand_name');
        $brand->updated_by = $user;
        $brand->save();

    }

    public function delete_brand(Request $request){
        $brand_id = $request->input('id');
        $brand = brand::where('id', $brand_id)->first();
        $brand->delete();
    }
}
