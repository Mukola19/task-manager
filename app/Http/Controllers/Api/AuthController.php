<?php

namespace App\Http\Controllers\Api;

use App\Actions\Users\CreateUser;
use App\Actions\Users\DeleteUser;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Knuckles\Scribe\Attributes\Group;
use Knuckles\Scribe\Attributes\Unauthenticated;


#[Group("Auth", "APIs for authentication")]
class AuthController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login', 'register']]);
    }

    #[Unauthenticated]
    public function register(Request $request): JsonResponse
    {
        $user = app(CreateUser::class)->handle($request->input());

        $token = auth()->login($user);

        return $this->respondWithToken($token);
    }

    #[Unauthenticated]
    public function login(Request $request): JsonResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (!$token = auth()->attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return $this->respondWithToken($token);
    }

    public function me(): JsonResponse
    {
        return response()->json(auth()->user());
    }

    public function logout(): JsonResponse
    {
        auth()->logout();

        return response()->json(['message' => 'Successfully logged out']);
    }

    public function delete(): JsonResponse
    {
        if (app(DeleteUser::class)->handle(auth()->user())) {
            auth()->logout();

            return response()->json(['message' => 'Successfully deleted user']);
        }

        return response()->json(['message' => 'Unable to delete user']);
    }


    public function refresh(): JsonResponse
    {
        return $this->respondWithToken(auth()->refresh());
    }

    protected function respondWithToken($token): JsonResponse
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60
        ]);
    }
}