<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Validator;
use DB, Hash, Mail;
use Illuminate\Support\Facades\Password;
use Illuminate\Mail\Message;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $rules = [
            'first_name' => 'required|max:255',
            'name' => 'required|max:255',
            'email' => 'required|email|max:255|unique:users',
            'password' => 'required|confirmed|min:6',
        ];
        $input = $request->only(
            'first_name',
            'name',
            'email',
            'password',
            'password_confirmation'
        );
        $validator = Validator::make($input, $rules);
        if ($validator->fails()) {
            $error = $validator->messages()->toJson();
            return response()->json(['success' => false, 'error' => $error]);
        }
        $first_name = $request->first_name;
        $name = $request->name;
        $email = $request->email;
        $password = $request->password;
        $user = User::create(['first_name' => $first_name, 'name' => $name, 'email' => $email, 'password' => Hash::make($password)]);
        $verification_code = str_random(30); //Generate verification code
        DB::table('user_verifications')->insert(['user_id' => $user->id, 'token' => $verification_code]);
        $subject = "Please verify your email address.";
        Mail::send('email.verify', ['first_name'=> $first_name,'name' => $name, 'verification_code' => $verification_code],
            function ($mail) use ($email, $name, $subject) {
                $mail->from(getenv('MAIL_USERNAME'), "Tavlr");
                $mail->to($email, $name);
                $mail->subject($subject);
            });
        return response()->json(['success' => true, 'message' => 'Thanks for signing up! Please check your email to complete your registration.']);
    }

    public function verifyUser($verification_code)
    {
        $check = DB::table('user_verifications')->where('token', $verification_code)->first();
        if (!is_null($check)) {
            $user = User::find($check->user_id);
            if ($user->is_verified == 1) {
                return response()->json([
                    'success' => true,
                    'message' => 'Account already verified..'
                ]);
            }
            $user->update(['is_verified' => 1]);
            DB::table('user_verifications')->where('token', $verification_code)->delete();
            return response()->json([
                'success' => true,
                'message' => 'You have successfully verified your email address.'
            ]);
        }
        return response()->json(['success' => false, 'error' => "Verification code is invalid."]);
    }

    public function login(Request $request)
    {
        $rules = [
            'email' => 'required|email',
            'password' => 'required',
        ];
        $input = $request->only('email', 'password');
        $validator = Validator::make($input, $rules);
        if ($validator->fails()) {
            $error = $validator->messages()->toJson();
            return response()->json(['success' => false, 'error' => $error]);
        }
        $credentials = [
            'email' => $request->email,
            'password' => $request->password,
            'is_verified' => 1
        ];
        try {
            // attempt to verify the credentials and create a token for the user
            if (!$token = JWTAuth::attempt($credentials)) {
                return response()->json(['success' => false, 'error' => 'Invalid Credentials. Please make sure you entered the right information and you have verified your email address.'], 401);
            }
        } catch (JWTException $e) {
            // something went wrong whilst attempting to encode the token
            return response()->json(['success' => false, 'error' => 'could_not_create_token'], 500);
        }
        // all good so return the token
        return response()->json(['success' => true, 'data' => ['token' => $token]]);
    }

    public function logout(Request $request)
    {
        try {
            JWTAuth::invalidate($request->input('token'));
            return response()->json(['success' => true]);
        } catch (JWTException $e) {
            // something went wrong whilst attempting to encode the token
            return response()->json(['success' => false, 'error' => 'Failed to logout, please try again.'], 500);
        }
    }

    public function recover(Request $request)
    {
        $user = User::where('email', $request->email)->first();
        if (!$user) {
            $error_message = "Your email address was not found.";
            return response()->json(['success' => false, 'error' => ['email' => $error_message]], 401);
        }
        try {
            Password::sendResetLink($request->only('email'), function (Message $message) {
                $message->subject('Your Password Reset Link');
            });
        } catch (\Exception $e) {
            //Return with error
            $error_message = $e->getMessage();
            return response()->json(['success' => false, 'error' => $error_message], 401);
        }
        return response()->json([
            'success' => true, 'data' => ['msg' => 'A reset email has been sent! Please check your email.']
        ]);
    }
}
