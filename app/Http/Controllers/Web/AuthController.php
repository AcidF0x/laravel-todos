<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserRegisterRequest;
use App\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    /**
     * @return Response
     */
    public function showLoginPage()
    {
        return response()->view('login');
    }

    /**
     * @param  Request  $request
     * @return RedirectResponse
     */
    public function login(Request $request)
    {
        $credentials = $request->only(['name', 'password']);

        if (Auth::attempt($credentials)) {
            return response()->redirectToRoute('web-view-todo-page');
        }

        return response()
            ->redirectToRoute('web-view-login-page')
            ->withErrors([
                'login' => __('auth.failed')
            ]);
    }

    /**
     * @return Response
     */
    public function showRegisterPage()
    {
        return response()->view('register');
    }

    /**
     * @param  UserRegisterRequest  $request
     * @return RedirectResponse
     */
    public function register(UserRegisterRequest $request)
    {
        $user = User::create([
            'name' => $request->get('name'),
            'password' => Hash::make($request->get('password')),
            'api_token' => Str::random(80)
        ]);

        Auth::login($user);

        return response()->redirectToRoute('web-view-todo-page');
    }

    /**
     * @return RedirectResponse
     */
    public function logout()
    {
        Auth::logout();
        return response()->redirectToRoute('web-view-login-page');
    }
}
