<?php

namespace App\Http\Controllers;

use App\Events\RegisterUser;
use App\Http\Requests\LoginFormRequest;
use App\Http\Requests\RegisterFormRequest;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

use App\Http\Traits\ApiResponseTrait;

use Illuminate\Http\Request as RequestVerify;
use Illuminate\Support\Facades\Request;
use Symfony\Component\HttpFoundation\Response as HttpResponse;

class AuthController extends Controller
{

    use ApiResponseTrait;

    public function __construct(protected UserService $userService)
    {
    }

    public function login(LoginFormRequest $request): JsonResponse
    {
        if (!Auth::attempt($request->only('email', 'password'))) {

            return $this->error('Invalid login details', [], HttpResponse::HTTP_UNAUTHORIZED);
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
        $user = $this->userService->create($request->safe()->only(['name', 'email', 'password']));

        $this->notifyVerify($user);

        return $this->success('User created successfully. Please Check your email for verification link',
            ['user_id' =>$user->id],
            HttpResponse::HTTP_CREATED);
    }

    public function logout(User $user): JsonResponse
    {
        $user->tokens()->delete();
        return $this->success('Logout Successfully');
    }

    public function verify($user_id, RequestVerify $request): JsonResponse
    {

        if (!$request->hasValidSignature()) {
            return $this->error('Invalid or Expired url provided',
                [],
                HttpResponse::HTTP_UNAUTHORIZED);
        }

        $user = User::findOrFail($user_id);

        if (!$user->hasVerifiedEmail()) {
            $user->markEmailAsVerified();
        }
        return $this->success('Email has been verified');

    }

    public function resend(User $user): JsonResponse
    {
        if ($user->hasVerifiedEmail()) {

            return $this->error('Email Already Verified', [], HttpResponse::HTTP_BAD_REQUEST);
        }

        $this->notifyVerify($user);

        return $this->success('Email verification link sent on your email');
    }

    private function notifyVerify($user): void
    {
        event(new RegisterUser($user));
    }


}
