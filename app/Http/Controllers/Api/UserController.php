<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\UserResource;
use App\Http\Resources\UsersResource;
use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Knuckles\Scribe\Attributes\Group;
use Knuckles\Scribe\Attributes\Response;
use Knuckles\Scribe\Attributes\ResponseFromApiResource;
use Knuckles\Scribe\Attributes\UrlParam;

#[Group('Users', 'APIs for users')]
class UserController
{

    #[ResponseFromApiResource(UsersResource::class, User::class, 200, collection: true)]
    #[Response(content: ['message' => 'Forbidden'], status: 403)]
    public function users()
    {
        if (Gate::denies('viewAny', User::class)) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $users = User::query()
            ->has('tasks')
            ->withCount('tasks')
            ->get();

        return UsersResource::collection($users);
    }

    #[UrlParam('id', 'int', 'The id of category.', example: 1)]
    #[ResponseFromApiResource(UserResource::class, User::class, 200)]
    #[Response(content: ['message' => 'Forbidden'], status: 403, description: 'Forbidden')]
    #[Response(content: ['message' => 'User not found'], status: 404, description: 'User not found')]
    public function user(int $userId)
    {
        $user = User::query()->findOrFail($userId);

        if (Gate::denies('view', User::class)) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        return UsersResource::make($user);
    }
}