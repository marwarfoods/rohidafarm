<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\SubCategory;
use App\Traits\LogsActivity;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    use LogsActivity;

    /**
     * Display categories and subcategories listing.
     */
    public function index()
    {
        $categories = Category::with(['subCategories' => function($q) {
            $q->withCount('products');
        }])->withCount('products')->orderBy('name')->get();
        return view('admin.categories.index', compact('categories'));
    }

    /**
     * Create Category.
     */
    public function store(Request $request)
    {
        if ($request->filled('parent_id')) {
            $request->validate([
                'parent_id' => 'required|exists:categories,id',
                'name' => 'required|string|max:255|unique:sub_categories,name',
                'description' => 'nullable|string',
            ]);

            $sub = SubCategory::create([
                'category_id' => $request->input('parent_id'),
                'name' => $request->input('name'),
                'slug' => Str::slug($request->input('name')),
                'description' => $request->input('description'),
                'is_active' => true
            ]);

            self::logActivity('subcategory_create', "Created sub-category {$sub->name} under category ID {$sub->category_id}");

            return back()->with('success', 'Subcategory created successfully.');
        }

        $request->validate([
            'name' => 'required|string|max:255|unique:categories,name',
            'description' => 'nullable|string',
            'icon' => 'nullable|string|max:255',
            'image' => 'nullable|string|max:500',
            'banner_image' => 'nullable|string|max:500',
        ]);

        $category = Category::create([
            'name' => $request->input('name'),
            'slug' => Str::slug($request->input('name')),
            'description' => $request->input('description'),
            'icon' => $request->input('icon', 'bi-tags'),
            'image' => $request->input('image'),
            'banner_image' => $request->input('banner_image'),
            'is_active' => true
        ]);

        self::logActivity('category_create', "Created category {$category->name}");

        return back()->with('success', 'Category created successfully.');
    }

    /**
     * Create Subcategory.
     */
    public function storeSubcategory(Request $request)
    {
        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255|unique:sub_categories,name',
            'description' => 'nullable|string',
        ]);

        $sub = SubCategory::create([
            'category_id' => $request->input('category_id'),
            'name' => $request->input('name'),
            'slug' => Str::slug($request->input('name')),
            'description' => $request->input('description'),
            'is_active' => true
        ]);

        self::logActivity('subcategory_create', "Created sub-category {$sub->name} under category ID {$sub->category_id}");

        return back()->with('success', 'Subcategory created successfully.');
    }

    /**
     * Delete Category (soft-delete).
     */
    public function destroy($id)
    {
        $cat = Category::findOrFail($id);
        $name = $cat->name;
        $cat->delete();

        self::logActivity('category_delete', "Deleted category {$name}");

        return back()->with('success', 'Category deleted successfully.');
    }

    /**
     * Update Category.
     */
    public function update(Request $request, $id)
    {
        $category = Category::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255|unique:categories,name,' . $id,
            'description' => 'nullable|string',
            'icon' => 'nullable|string|max:255',
            'image' => 'nullable|string|max:500',
            'banner_image' => 'nullable|string|max:500',
        ]);

        $category->update([
            'name' => $request->input('name'),
            'slug' => Str::slug($request->input('name')),
            'description' => $request->input('description'),
            'icon' => $request->input('icon', 'bi-tags'),
            'image' => $request->input('image'),
            'banner_image' => $request->input('banner_image'),
        ]);

        self::logActivity('category_update', "Updated category {$category->name}");

        return back()->with('success', 'Category updated successfully.');
    }

    /**
     * Update Subcategory.
     */
    public function updateSubcategory(Request $request, $id)
    {
        $sub = SubCategory::findOrFail($id);

        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255|unique:sub_categories,name,' . $id,
            'description' => 'nullable|string',
        ]);

        $sub->update([
            'category_id' => $request->input('category_id'),
            'name' => $request->input('name'),
            'slug' => Str::slug($request->input('name')),
            'description' => $request->input('description'),
        ]);

        self::logActivity('subcategory_update', "Updated sub-category {$sub->name}");

        return back()->with('success', 'Subcategory updated successfully.');
    }

    /**
     * Delete Subcategory.
     */
    public function destroySubcategory($id)
    {
        $sub = SubCategory::findOrFail($id);
        $name = $sub->name;
        $sub->delete();

        self::logActivity('subcategory_delete', "Deleted sub-category {$name}");

        return back()->with('success', 'Subcategory deleted successfully.');
    }
}
