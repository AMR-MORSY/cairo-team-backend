<?php

namespace App\Http\Controllers\Batteries;

use App\Models\Sites\Site;
use Illuminate\Http\Request;
use App\Models\Batteries\Battery;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class BatteriesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    protected $rules=[
        "site_code" => ['required', "exists:sites,site_code"],
        "battery_brand" =>  ["required",'max:50', 'regex:/^[a-zA-Z0-9 \/]+$/'],
        "stock" =>  ["nullable",'max:50', 'regex:/^[a-zA-Z0-9 \/]+$/'],
        "comment"=> ["nullable",'max:250', 'regex:/^[a-zA-Z0-9 \/]+$/'],
        "category"=>["required",'regex:/^Used|New|Tested$/'],
        "battery_volt" =>  ['nullable', 'max:50', 'regex:/^[a-zA-Z0-9 \/]+$/'],
        "battery_amp_hr" =>  ['nullable', 'max:50', 'regex:/^[a-zA-Z0-9 \/]+$/'],
        "no_strings" =>  ["required",'integer', 'max:100'],
        "batteries_status" =>  ['nullable', 'max:50', 'regex:/^[a-zA-Z0-9 \/]+$/'],
        "installation_date"=>["required","date"],
        "theft_case"=>["nullable","date"],

    ];
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(),$this->rules);
        if ($validator->fails()) {
            return response()->json([
                $validator->getMessageBag(),
            ], 422);
        } else {
            $validated = $validator->validated();
            $battery=Battery::create($validated);
            return response()->json([
                "success"=>true,
                "data"=>$battery
            ],200);

        }
        
    }

    /**
     * Display the specified resource.
     */
    public function show(Site $site)
    {
        if($site)
        {
            $batteries=$site->batteries;
            if(count($batteries)>0)
            {
              return response()->json([
                "succsess"=>true,
              "data"=>  $batteries
            ],200);


            }
            return response()->json([
                "succsess"=>false,
                "message"=>"No batteries data found"
            ],200);
          
        }
       
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Battery $battery)
    {
        if($battery)
        {
            $validator = Validator::make($request->all(),$this->rules);
            if ($validator->fails()) {
                return response()->json([
                    $validator->getMessageBag(),
                ], 422);
            } else {
                $validated = $validator->validated();
                $battery=$battery->update($validated);
                return response()->json([
                    "success"=>true,
                    "data"=>$battery
                ],200);
    
            }

        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Battery $battery)
    {
        //
    }
}
