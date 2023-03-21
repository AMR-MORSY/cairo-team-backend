<?php

namespace App\Http\Controllers\User;

use App\Models\Users\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\Users\PasswordReset;
use App\Mail\ResetPasswordMailable;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class ResetPasswordController extends Controller
{
    public function sendToken(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "email" => 'required'

        ]);
        $validated = $validator->validated();
        if ($validator->fails()) {
            return response()->json([
                $validator->getMessageBag(),
            ], 422);
        }
        if ($validated) {
            $user = User::where('email', $validated['email'])->first();
        }


        if ($user) {
            $token = Str::random(32);
            Mail::to($user)->send(new ResetPasswordMailable($token));
            $password_reset = new PasswordReset();
            $password_reset->email = $user->email;
            $password_reset->token = $token;
            $password_reset->save();
        } else {
            return response()->json([
                "error" => "Email does not exist",
            ], 401);
        }
    }
    public function validateToken(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "token" => 'required'

        ]);
        $validated = $validator->validated();

        if ($validator->fails()) {
            return response()->json([
                $validator->getMessageBag(),
            ], 422);
        }
        if ($validated) {
            $password_reset = PasswordReset::where('token', $validated['token'])->first();
        }


        if (!$password_reset) {
            return response()->json([
                "error" => "invalid Token"
            ], 401);
        } else {
            $user = User::where("email", $password_reset->email)->first();
            return response()->json($user, 200);
        }
    }

    public function resetPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "password" => ['required', 'string', "regex: /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/", 'confirmed']

        ]);
        if ($validator->fails()) {
            return response()->json([
              "errors"=>  $validator->getMessageBag()->toArray(),
            ], 422);
        }
        else{
            $validated=$validator->validated();
            $user = User::find($validated["user_id"]);

            $password_reset = PasswordReset::where("email", $user->email);
            $password_reset->delete();
            $user->password = bcrypt($request->input('password'));
            $user->save();
        }

       
    }
}
