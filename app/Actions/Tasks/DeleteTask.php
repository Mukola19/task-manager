<?php

namespace App\Actions\Tasks;


use App\Actions\BaseAction;
use App\Exceptions\UnsupportedAction;
use App\Models\Category;
use App\Models\Task;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;

class DeleteTask extends BaseAction
{
    /**
     * @throws UnsupportedAction|\Throwable
     */
    public function handle(Task $task): bool
    {
        DB::beginTransaction();
        try {
            if ($task->delete()) {
                DB::commit();

                return true;
            } else {
                throw new UnsupportedAction('Жодного завдання не видалено!');
            }
        } catch (UnsupportedAction $unsupportedAction) {
            DB::rollBack();

            throw $unsupportedAction;
        } catch (QueryException|\Exception $e) {
            DB::rollBack();

            throw $this->exception($e);
        }
    }
}
