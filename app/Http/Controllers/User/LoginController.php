<?php

namespace App\Http\Controllers\User;

use Carbon\Carbon;
use App\Models\Users\User;
use Jenssegers\Agent\Agent;
use Illuminate\Http\Request;
use App\Models\Users\AccessToken;
use App\Models\Users\UserSession;
use Spatie\Permission\Models\Role;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Validator;
use Stevebauman\Location\Facades\Location;

class LoginController extends Controller
{


    public function login(Request $request)

    {
        $validator = Validator::make($request->all(), [
            "email" => "required|email",
            "password" => ['required', "regex: /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/"]

        ]);
        if ($validator->fails()) {
            return response()->json([
                "errors" => $validator->getMessageBag()
            ], 422);
        } else {
            $validated = $validator->validated();
            $user = User::where("email", $validated["email"])->first();
            if ($user) {
                if ($user->email_verified_at != null) {
                    if (!Auth::attempt(['email' => $request->input("email"), 'password' =>  $request->input("password")])) {
                        return response()->json([
                            "message" => "invalid Credentials",

                        ], 422);
                    } else {
                        $token = $request->user()->createToken($request->input("email"));
                        $user = User::where("id", Auth::user()->id)->first();
                        $user_data["user"] = $user;
                        $user_data["token"] = $token;
                        return response()->json(
                            ["message" => "User loged in successfully","user_data"=> $user_data],

                            200
                        );
                    }
                } else {
                    return response()->json([
                        "message" => "Account is not verified yet"
                    ], 200);
                }
            } else {

                return response()->json([
                    "message" => "This email address does not exist"
                ], 200);
            }
        }
    }
}
