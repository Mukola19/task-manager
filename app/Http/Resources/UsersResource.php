<?php

namespace App\Http\Resources;

use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UsersResource extends JsonResource
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {
        $categories = $this->categories()->withCount('tasks')->get();

        $categories = $categories
            ->map(function ($category) {
                return [
                    'id' => $category->id,
                    'name' => $category->name,
                    'tasks_count' => $category->tasks_count,
                ];
            });

        return [
            'email' => $this->email,
            'categories' => $categories,
        ];
    }
}
