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
            if (!Auth::attempt(['email' => $request->input("email"), 'password' =>  $request->input("password")])) {
                return response()->json([
                    "message" => "invalid Credentials",

                ], 401);
            } else {
                $token = $request->user()->createToken($request->input("email"));
                $roles = User::find(Auth::user()->id)->roles;
              
                $permissions =  User::find(Auth::user()->id)->permissions;
                // foreach ($roles as $role) {
                //     $permission = Role::find($role->id)->permissions;
                //     array_push($permissions, $permission);
                // }
           
                $user = User::where("id", Auth::user()->id)->first();
              
                $data = [];
                $user_data = [];
                $user_data["user"] = $user;
                $user_data["roles"] = $roles;
                $user_data["permissions"] = $permissions;
                $user_data["token"] = $token;
                $data["user_data"] = $user_data;
              

                return response()->json(
                    $data,
                    200
                );
            }
        }
    }
}
