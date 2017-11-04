<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Validator;
use DB, Hash, Mail;
use Response;
use Illuminate\Support\Facades\Password;
use Illuminate\Mail\Message;
use App\ImageSave;
use Auth;
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
        $validation = [
            'first_name'=> $request->get('first_name'),
            'name'=> $request->get('name'),
            'email'=> $request->get('email'),
            'password'=> $request->get('password'),
            'password_confirmation' => $request->get('password_confirmation')
        ];
        $validator = Validator::make($validation, $rules);
        if ($validator->fails()) {
            $error = $validator->messages();
            return response()->json(['success' => false, 'error' => $error]);
        }
        $first_name = $request->get('first_name');
        $name = $request->get('name');
        $email = $request->get('email');
        $password = $request->get('password');
        $time_zone = $request->get('time_zone', null);
        $city = $request->get('city', null);
        $country = $request->get('country', null);
        DB::beginTransaction();
        $user = User::create(['first_name' => $first_name, 'name' => $name, 'email' => $email, 'city' => $city, 'country'=> $country, 'time_zone'=> $time_zone, 'password' => Hash::make($password)]);
        if($request->file('profile_image')){
            $image = $request->file('profile_image');
            $extension = $image->getClientOriginalExtension();
            if(!($extension == 'jpg' || $extension == 'png')) {
                DB::rollBack();
                return Response::json([
                    'message' => 'Only upload jpg or png images please'
                ], 422);
            }else{
                $bytes = random_bytes(10);
                $img_name = bin2hex($bytes);
                try{
                    $save = new ImageSave(400,400,'u_' . $user->id . '/profile', $img_name . '.' . $extension, $image );
                    $save_thumb = new ImageSave(90,90,'u_' . $user->id  . '/profile', $img_name . '_thumb.' . $extension, $image );
                    $profile_image = $save->saveImage();
                    $profile_thumb = $save_thumb->saveImage();
                }catch (Exception $e) {
                    DB::rollBack();
                    return Response::json([
                        'message' => 'something went wrong while trying to upload your picture try again please'
                    ], 422);
                }
            }
        }else{
            $profile_image = 'default_profile.jpg';
            $profile_thumb = 'default_profile_thumb.jpg';
        }
        $user->profile_image = $profile_image;
        $user->profile_image_thumb = $profile_thumb;
        $image_save = $user->save();
        $verification_code = str_random(30); //Generate verification code
        $verificationinsert = DB::table('user_verifications')->insert(['user_id' => $user->id, 'token' => $verification_code]);
        $subject = "Please verify your email address.";
        $mailsend = Mail::send('email.verify', ['first_name'=> $first_name,'name' => $name, 'verification_code' => $verification_code],
            function ($mail) use ($email, $name, $subject) {
                $mail->from(getenv('MAIL_USERNAME'), "Tavlr");
                $mail->to($email, $name);
                $mail->subject($subject);
            });
        if(count(Mail::failures()) > 0 ) {
            DB::rollBack();
            foreach(Mail::failures as $email_address) {
                $failure .= "$email_address,";
            }
            return response()->json(['success' => false, 'error' => $failure]);
        }
        if(!$user || !$verificationinsert || !$profile_image || !$profile_thumb){
            DB::rollBack();
            return response()->json(['success' => false, 'error' => ['error' => 'something went wrong']]);
        }
        DB::commit();
        return response()->json(['success' => true, 'message' => 'Thanks for signing up! Please check your email to complete your registration.'], 200);
    }

    public function verifyUser($verification_code)
    {
        $check = DB::table('user_verifications')->where('token', $verification_code)->first();
        if (!is_null($check)) {
            $user = User::find($check->user_id);
            if ($user->is_verified == 1) {
                return redirect('http://127.0.0.1:4200/login?message=is_verified');
            }
            $user->update(['is_verified' => 1]);
            DB::table('user_verifications')->where('token', $verification_code)->delete();
            return redirect('http://127.0.0.1:4200/login?message=succes');
        }
        return redirect('http://127.0.0.1:4200/login?message=invalid');
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
                return response()->json(['success' => false, 'error' => 'Invalid Credentials. Please make sure you entered the right information and you have verified your email address.']);
            }
        } catch (JWTException $e) {
            // something went wrong whilst attempting to encode the token
            return response()->json(['success' => false, 'error' => 'could_not_create_token'], 500);
        }
        // all good so return the token
        return response()->json(['success' => true, 'data' => ['token' => $token, 'user' => Auth::user()]]);
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
