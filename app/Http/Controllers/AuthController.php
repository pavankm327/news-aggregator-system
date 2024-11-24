<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\JsonResponse;

use App\Http\Controllers\BaseController as BaseController;


use Validator;

class AuthController extends BaseController
{
    /**
     * Register a new user
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function register(Request $request): JsonResponse 
    {
        $validator = Validator::make($request->all(), [
            'name' =>'required|string|max:255',
            'email' =>'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);
        
        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors(), 422);       
        }

        $input = $request->all();        
        $user = User::create([
            'name' => $input['name'],
            'email' => $input['email'],
            'password' => bcrypt($input['password']),
        ]);
    
        $success['name'] =  $user->name;
   
        return $this->sendResponse($success, 'User registered successfully..!');
    }

    /**
     * Login a user and generate an access token
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function login(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email' =>'required',
            'password' => 'required',
        ]);

        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors(), 422);       
        }
    
        if (!auth()->attempt($request->only('email', 'password'))) {
            return $this->sendError('Invalid credentials..!', null, 401);
        }

        $user = Auth::user(); 
        $success['access_token'] =  $user->createToken('auth_token')->plainTextToken; 
        $success['token_type'] =  'Bearer';
        $success['name'] =  $user->name;

        return $this->sendResponse($success, 'Logged in successfully..!');
    }

    /**
     * Logout the user by deleting the current access token
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function logout(Request $request): JsonResponse
    {
        auth()->user()->tokens->each(function($token) {
            $token->delete();
        });
        
        return $this->sendResponse(null, 'Logged out successfully..!');
    }

    /**
     * Send a password reset link to the user's email
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function sendResetLinkEmail(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email' =>'required',
        ]);
        
        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors(), 422);       
        }

        // Send the reset link
        $response = Password::broker('users')->sendResetLink(
            $request->only('email')
        );
        
        if ($response == Password::RESET_LINK_SENT) {
            return $this->sendResponse(null, 'Password reset link sent successfully..!');
        }

        return $this->sendError('Failed to send password reset link..!', null);
    }

    /**
     * Reset the user's password using the provided token
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function resetPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'token' => 'required',
            'password' => 'required|confirmed|min:8',
        ]);
        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors(), 422);       
        }

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill(['password' => bcrypt($password)])->save();
            }
        );

        if($status == Password::PASSWORD_RESET) {
            return $this->sendResponse(null, trans($status)); 
        }

        return $this->sendError(trans($status), 400);
    }
}
