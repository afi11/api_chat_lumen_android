<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use App\Models\User;

class UserController extends Controller
{
    public function __construct() {
        
    }

    public function register(Request $request)
    {
        // validate data
        $this->validate($request, [
            'name' => 'required|string',
            'email' => 'required|email|unique:users',
            'password' => 'required|confirmed'
        ]);   

        $input = $request->only('name','email','password');

        try {
            $user = new User;
            $user->name = $input['name'];
            $user->email = $input['email'];
            $user->photo = 'default.jpg';
            $password = $input['password'];

            $user->password = app('hash')->make($password);

            if($user->save()){
                $code = 200;
                $ouput = [
                    'user' => $user,
                    'code' => $code,
                    'message' => 'User created successfully'
                ];
            }else{
                $code = 500;
                $ouput = [
                    'code' => $code,
                    'message' => 'An error occured while creating user'
                ];
            }
        }catch (Exception $e){
            $code = 500;
            $ouput = [
                'code' => $code,
                'message' => 'An error occured while creating user'
            ];
        }

        return response()->json($ouput, $code);
    }

    public function login(Request $request)
    {
        $this->validate($request, [
            'email' => 'required|email',
            'password' => 'required'
        ]);

        $input = $request->only('email','password');
        $authorized = Auth::attempt($input);
        if(!$authorized){
            $code = 401;
            $output = [
                'code' => $code,
                'message' => 'User is not authorized'
            ];
        }else{
            $code = 200;
            $token = $this->respondWithToken($authorized);
            $output = [
                'code' => $code,
                'message' => 'User is loggin',
                'id_user' => \Auth::id(),
                'token' => $token  
            ];
        }

        return response()->json($output, $code);
    }

}