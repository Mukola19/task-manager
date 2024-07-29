<?php

namespace App\Actions;

use App\Exceptions\UnsupportedAction;
use Exception;
use Illuminate\Database\QueryException;

abstract class BaseAction
{
    protected array $warnings = [];

    /**
     * Обробка помилок
     */
    public function exception(Exception|QueryException $e): Exception|UnsupportedAction
    {
        if ($e instanceof QueryException) {
            if ($e->errorInfo[1] == 1062) {
                return new UnsupportedAction('Помилка при збереженні. Дублікат запису!');
            }
            if ($e->errorInfo[1] == 1451) {
                return new UnsupportedAction('Неможливо видалити дані які ще використовуються!');
            }

            report($e);
            return $e;

            return new UnsupportedAction('Помилка при роботі з базою даних!');
        }

        if (config('app.debug')) {
            return $e;
        }

        report($e);

        return new UnsupportedAction('Упс. Щось пішло не так!');
    }

    /**
     * Сповіщення які не викликають помилок, але важливо сповістити
     */
    public function getWarnings(): array
    {
        return $this->warnings;
    }

    public function hasWarnings(): bool
    {
        return ! empty($this->warnings);
    }

    protected function baseContentRules(): array
    {
       return [];
    }
}
