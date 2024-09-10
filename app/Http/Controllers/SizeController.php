<?php

namespace App\Http\Controllers;

use App\Models\Size;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;

class SizeController extends Controller
{
    public function index(){
        return view ('dress.size');
    }
    public function show_size()
    {
        $sno=0;

        $view_size= size::all();
        if(count($view_size)>0)
        {
            foreach($view_size as $value)
            {
                
                $modal='<a class="btn btn-outline-secondary btn-sm edit" data-bs-toggle="modal" data-bs-target="#add_size_modal" onclick=edit("'.$value->id.'") title="Edit">
                            <i class="fas fa-pencil-alt" title="Edit"></i>
                        </a>
                        <a class="btn btn-outline-secondary btn-sm edit" onclick=del("'.$value->id.'") title="Delete">
                            <i class="fas fa-trash" title="Edit"></i>
                        </a>';
                $add_data=get_date_only($value->created_at);

                $sno++;
                $json[]= array(
                            $sno,
                            $value->size_name,
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

    public function add_size(Request $request){

        // $user_id = Auth::id();
        // $data= User::find( $user_id)->first();
        // $user= $data->username;
        $user_id="";
        $user="";

        $size = new size();
         
        
        $size->size_name = $request['size_name'];
        $size->added_by = $user;
        $size->user_id = $user_id;
        $size->save();
        return response()->json(['size_id' => $size->id]);

    }

    public function edit_size(Request $request){
        $size = new size();
        $size_id = $request->input('id');

        // Use the Eloquent where method to retrieve the size by column name
        $size_data = size::where('id', $size_id)->first();
        // Add more attributes as needed
        $data = [
            'size_id' => $size_data->id,
            'size_name' => $size_data->size_name,
            // Add more attributes as needed
        ];

        return response()->json($data);
    }

    public function update_size(Request $request){

        // $user_id = Auth::id();
        // $data= User::find( $user_id)->first();
        // $user= $data->username;
        $user_id="";
        $user="";
        $size_id = $request->input('size_id');
        $size = size::where('id', $size_id)->first();
        $size->size_name = $request->input('size_name');
        $size->updated_by = $user;
        $size->save();
         
    }

    public function delete_size(Request $request){
        $size_id = $request->input('id');
        $size = size::where('id', $size_id)->first();
        $size->delete();
    }
}
