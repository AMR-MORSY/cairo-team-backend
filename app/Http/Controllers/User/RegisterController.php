<?php

namespace App\Http\Controllers\User;

use App\Models\Users\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class RegisterController extends Controller
{
    public function register(Request $request)
    {
        $validator=Validator::make($request->all(),[
            "email"=>"required|unique:users",
            "password"=>  ['required', "regex: /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/", 'confirmed'],
            "name"=>"required|min:8"
        ]);

        if($validator->fails())
        {
            return response()->json([
                "errors"=>$validator->getMessageBag(),

            ],422);
        }
        else{
            $validated=$validator->validated();
            User::create([
                "name"=>$validated["name"],
                "email"=>$validated['email'],
                "password"=>Hash::make($validated['password'])

            ]);
            return response()->json([
                "success"=>true,
                "message"=>"registered Successfully"

            ],200);
        }

    }
}
