<?php

namespace App\Actions\Categories;


use App\Actions\BaseAction;
use App\Exceptions\UnsupportedAction;
use App\Models\Category;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Throwable;

class DeleteCategory extends BaseAction
{
    /**
     * @throws UnsupportedAction|Throwable
     */
    public function handle(Category $category): bool
    {
        DB::beginTransaction();
        try {
            if ($category->delete()) {
                DB::commit();

                return true;
            } else {
                throw new UnsupportedAction('Жодної категорії не видалено!');
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
