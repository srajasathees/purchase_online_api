<?php
namespace App\Http\Controllers\API;

use App\Http\Controllers\API\BaseController as BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Validator;
use App\Models\User;

class AuthController extends BaseController
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8'
        ]);

        if($validator->fails()){
			return $this->sendError('Error validation', $validator->errors());      
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password)
        ]);
		 
		$success['token'] =  $user->createToken('auth_token')->plainTextToken;
        $success['name'] =  $user->name;
		
		return $this->sendResponse($success, 'User created successfully.');
    }

    public function login(Request $request)
    {
        if (!Auth::attempt($request->only('email', 'password')))
        {
			return $this->sendError('Unauthorised.', ['error'=>'Unauthorised']);
        }

        $user = User::where('email', $request['email'])->firstOrFail();
		
		$success['token'] =  $user->createToken('auth_token')->plainTextToken; 
        $success['name'] =  $user->name;
   
        return $this->sendResponse($success, 'User signed in');

    }

    // method for user logout and delete token
    public function logout()
    {
        auth()->user()->tokens()->delete();
		
		return $this->sendResponse([], 'User successfully logged out.');

    }
}