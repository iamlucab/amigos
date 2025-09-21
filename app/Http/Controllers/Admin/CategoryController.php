<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::latest()->get();
        return view('admin.categories.index', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:categories,name',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $data = ['name' => $request->name];

        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('categories', 'public');
            $data['image'] = $imagePath;
        }

        Category::create($data);

        return back()->with('success', 'Category added successfully.');
    }

    public function update(Request $request, Category $product_category)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:categories,name,' . $product_category->id,
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $data = ['name' => $request->name];

        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($product_category->image && \Storage::disk('public')->exists($product_category->image)) {
                \Storage::disk('public')->delete($product_category->image);
            }
            
            $imagePath = $request->file('image')->store('categories', 'public');
            $data['image'] = $imagePath;
        }

        $product_category->update($data);

        return back()->with('success', 'Category updated successfully.');
    }

    public function destroy(Category $product_category)
    {
        // Delete image if exists
        if ($product_category->image && \Storage::disk('public')->exists($product_category->image)) {
            \Storage::disk('public')->delete($product_category->image);
        }
        
        $product_category->delete();
        return back()->with('success', 'Category deleted successfully.');
    }
}
