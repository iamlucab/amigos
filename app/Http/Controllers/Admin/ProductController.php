<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Setting;

class ProductController extends Controller
{
 public function index(Request $request)
{
    $selectedCategoryId = $request->input('category_id');

    $query = Product::with('category');
    if ($selectedCategoryId) {
        $query->where('category_id', $selectedCategoryId);
    }

    return view('admin.products.index', [
        'products' => $query->get(),
        'categories' => Category::all(),
        'selectedCategoryId' => $selectedCategoryId,
    ]);
}

 public function create()
{
    $discountValues = json_decode(Setting::get('discount_values', '[]'), true);
    $promoCodes = json_decode(Setting::get('promo_codes', '[]'), true);
    $sizes = json_decode(Setting::get('available_sizes', '[]'), true);
    $colors = json_decode(Setting::get('available_colors', '[]'), true);

    $categories = Category::all(); // ✅ make sure this returns data
    $units = Unit::all();          // ✅ make sure this returns data

    return view('admin.products.create', compact(
        'discountValues',
        'promoCodes',
        'sizes',
        'colors',
        'categories',
        'units'
    ));
}


    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'cashback_amount' => 'required|numeric|min:0',
            'cashback_max_level' => 'required|integer|min:1|max:11',
            'cashback_level_bonuses' => 'nullable|array',
            'stock_quantity' => 'required|integer|min:0',
            'category_id' => 'required|exists:categories,id',
            'unit_id' => 'required|exists:units,id',
            'thumbnail' => 'nullable|image|max:2048',
            'gallery.*' => 'nullable|image|max:2048',
            'attributes' => 'nullable|string|in:S,M,L, XL, XXL, Corner, Round, Red, Blue, Green, Yellow, Black, White, Others',
            'discount_value' => 'nullable|numeric|min:0',
            'discount_type' => 'nullable|in:flat,percent',
            'promo_code' => 'nullable|string|max:50',
        ]);
        
        // Convert cashback_level_bonuses to proper format if provided
        if ($request->has('cashback_level_bonuses')) {
            $levelBonuses = [];
            foreach ($request->cashback_level_bonuses as $level => $amount) {
                if (!empty($amount)) {
                    $levelBonuses[$level] = (float) $amount;
                }
            }
            $validated['cashback_level_bonuses'] = $levelBonuses;
        }

        // Handle possible array for attributes
        if (is_array($validated['attributes'] ?? null)) {
            $validated['attributes'] = $validated['attributes'][0]; // Or implode(',', array)
        }

        // Set the created_by field to current user
        $validated['created_by'] = Auth::id();

        // Create product
        $product = new Product($validated);

        // Handle thumbnail
        if ($request->hasFile('thumbnail')) {
            $product->thumbnail = $request->file('thumbnail')->store('products/thumbnails', 'public');
        }

        // Handle gallery
        if ($request->hasFile('gallery')) {
            $galleryPaths = [];
            foreach ($request->file('gallery') as $image) {
                $galleryPaths[] = $image->store('products/gallery', 'public');
            }

            // Reorder based on gallery_order
            if ($request->filled('gallery_order')) {
                $order = json_decode($request->gallery_order, true);
                if (is_array($order)) {
                    $galleryPaths = collect($order)->map(fn($i) => $galleryPaths[$i] ?? null)->filter()->values()->all();
                }
            }

            $product->gallery = json_encode($galleryPaths);
        }

        $product->save();

        return redirect()->route('admin.products.index')->with('success', 'Product created successfully.');
    }

    public function edit(Product $product)
    {
        $categories = Category::all();
        $units = Unit::all();
        return view('admin.products.edit', compact('product', 'categories', 'units'));
    }
public function update(Request $request, Product $product)
{
    $validated = $request->validate([
        'name'                  => 'required|string|max:255',
        'description'           => 'nullable|string',
        'price'                 => 'required|numeric|min:0',
        'cashback_amount'       => 'required|numeric|min:0',
        'cashback_max_level'    => 'required|integer|min:1|max:11',
        'cashback_level_bonuses' => 'nullable|array',
        'stock_quantity'        => 'required|integer|min:0',
        'category_id'           => 'required|exists:categories,id',
        'unit_id'               => 'required|exists:units,id',
        'thumbnail'             => 'nullable|image|max:2048',
        'gallery.*'             => 'nullable|image|max:2048',
        'attributes'            => 'nullable|string|in:S,M,L,XL,XXL,Corner,Round,Red,Blue,Green,Yellow,Black,White,Others',
        'discount_value'        => 'nullable|numeric|min:0',
        'discount_type'         => 'nullable|in:flat,percent',
        'promo_code'            => 'nullable|string|max:50',
    ]);
    
    // Convert cashback_level_bonuses to proper format if provided
    if ($request->has('cashback_level_bonuses')) {
        $levelBonuses = [];
        foreach ($request->cashback_level_bonuses as $level => $amount) {
            if (!empty($amount)) {
                $levelBonuses[$level] = (float) $amount;
            }
        }
        $validated['cashback_level_bonuses'] = $levelBonuses;
    }

    // Update core fields
    $product->fill($validated);

    // Handle thumbnail upload
    if ($request->hasFile('thumbnail')) {
        $product->thumbnail = $request->file('thumbnail')->store('products/thumbnails', 'public');
    }

    // Handle gallery upload and removal
    $galleryPaths = [];
    
    // Get existing gallery paths if they exist
    $existingGallery = $product->gallery ?? [];
    if (!is_array($existingGallery)) {
        $existingGallery = [];
    }
    
    // If we're updating with new files or existing gallery data
    if ($request->hasFile('gallery') || $request->has('existing_gallery')) {
        // Add existing gallery images that weren't removed
        if ($request->has('existing_gallery')) {
            $galleryPaths = $request->existing_gallery;
        } else {
            // If no existing_gallery data, preserve all existing images
            $galleryPaths = $existingGallery;
        }
        
        // Add new gallery images if any were uploaded
        if ($request->hasFile('gallery')) {
            foreach ($request->file('gallery') as $image) {
                $galleryPaths[] = $image->store('products/gallery', 'public');
            }
        }
        
        $product->gallery = json_encode($galleryPaths);
    }

    $product->save();

    return redirect()->route('admin.products.index')->with('success', 'Product updated successfully.');
}

    public function destroy(Product $product)
    {
        $product->delete();
        return redirect()->route('admin.products.index')->with('success', 'Product deleted.');
    }
    
    /**
     * Display the specified product.
     */
    public function show(Product $product)
    {
        return view('admin.products.show', compact('product'));
    }
    
    /**
     * Preview cashback distribution based on provided parameters.
     */
    public function previewCashback(Request $request)
    {
        $cashbackAmount = $request->input('cashback_amount', 0);
        $maxLevel = $request->input('cashback_max_level', 1);
        $levelBonuses = $request->input('cashback_level_bonuses', []);
        
        // Create a temporary product for preview
        $product = new Product([
            'cashback_amount' => $cashbackAmount,
            'cashback_max_level' => $maxLevel,
            'cashback_level_bonuses' => $levelBonuses,
        ]);
        
        $cashbacks = $product->getAllCashbacks();
        
        return response()->json([
            'cashbacks' => $cashbacks,
            'total' => array_sum($cashbacks),
        ]);
    }
}
