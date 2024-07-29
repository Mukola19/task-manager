<?php

namespace App\Actions\Tasks;

use App\Actions\BaseAction;
use App\Exceptions\UnsupportedAction;
use App\Models\Category;
use App\Models\Task;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Throwable;

class UpdateTask extends BaseAction
{
    /**
     * @throws UnsupportedAction
     * @throws ValidationException|Throwable
     */
    public function handle(Task $task, array $data): Task
    {
        $validated = $this->validate($data);

        DB::beginTransaction();
        try {

            $category = Category::query()
                ->where('user_id', $task->user_id)
                ->find($validated['category_id']);

            if (!$category) {
                throw new UnsupportedAction('Category not found');
            }

            $task->category_id = $validated['category_id'];
            $task->name = $validated['name'];
            $task->description = $validated['description'];
            $task->finished_at = $validated['finished_at'];
            $task->save();

            DB::commit();

            return $task;
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
            'category_id' => ['required', 'integer'],
            'name' => ['required', 'string'],
            'description' => ['string', 'nullable'],
            'finished_at' => ['string', 'date:Y-m-d H:i:s'],
        ];

        return Validator::make($data, $rules, [], [
            'category_id' => 'Категорія',
            'name' => 'Назва',
            'description' => 'Опис',
            'finished_at' => 'Дата виконання',
        ])->validateWithBag('update-task');
    }
}
