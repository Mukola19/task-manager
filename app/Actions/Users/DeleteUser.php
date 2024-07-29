<?php

namespace App\Actions\Users;

use App\Actions\BaseAction;
use App\Actions\Categories\CreateCategory;
use App\Exceptions\UnsupportedAction;
use App\Models\Task;
use App\Models\User;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Throwable;

class DeleteUser extends BaseAction
{
    /**
     * @throws UnsupportedAction|Throwable
     */
    public function handle(User $user): bool
    {
        DB::beginTransaction();
        try {

            $user->categories()->delete();
            $user->tasks()->delete();

            if ($user->delete()) {
                DB::commit();

                return true;
            } else {
                throw new UnsupportedAction('Жодного користувача не видалено!');
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
