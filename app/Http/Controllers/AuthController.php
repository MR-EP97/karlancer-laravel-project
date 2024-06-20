<?php

namespace App\Http\Controllers;

use App\Events\RegisterUser;
use App\Http\Requests\LoginFormRequest;
use App\Http\Requests\RegisterFormRequest;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use App\Http\Traits\ApiResponseTrait;
use Illuminate\Http\Request as Request;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\Response as HttpResponse;

class AuthController extends Controller
{

    use ApiResponseTrait;

    public function __construct(protected UserService $userService)
    {
    }

    /**
     * Login User with Sanctum authentication
     *
     * @param LoginFormRequest $request
     * @return JsonResponse
     *
     *
     * @OA\Post(
     *     path="/api/login",
     *     summary="Login a user",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email", "password"},
     *             @OA\Property(property="email", type="string", example="user@example.com"),
     *             @OA\Property(property="password", type="string", example="password")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="User logged in successfully"
     *     )
     * )
     */

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

    /**
     * Register User with Sanctum authentication
     *
     * @param RegisterFormRequest $request
     * @return JsonResponse
     *
     * @OA\Post(
     *     path="/api/register",
     *     summary="Register a new user",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name", "email", "password"},
     *             @OA\Property(property="name", type="string", example="John Doe"),
     *             @OA\Property(property="email", type="string", example="john.doe@example.com"),
     *             @OA\Property(property="password", type="string", minLength=8 ,example="password123")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="User registered successfully"
     *     )
     * )
     */


    public function register(RegisterFormRequest $request): JsonResponse
    {
        $user = $this->userService->create($request->safe()->only(['name', 'email', 'password']));

        $this->notifyVerify($user);

        return $this->success('User created successfully. Please Check your email for verification link',
            ['user_id' => $user->id],
            HttpResponse::HTTP_CREATED);
    }

    /**
     * Logout user
     * @param Request $request
     * @return JsonResponse
     * @OA\Post(
     *     path="/api/logout",
     *     summary="Logout a user",
     *     security={{"sanctum":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Logout Successfully"
     *     )
     * )
     */

    public function logout(Request $request): JsonResponse
    {
        $request->user()->tokens()->delete();
        return $this->success('Logout Successfully', [], HttpResponse::HTTP_NO_CONTENT);
    }

    /**
     * Verify a user's email.
     *
     * @param $user_id
     * @param RequestVerify $request
     * @return JsonResponse
     *
     * @OA\Get(
     *     path="/api/verify/{id}",
     *     summary="Verify a user's email",
     *     @OA\Parameter(
     *         name="token",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Email verified successfully"
     *     )
     * )
     */
    public function verify($user_id, Request $request): JsonResponse
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

    /**
     * Resend verification email.
     *
     * @param User $user
     * @return JsonResponse
     *
     * @OA\Post(
     *     path="/api/resend",
     *     summary="Resend verification email",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email"},
     *             @OA\Property(property="email", type="string", example="john.doe@example.com")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Verification email resent successfully"
     *     )
     * )
     */

    public function resend(User $user): JsonResponse
    {
        if ($user->hasVerifiedEmail()) {

            return $this->error('Email Already Verified', [], HttpResponse::HTTP_BAD_REQUEST);
        }

        $this->notifyVerify($user);

        return $this->success('Email verification link sent on your email');
    }

    /**
     *Send email verify
     * @param $user
     * @return void
     *
     */

    private function notifyVerify($user): void
    {
        event(new RegisterUser($user));
    }

}
