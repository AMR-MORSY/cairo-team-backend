<?php

namespace App\Http\Controllers\Modifications;

use App\Exports\Modifications\AllModificationsExport;
use Carbon\Carbon;
use App\Models\Sites\Site;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Models\Modifications\Modification;

class ModificationsController extends Controller
{
    
    private function get_column_values($column_name)
    {

        $keys = Modification::all()->groupBy($column_name)->keys();


        return $keys;
    }

    public function analysis()
    {
        $analysis = [];
        $status = $this->get_column_values('status');
        $subcontractor = $this->get_column_values('subcontractor');
        $project = $this->get_column_values('project');
        $requester = $this->get_column_values('requester');
        $analysis["status"] = $status;
        $analysis["subcontractor"] =  $subcontractor;
        $analysis["project"] = $project;
        $analysis["requester"] = $requester;

        return response()->json([
            'status' => '200',
            'message' => 'success',
            'index' => $analysis

        ]);
    }

    public function index($colmnName, $colmnValue)
    {
        $this->authorize("viewAny",Modification::class);
        $data = [
            "columnName" => $colmnName,
            "columnValue" => $colmnValue
        ];
        $validator = Validator::make($data, [
            "columnName" => ['required', "regex:/(status|requester|subcontractor|project)/"],
            "columnValue" => ['required', 'string']
        ]);
        if ($validator->fails()) {

            return response()->json(array(
                'success' => false,
                'message' => 'There are incorect values in the form!',
                'errors' => $validator->getMessageBag()->toArray()
            ), 422);


            $this->throwValidationException(


                $validator

            );
        } else {
            $validated = $validated = $validator->validated();
            $modifications = Modification::where($validated['columnName'], $validated['columnValue'])->orderBy('request_date', "desc")->get();

            return response()->json([

                'modifications' => $modifications

            ], 200);
        }
    }

    public function modificationDetails($id)
    {
        $this->authorize("viewAny",Modification::class);
        if ($id == "null") {
            $id = null;
        }
        $data = [
            "id" => $id,
        ];
        $validator = Validator::make($data, ["id" => ['required', "exists:modifications,id"]]);
        if ($validator->fails()) {
            return response()->json(
                [
                    "errors" => $validator->getMessageBag()->toArray(),

                ],
                422
            );
            $this->throwValidationException(


                $validator

            );
        } else {
            $validated = $validator->validated();
            $details = Modification::find($validated["id"]);
            return response()->json([
                "message" => "success",
                "details" => $details,


            ], 200);
        }
    }

    public function modificationUpdate(Request $request)
    {
        $this->authorize("update",Modification::class);
        $ruls = [
            "id" => ['required', "exists:modifications,id"],
            "site_code" => "required|exists:modifications,site_code",
            "site_name" => "required|exists:modifications,site_name",
            "requester" => "required|exists:modifications,requester",
            "subcontractor" => ["required", "regex:/^OT|Alandick|Tri-Tech|Siatnile|Merc|GP|MBV|Systel|TELE-TECH|SAG|LM|HAS|MERG|H-PLUS|STEPS|GTE|AFRO|Benaya|EEC|Egypt Gate|Huawei|INTEGRA|Unilink|Red Tech|Tele-Trust|SAMA-TEL$/"],
            "request_date" => "required|date",
            "finish_date" => "nullable|date",
            "status" => ["required", "regex:/^waiting D6|done|in progress$/"],
            "requester" => ["required", "regex:/^Acquisition|Civil Team|Maintenance|Radio|Transmission|rollout|GA|Sharing team$/"],
            "project" => ["required", "regex:/^Normal Modification|LTE|Critical repair|Repair|LDN|Retrofitting|Adding sec|NTRA|Sharing|L2600|5G$/"],
            "cost" => "nullable|numeric",
            "action" => "required|string",
            "materials" => "nullable|string",
        ];
        $validator = Validator::make($request->all(), $ruls);
        if ($validator->fails()) {
            return response()->json(
                [
                    "errors" => $validator->getMessageBag()->toArray(),

                ],
                422
            );
            $this->throwValidationException(


                $validator

            );
        } else {
            $validated = $validator->validated();
            $modification = Modification::find($validated["id"]);
            $modification->requester = $validated["requester"];
            $modification->subcontractor = $validated["subcontractor"];
            $modification->status = $validated["status"];
            $modification->request_date = $this->dateFormat($validated["request_date"]);
                $modification->finish_date = $this->dateFormat($validated["finish_date"]);
            $modification->cost = $validated["cost"];
            $modification->project = $validated["project"];
            $modification->action = $validated["action"];
            $modification->materials = $validated["materials"];
            $modification->save();

            return response()->json([
                "message" => "Updated successfully ",



            ], 200);
        }
    }

