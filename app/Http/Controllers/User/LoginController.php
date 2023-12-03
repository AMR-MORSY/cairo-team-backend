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
use App\Traits\Admin\AuthAndAuthorization;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Validator;
use Stevebauman\Location\Facades\Location;

class LoginController extends Controller
{
    use AuthAndAuthorization;


    public function login(Request $request)

    {
       
       return $this->Authenticate($request->all());
    }
      
}
