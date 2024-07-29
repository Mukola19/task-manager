<?php

namespace App\Actions\Users;

use App\Actions\BaseAction;
use App\Actions\Categories\CreateCategory;
use App\Exceptions\UnsupportedAction;
use App\Models\User;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Throwable;

class CreateUser extends BaseAction
{
    /**
     * @throws UnsupportedAction
     * @throws ValidationException
     * @throws Throwable
     */
    public function handle(array $data): User
    {
        $validated = $this->validate($data);

        DB::beginTransaction();
        try {

            $user = User::query()
                ->create([
                    'name' => $validated['name'],
                    'email' => $validated['email'],
                    'password' => $validated['password'],
                ]);

            app(CreateCategory::class)->handle($user, [
                'name' => 'Термінові',
                'type' => 'category',
                'is_static' => true
            ]);

            $user->assignRole('User');

            DB::commit();

            return $user;
        } catch (QueryException|Exception $e) {
            DB::rollBack();

            throw $this->exception($e);
        }
    }

    /**
     * @throws ValidationException
     */
    public function validate(array $data): array
    {
        $rules = [
            'name' => ['required', 'string'],
            'email' => ['required', 'email', 'unique:users'],
            'password' => ['required', 'string'],
        ];

        return Validator::make($data, $rules, [], [
            'name' => 'Імʼя',
            'email' => 'Email',
            'password' => 'Password',
        ])->validateWithBag('create-user');
    }
}
