<?php

namespace App\Http\Controllers\Api;

use App\Actions\Categories\CreateCategory;
use App\Actions\Categories\UpdateCategory;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Gate;
use Knuckles\Scribe\Attributes\BodyParam;
use Knuckles\Scribe\Attributes\Group;
use Illuminate\Http\Resources\Json\JsonResource;
use Knuckles\Scribe\Attributes\Response;
use Knuckles\Scribe\Attributes\ResponseFromApiResource;
use Knuckles\Scribe\Attributes\UrlParam;


#[Group("Categories", "APIs for categories")]
class CategoryController extends Controller
{
    #[ResponseFromApiResource(CategoryResource::class, Category::class, 200, collection: true)]
    #[Response(content: ['message' => 'Forbidden'], status: 403)]
    public function index(): AnonymousResourceCollection|JsonResponse
    {
        $user = auth()->user();
        $query = Category::query();

        if ($user->can('view-own-category')) {
            $query->where('user_id', $user->id);
        } elseif (!$user->can('view-any-category')) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        return CategoryResource::collection($query->get());
    }

    #[BodyParam('name', 'string', 'The name of category.', example: 'Top')]
    #[BodyParam('type', 'string', 'The type of category.', example: 'type')]
    #[ResponseFromApiResource(CategoryResource::class, Category::class, 201)]
    #[Response(content: ['message' => 'Forbidden'], status: 403)]
    public function store(Request $request): CategoryResource|JsonResponse
    {
        if (Gate::denies('create', Category::class)) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $category = app(CreateCategory::class)->handle(auth()->user(), $request->input());

        return CategoryResource::make($category);
    }

    #[UrlParam('id', 'int', 'The id of category.', example: 1)]
    #[ResponseFromApiResource(CategoryResource::class, Category::class, 200)]
    #[Response(content: ['message' => 'Forbidden'], status: 403, description: 'Forbidden')]
    #[Response(content: ['message' => 'Category not found'], status: 404, description: 'Category not found')]
    public function show(string $id): CategoryResource|JsonResponse
    {
        $category = Category::query()->findOrFail($id);

        if (Gate::denies('view', [Category::class, $category])) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        return CategoryResource::make($category);
    }

    #[UrlParam('id', 'int', 'The id of category.', example: 1)]
    #[BodyParam('name', 'string', 'The name of category.', example: 'Top')]
    #[BodyParam('type', 'string', 'The type of category.', example: 'type')]
    #[ResponseFromApiResource(CategoryResource::class, Category::class, 201)]
    #[Response(content: ['message' => 'Forbidden'], status: 403, description: 'Forbidden')]
    #[Response(content: ['message' => 'Category not found'], status: 404, description: 'Category not found')]
    public function update(Request $request, string $id): CategoryResource|JsonResponse
    {
        $category = Category::query()->findOrFail($id);

        if (Gate::denies('update', [Category::class, $category])) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $category = app(UpdateCategory::class)->handle($category, $request->all());

        return CategoryResource::make($category);
    }

    #[UrlParam('id', 'int', 'The id of category.', example: 1)]
    #[Response(content: ['success' => true], description: 'success')]
    #[Response(content: ['message' => 'Forbidden'], status: 403, description: 'Forbidden')]
    #[Response(content: ['message' => 'Category not found'], status: 404, description: 'category not found')]
    public function destroy(string $id): JsonResponse
    {
        $category = Category::query()->findOrFail($id);

        if (Gate::denies('delete', [Category::class, $category])) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        return response()->json([
            'success' => $category->delete(),
        ]);
    }
}
