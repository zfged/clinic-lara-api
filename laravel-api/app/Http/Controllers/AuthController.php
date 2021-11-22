<?php

namespace App\Http\Controllers;

use JWTAuth;
use App\Models\User;
use App\Models\Command;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Exceptions\JWTException;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\Command as CommandResource;
use App\Http\Resources\User as UserResource;

class AuthController extends BaseController
{
    public function register(Request $request)
    {
    	//Validate data
        $data = $request->only('name', 'email', 'password');
        $validator = Validator::make($data, [
            'name' => 'required|string',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:6|max:50'
        ]);

        //Send failed response if request is not valid
        if ($validator->fails()) {
            return response()->json(['error' => $validator->messages()], 200);
        }

        //Request is valid, create new user
        $user = User::create([
        	'name' => $request->name,
        	'email' => $request->email,
        	'password' => bcrypt($request->password)
        ]);

        //User created, return success response
        return response()->json([
            'success' => true,
            'message' => 'User created successfully',
            'data' => $user
        ], Response::HTTP_OK);
    }
 
    public function authenticate(Request $request)
    {
        $credentials = $request->only('email', 'password');

        //valid credential
        $validator = Validator::make($credentials, [
            'email' => 'required|email',
            'password' => 'required|string|min:6|max:50'
        ]);

        //Send failed response if request is not valid
        if ($validator->fails()) {
            return response()->json(['error' => $validator->messages()], 500);
        }

        //Request is validated
        //Crean token
        try {
            if (! $token = JWTAuth::attempt($credentials)) {
                return response()->json([
                	'success' => false,
                	'message' => 'Login credentials are invalid.',
                ], 400);
            }
        } catch (JWTException $e) {
    	return $credentials;
            return response()->json([
                	'success' => false,
                	'message' => 'Could not create token.',
                ], 500);
        }

        $user = User::where('email', '=', $credentials['email'])->firstOrFail();

        return response()->json([
            'success' => true,
            'token' => $token,
            'data' => $user
        ]);
    }
 
    public function logout(Request $request)
    {
        //valid credential
        $validator = Validator::make($request->only('token'), [
            'token' => 'required'
        ]);

        //Send failed response if request is not valid
        if ($validator->fails()) {
            return response()->json(['error' => $validator->messages()], 200);
        }

		//Request is validated, do logout        
        try {
            JWTAuth::invalidate($request->token);
 
            return response()->json([
                'success' => true,
                'message' => 'User has been logged out'
            ]);
        } catch (JWTException $exception) {
            return response()->json([
                'success' => false,
                'message' => 'Sorry, user cannot be logged out'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
 
    public function get_user(Request $request)
    {
        $this->validate($request, [
            'token' => 'required'
        ]);
 
        $user = JWTAuth::authenticate('eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOlwvXC8xMjcuMC4wLjE6ODAwMFwvYXBpXC9sb2dpbiIsImlhdCI6MTYzNTQ5NjExMCwiZXhwIjoxNjM1NDk5NzEwLCJuYmYiOjE2MzU0OTYxMTAsImp0aSI6IjU1WEdTdmE2Y29jcFU2RFEiLCJzdWIiOjIsInBydiI6IjIzYmQ1Yzg5NDlmNjAwYWRiMzllNzAxYzQwMDg3MmRiN2E1OTc2ZjcifQ.ArVOmhIZ5DU4IaYUFpcjFnKdEdQ8rft5unkLechrohE');
 
        return response()->json(['user' => $user]);
    }

    public function command(Request $request){
        $commands = Command::all();
        return $this->sendResponse(CommandResource::collection($commands), 'Products retrieved successfully.');
    }

    public function user(Request $request){
        $users = User::all();
        return $this->sendResponse(UserResource::collection($users), 'Products retrieved successfully.');
    }

    public function user1(Request $request){
        return view('welcome',['name' => 'Dima']);
    }

    public function user2(Request $request){
        $users = User::all();
        return $this->sendResponse(UserResource::collection($users), 'Products retrieved successfully.');
    }

    public function user3(Request $request){
        $users = User::all();
        return $this->sendResponse(UserResource::collection($users), 'Products retrieved successfully.');
    }
}