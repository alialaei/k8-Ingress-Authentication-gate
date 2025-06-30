<?php

namespace App\Http\Controllers\Authentication;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Actions\Auth\RegisterUser;
use Illuminate\Http\Request;

class RegisterController extends Controller
{
    public function __invoke(RegisterRequest $request, RegisterUser $action)
    {
        $user = $action->handle($request->validated());

        // Automatically issue Passport token
        $response = app()->handle(
            Request::create('/oauth/token', 'POST', [
                'grant_type' => 'password',
                'client_id' => config('passport.password_client_id'),
                'client_secret' => config('passport.password_client_secret'),
                'username' => $user->email,
                'password' => $request->password,
                'scope' => '*',
            ])
        );

        $data = json_decode($response->getContent(), true);

        return response()->json([
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ],
            'access_token' => $data['access_token'],
            'refresh_token' => $data['refresh_token'],
            'expires_in' => $data['expires_in'],
            'token_type' => $data['token_type'],
        ]);

    }
}
