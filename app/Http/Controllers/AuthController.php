<?php

namespace App\Http\Controllers;
use Validator;
use App\User;
use Firebase\JWT\JWT;
use Illuminate\Http\Request;
use Firebase\JWT\ExpiredException;
use Illuminate\Support\Facades\Hash;
use Laravel\Lumen\Routing\Controller as BaseController;
class AuthController extends BaseController
{
    /**
     * The request instance.
     *
     * @var \Illuminate\Http\Request
     */
    private $request;
    /**
     * Create a new controller instance.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return void
     */
    public function __construct(Request $request) {
        $this->request = $request;
    }
    /**
     * Create a new token.
     *
     * @param  \App\User   $user
     * @return string
     */
    protected function jwt(User $user) {
        $payload = [
            'iss' => "lumen-jwt", // Issuer of the token
            'sub' => $user->id, // Subject of the token
            'iat' => time(), // Time when JWT was issued.
            'exp' => time() + 60*60 // Expiration time
        ];

        // As you can see we are passing `JWT_SECRET_KEY` as the second parameter that will
        // be used to decode the token in the future.
        return JWT::encode($payload, env('JWT_SECRET_KEY'));
    }
    /**
     * Authenticate a user and return the token if the provided credentials are correct.
     *
     * @param  \App\User   $user
     * @return mixed
     */
    public function authenticate(User $user) {
        $this->validate($this->request, [
            'mobile'     => 'required',
            'password'  => 'required'
        ]);
//        // Find the user by email
        $user = User::where('mobile', $this->request->input('mobile'))->first();
        if (!$user) {
            // You wil probably have some sort of helpers or whatever
            // to make sure that you have the same response format for
            // differents kind of responses. But let's return the 
            // below respose for now.
            return response()->json([
                'error' => 'Mobile does not exist.'
            ], 400);
        }
        // Verify the password and generate the token
        if (Hash::check($this->request->input('password'), $user->password)) {
            return response()->json([
                'token' => $this->jwt($user)
            ], 200);
        }
        // Bad Request response
        return response()->json([
            'error' => 'Mobile or password is wrong.'
        ], 400);
    }

    public function signup() {
        $this->validate($this->request, [
            'mobile'     => 'required|unique:users,mobile|regex:/^[789]\d{9}$/i',
            'password'  => 'required',
            'category' => 'required|integer|between:1,2',
            'name' => 'required|regex:/[a-zA-Z][a-zA-Z ]*/i'
        ]);
        $user = new User();
        $user->mobile = $this->request->post('mobile');
        $user->name = $this->request->post('name');
        $user->category = $this->request->post('category');
        $user->password = Hash::make($this->request->post('password'));

        if($user->save()) {
            return response()->json([
                'message' => 'User Registered Successfully.'
            ], 200);
        } else {
            return response()->json([
                'error' => 'Error in Regestring User'
            ], 400);
        }
    }
}