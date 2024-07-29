<?php

namespace App\Actions\Categories;


use App\Actions\BaseAction;
use App\Exceptions\UnsupportedAction;
use App\Models\Category;
use App\Models\User;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Throwable;

class CreateCategory extends BaseAction
{
    /**
     * @throws UnsupportedAction
     * @throws ValidationException|Throwable
     */
    public function handle(User $user, array $data): Category
    {
        $validated = $this->validate($data);

        DB::beginTransaction();
        try {

            $category = Category::query()
                ->create([
                    'user_id' => $user->getAuthIdentifier(),
                    'name' => $validated['name'],
                    'type' => $validated['type'],
                    'is_static' => $validated['is_static'] ?? false,
                ]);

            DB::commit();

            return $category;
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
            'type' => ['required', 'string'],
            'is_static' => ['boolean'],
        ];

        return Validator::make($data, $rules, [], [
            'name' => 'Назва',
            'type' => 'Тип',
            'is_static' => 'Прапор "Категорію не можна змінити"',
        ])->validateWithBag('create-category');
    }
}
