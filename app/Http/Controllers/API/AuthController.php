<?php

namespace App\Http\Controllers\API;

use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Models\User;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validator =Validator::make($request->all(),[
            'first_name'=> 'required|max:55',
            'last_name'=> 'required|max:55',
            'email' => 'email|required|unique:users',
            'password'=> 'required',
            'date_of_birth'=>'required',
            'phone_number'=>'required',
            'gender'=>'required'
        ]);
        if($validator->fails()){
            return response()->json([ 
                'status' => 400,
                'error' => $validator->errors() 
            ]);
        }

        $firstName=$request->first_name;
        $lastName=$request->last_name;
        $dob=$request->date_of_birth;
        $email=$request->email;
        $phone=$request->phone_number;
        $gender=$request->gender;
        $hashedPassword = Hash::make($request->password);
        $user = $this->createUser($firstName,$lastName,$email,$hashedPassword,$phone,$gender, $dob);
        $accessToken = $user->createToken('authToken')->accessToken;
        return response()->json([
            'status'=>200,
            'message'=>'User created',
            'data'=>new UserResource($user),
            'token'=>$accessToken
        ]);
    }

    private function createUser($firstName,$lastName,$email,$hashedPassword,$phone,$gender,$dob){
        return User::create([
        'first_name'=>$firstName,
        'last_name'=>$lastName,
        'email'=>$email,
        'phone_number'=>$phone,
        'password'=>$hashedPassword,
        'date_of_birth'=>$dob,
        'gender'=>$gender
        ]);
    }

    public function login(Request $request)
    {
        $validator =Validator::make($request->all(),[
            'email' => 'email|required',
            'password' => 'required'
        ]);
        if($validator->fails()){
            return response()->json([ 
                'status' => 400,
                'error' => $validator->errors() 
            ]);
        }
        $loginData = $request->validate([
            'email' => 'email|required',
            'password' => 'required'
        ]);

        if (!auth()->attempt($loginData)) {
            return response()->json([
                'status'=>400,
                'message'=>'Invalid credentials supplied'
            ]);
        }

        $accessToken = auth()->user()->createToken('authToken')->accessToken;
        return response()->json([
            'status'=>200,
            'message'=>'login successful',
            'data'=>new UserResource(auth()->user()),
            'token'=>$accessToken
        ]);

    }
}
