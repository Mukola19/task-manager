<?php

namespace App\Actions\Categories;

use App\Actions\BaseAction;
use App\Exceptions\UnsupportedAction;
use App\Models\Category;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Throwable;

class UpdateCategory extends BaseAction
{
    /**
     * @throws UnsupportedAction|Throwable
     */
    public function handle(Category $category, array $data): Category
    {
        $validated = $this->validate($data);

        DB::beginTransaction();
        try {
            $category->name = $validated['name'];
            $category->type = $validated['type'];
            $category->save();

            DB::commit();

            return $category;
        } catch (QueryException|\Exception $e) {
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
        ];

        return Validator::make($data, $rules, [], [
            'name' => 'Назва',
            'type' => 'Тип',
        ])->validateWithBag('update-category');
    }
}
