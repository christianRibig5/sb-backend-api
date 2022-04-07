<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    
    public function getAllUsers()
    {
        $users = User::all();
        return response()->json([
            'status'=>200,
            'massage'=>'Retrieved success',
            'data'=>UserResource::collection($users)
        ]);
    }


    public function getUserByID(Request $request)
    {
        $user = User::find($request->id);
        return response()->json([
            'status'=>200,
            'message'=>'Retrieved Success',
            'data'=>new UserResource($user)
        ]);
    }

   
    public function updateUserByID(Request $request)
    {
        $user = User::findOrFail($request->id);
        $user->first_name=$request->first_name;
        $user->last_name=$request->last_name;
        $user->date_of_birth=$request->date_of_birth;
        $user->gender=$request->gender;
        $user->save();
        
        return response()->json([
            'status'=>200,
            'message'=>'Updated successfully',
            'data'=>new UserResource($user)
        ]);
    }

   
    public function updateAvatarByID(Request $request)
    {
        $validator =Validator::make($request->all(),[
            'avatar_path'=> 'image|mimes:jpg,png',
            
        ]);
        if($validator->fails()){
            return response()->json([ 
                'status' => 400,
                'error' => $validator->errors() 
            ]);
        }
        $image = $request->file('avatar_path');
        $renamedImage = time().'.'.$image->getClientOriginalExtension();
        $destination = public_path('/images');
        $image->move($destination,$renamedImage);

        $user = User::findOrFail($request->id);
        $user->avatar_path = $renamedImage;
        $user->save();
        return response()->json([
            'status'=>200,
            'message'=>'Profile image uploaded successfully',
            'data'=>new UserResource($user)
        ]);
        
    }
}
