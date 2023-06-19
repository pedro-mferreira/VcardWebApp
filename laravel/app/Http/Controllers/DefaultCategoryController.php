<?php

namespace App\Http\Controllers;

use App\Models\DefaultCategory;
use App\Http\Requests\UpdateStoreDefaultCategoryRequest;
use Illuminate\Http\Request;

use App\Http\Resources\DefaultCategoryResource;

class DefaultCategoryController extends Controller
{
    public function getAll()
    {
        return DefaultCategoryResource::collection(DefaultCategory::all());
    }

    public function getOne(DefaultCategory $category)
    {
        return new DefaultCategoryResource($category);
    }

    public function update(UpdateStoreDefaultCategoryRequest $request, DefaultCategory $category)
    {
        $category->update($request->validated());
        return new DefaultCategoryResource($category);
    }

    public function store(UpdateStoreDefaultCategoryRequest $request)
    {
        $newCategory = DefaultCategory::create($request->validated());;
        return new DefaultCategoryResource($newCategory);
    }

    public function destroy(DefaultCategory $category)
    {
        $category->delete();
        return new DefaultCategoryResource($category);
    }
}
