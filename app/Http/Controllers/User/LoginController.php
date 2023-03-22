<?php

namespace App\Http\Controllers\User;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Users\User;
use App\Models\Users\UserSession;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Role;

class LoginController extends Controller
{
    // private function fill_session_table()
    // {
    //     $session_duration=config("session.lifetime");
    //     $time_now=Carbon::now();
    //     $session_end=$time_now->addSeconds($session_duration*60);
    //     // $session_end=Carbon::createFromFormat('Y-m-d H:i:s.u', $session_end);

    //     $user_session=User::find(Auth::user()->id)->session;

    //     if($user_session)
    //     {
    //         $user_session->session_start=Carbon::now();
    //         $user_session->session_end=$session_end;
    //         $user_session->save();
    //     }
    //     else{
    //         $user_session=UserSession::create([
    //             "user_id"=>Auth::user()->id,
    //             "session_start"=>Carbon::now(),
    //             "session_end"=>$session_end,
    
    //         ]);

    //     }
        
      
       

    // }
    // public function refresh_session()
    // {
    //     $this->fill_session_table();
    //     $roles=User::find(Auth::user()->id)->roles;
    //     $permissions=[];
    //     foreach($roles as $role)
    //     {
    //         $permission=Role::find($role->id)->permissions;
    //         array_push($permissions,$permission);

    //     }
    //     $this->fill_session_table();

    //     $user=User::with("session")->where("id",Auth::user()->id)->first();
      
    //     return response()->json([
    //         "user" =>$user,
    //         "roles"=>$roles,
    //         "permissions"=>$permissions,
           
            




    //     ]);
    // }
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
                $token=$request->user()->createToken($request->input("email"));
                
              

                $roles=User::find(Auth::user()->id)->roles;
                $permissions=[];
                foreach($roles as $role)
                {
                    $permission=Role::find($role->id)->permissions;
                    array_push($permissions,$permission);

                }
                // $this->fill_session_table();

                 $user=User::where("id",Auth::user()->id)->first();
                $data=[];
                $user_data=[];
                $user_data["user"]=$user;
                $user_data["roles"]=$roles;
                $user_data["permissions"]=$permissions;
                $user_data["token"]=$token;
                $data["user_data"]=$user_data;
               
                return response()->json(
                    $data
                    
        



                ,200);
            }
        }
    }
}