    public function siteModifications($site_code)
    {
        $this->authorize("viewAny",Modification::class);
        if ($site_code == "null") {
            $site_code = null;
        }
        $data = [
            "site_code" => $site_code
        ];
        $validator = Validator::make($data, ["site_code" => ['required', "exists:sites,site_code"]]);

        if ($validator->fails()) {
            return response()->json(
                [
                    "errors" => $validator->getMessageBag()->toArray(),

                ],
                422
            );
            $this->throwValidationException(


                $validator

            );
        } else {
            $validated = $validator->validated();
            $siteCode = $validated['site_code'];

            $site = Site::where("site_code", $siteCode)->first();
            if ($site) {
                $modifications = $site->modifications;

                return response()->json([
                    "message" => "success",
                    "modifications" => $modifications,


                ], 200);
            } else {
                return response()->json([
                    "message" => "Site Not Found",

                ], 404);
            }
        }
    }

    private function dateFormat($date)
    {
        if (isset($date) && !empty($date)) {
            $newDate = Carbon::parse($date);
            return $newDate = $newDate->format("Y-m-d");
        }
        else
        {
            return null;
        }
    }

    public function newModification(Request $request)
    {
        $this->authorize("create",Modification::class);
        
        $ruls = [
            "site_code" => "required|exists:sites,site_code",
            "site_name" => "required|exists:sites,site_name",
            "subcontractor" => ["required", "regex:/^OT|Alandick|Tri-Tech|Siatnile|Merc|GP|MBV|Systel|TELE-TECH|SAG|LM|HAS|MERG|H-PLUS|STEPS|GTE|AFRO|Benaya|EEC|Egypt Gate|Huawei|INTEGRA|Unilink|Red Tech|Tele-Trust|SAMA-TEL$/"],
            "requester" => ["required", "regex:/^Acquisition|Civil Team|Maintenance|Radio|Transmission|rollout|GA|Sharing team$/"],
            "project" => ["required", "regex:/^Normal Modification|LTE|Critical repair|Repair|LDN|Retrofitting|Adding sec|NTRA|Sharing|L2600|5G$/"],
            "action" => "required|string",
            "cost" => "nullable|numeric",
            "status" => ["required", "regex:/^waiting D6|done|in progress$/"],
            "materials" => "nullable|string",
            "request_date" => "required|date",
            "finish_date" => "nullable|date",

        ];
        $validator = Validator::make($request->all(), $ruls);

        if ($validator->fails()) {
            return response()->json(
                [
                    "errors" => $validator->getMessageBag()->toArray(),

                ],
                422
            );
            $this->throwValidationException(


                $validator

            );
        } else {
            $validated = $validator->validated();
            Modification::create([
                "site_code" => $validated["site_code"],
                "site_name" => $validated["site_name"],
                "cost" => $validated["cost"],
                "status" => $validated["status"],
                "project" => $validated["project"],
                "subcontractor" => $validated["subcontractor"],
                "requester" => $validated["requester"],
                "action" => $validated["action"],
                "materials" => $validated["materials"],
                "request_date" => $this->dateFormat($validated["request_date"]),
                "finish_date" => $this->dateFormat($validated["finish_date"]),

            ]);
            return response()->json([
                "message" => "Inserted Successfully"

            ], 200);
        }
    }

    public function deleteModification(Request $request)
    {
        $this->authorize("delete",Modification::class);
        $ruls = [
            "id" => "required|exists:modifications,id",


        ];
        $validator = Validator::make($request->all(), $ruls);

        if ($validator->fails()) {
            return response()->json(
                [
                    "errors" => $validator->getMessageBag()->toArray(),

                ],
                422
            );
            $this->throwValidationException(


                $validator

            );
        } else {
            $validated = $validator->validated();
            $modification = Modification::find($validated['id']);
            $modification->delete();
            return response()->json([
                "message" => "Deleted Successfully"

            ], 200);
        }
    }

    public function download(Request $request)
    {
        $this->authorize("viewAny",Modification::class);
        $ruls = [
            "column_name" => ["required"],
            "column_value" => ["required"],

        ];
        $validator = Validator::make($request->all(), $ruls);

        if ($validator->fails()) {
            return response()->json(
                [
                    "errors" => $validator->getMessageBag()->toArray(),

                ],
                422
            );
        } else {
            $validated = $validator->validated();

            return new AllModificationsExport($validated['column_name'], $validated["column_value"]);
        }
    }
}
