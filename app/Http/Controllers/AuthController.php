<?php

namespace App\Http\Controllers;

use App\Events\RegisterUser;
use App\Http\Requests\LoginFormRequest;
use App\Http\Requests\RegisterFormRequest;
use App\Http\Traits\ApiResponseTrait;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use

use Illuminate\Support\Facades\Request;class AuthController extends Controller
{
    use ApiResponseTrait;

    public function __construct(protected UserService $userService)
    {
    }

    public function login(LoginFormRequest $request): JsonResponse
    {
        if (!Auth::attempt($request->only('email', 'password'))) {

            return $this->error('Invalid login details', 401);
        }

        $user = Auth::user();
        $token = $user->createToken('auth_token')->plainTextToken;

        return $this->success('Login successfully',
            [
                'access_token' => $token,
                'token_type' => 'Bearer'
            ]);
    }

    public function register(RegisterFormRequest $request): JsonResponse
    {
        $user = $this->userService->create($request->only('name', 'email', 'password'));
        event(new RegisterUser($user));

        return $this->success('User created successfully. Please check your email for verification link',
            $user->id,
            201);
    }

    public function logout(): JsonResponse
    {
        return $this->success('Logout successfully');
    }

    public function verify(Request $request): JsonResponse
    {
        $user = $request->user();

        if ($user->hasVerifiedEmail()) {
            return $this->success('Already verified');
        }

        return response()->json(['message' => 'Email successfully verified'], 200);
    }

    public function show(Request $request): JsonResponse
    {
        return $request->user()->hasVerifiedEmail()
            ? $this->success('Email already verified')
            : $this->error('Verify your email address', 403);
    }

    public function resend(Request $request): JsonResponse
    {
        if ($request->user()->hasVerifiedEmail()) {
            return $this->success('Email already verified');
        }
        $request->user()->sendEmailVerificationNotification();

        return $this->success('Verification link resent');

    }


}
