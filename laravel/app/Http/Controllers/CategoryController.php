<?php

namespace App\Http\Controllers;


use App\Http\Requests\UpdateCategoryRequest;
use App\Http\Requests\StoreCategoryRequest;
use App\Http\Resources\CategoryResource;
use App\Http\Resources\TransactionResource;
use App\Http\Resources\VcardResource;
use App\Models\Category;
use App\Models\Vcard;
use App\Models\Transaction;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function getOne(Category $category)
    {
        return new CategoryResource($category);
    }

    public function getVcardCategories(Vcard $vcard) {
        return CategoryResource::collection($vcard->categories);
    }

    public function getTransactionCategory(Transaction $transaction) {
        return new TransactionResource($transaction->category_idDetail);
    }

    public function update(UpdateCategoryRequest $request, Category $category)
    {
        $category->update($request->validated());
        return new CategoryResource($category);
    }

    public function store(StoreCategoryRequest $request)
    {
        $newCategory = Category::create($request->validated());;
        return new CategoryResource($newCategory);
    }

    public function destroy(Category $category){
        if(Transaction::where('category_id', $category->id)->exists()){
            $category->delete();
        }else{
            $category->forceDelete();
        }
        return new CategoryResource($category);

    }
}
