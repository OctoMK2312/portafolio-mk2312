<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateCategoryRequest;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\StoreCategoryRequest;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class CategoryController extends Controller
{
    use AuthorizesRequests;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $this->authorize('viewAny', Category::class);

        $categories = Category::whereNull('parent_id')
            ->with('childrenRecursive')
            ->paginate(100);

        return CategoryResource::collection($categories)->additional([
            'message' => 'Lista de categorías raíz obtenida correctamente.',
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCategoryRequest $request): JsonResponse
    {
        $category = Category::create($request->validated());

        return response()->json([
            'message' => 'Categoría creada correctamente.',
            'data' => new CategoryResource($category),
        ], 201);

    }

    /**
     * Display the specified resource.
     */
    public function show(Category $category): CategoryResource
    {
        $this->authorize('view', $category);
        // Esta es la forma más limpia y minimalista. 
        return new CategoryResource($category);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCategoryRequest $request, Category $category): JsonResponse
    {
        $category->update($request->validated());
        return response()->json([
            'message' => 'Categoría actualizada correctamente.',
            'data' => new CategoryResource($category),
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $category): JsonResponse
    {
        $this->authorize('delete', $category);
        if ($category->children()->exists()) {
            return response()->json([
                'message' => 'No se puede eliminar una categoría que tiene subcategorías.',
            ], 400);
        }

        $category->delete();

        return response()->json([
            'message' => 'Categoría eliminada correctamente.',
        ], 204);
    }
}
