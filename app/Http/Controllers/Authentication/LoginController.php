<?php

namespace App\Http\Controllers\Authentication;

use App\Actions\Auth\LoginUser;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;

class LoginController extends Controller
{
    public function __invoke(LoginUser $action, LoginRequest $request){
        $response = $action->handle($request->email, $request->password);
        return $response;
    }
}
