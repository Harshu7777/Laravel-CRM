<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::with('parent')->orderBy('position')->orderBy('name')->get();
        return view('categories.index', compact('categories'));
    }

    public function getData(Request $request)
    {
        $query = Category::with('parent')
                         ->orderBy('position')
                         ->orderBy('name');

        if ($search = $request->input('search.value')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('slug', 'like', "%{$search}%");
            });
        }

        $total    = Category::count();
        $filtered = (clone $query)->count();
        $start    = (int) $request->input('start', 0);
        $length   = (int) $request->input('length', 10);
        $categories = $query->skip($start)->take($length)->get();

        return response()->json([
            'draw'            => (int) $request->input('draw'),
            'recordsTotal'    => $total,
            'recordsFiltered' => $filtered,
            'data'            => $categories,
        ]);
    }

    public function show($id)
    {
        $category = Category::with('children', 'parent')->findOrFail($id);
        return view('categories.show', compact('category'));
    }

    public function create()
    {
        // ✅ FIX: Saari categories fetch karo — koi bhi parent ban sakti hai
        $parentCategories = Category::with('parent')
                                    ->orderBy('name')
                                    ->get();

        return view('categories.create', compact('parentCategories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'slug'        => 'nullable|string|unique:categories,slug|max:255',
            'description' => 'nullable|string',
            'parent_id'   => 'nullable|exists:categories,id',
            'position'    => 'nullable|integer|min:0',
            'is_active'   => 'boolean',
            'image'       => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $validated['slug']      = empty($validated['slug'])
                                    ? Str::slug($validated['name'])
                                    : $validated['slug'];
        $validated['is_active'] = $request->boolean('is_active');

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('categories', 'public');
        }

        // ✅ parent_id empty string aaye toh null set karo
        $validated['parent_id'] = $validated['parent_id'] ?: null;

        Category::create($validated);

        return redirect()->route('categories.index')
                         ->with('success', 'Category created successfully!');
    }

    public function edit(Category $category)
    {
        // ✅ FIX: Saari categories fetch karo except current category
        // (category khud apni parent nahi ban sakti)
        $parentCategories = Category::with('parent')
                                    ->where('id', '!=', $category->id)
                                    ->orderBy('name')
                                    ->get();

        return view('categories.edit', compact('category', 'parentCategories'));
    }

    public function update(Request $request, Category $category)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'slug'        => 'nullable|string|unique:categories,slug,' . $category->id . '|max:255',
            'description' => 'nullable|string',
            'parent_id'   => 'nullable|exists:categories,id',
            'position'    => 'nullable|integer|min:0',
            'is_active'   => 'boolean',
            'image'       => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $validated['slug']      = empty($validated['slug'])
                                    ? Str::slug($validated['name'])
                                    : $validated['slug'];
        $validated['is_active'] = $request->boolean('is_active');
        $validated['parent_id'] = $validated['parent_id'] ?: null;

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('categories', 'public');
        }

        $category->update($validated);

        return redirect()->route('categories.index')
                         ->with('success', 'Category updated successfully!');
    }

    public function destroy(Category $category)
    {
        // ✅ Children ko bhi handle karo — parent_id null kar do
        $category->children()->update(['parent_id' => null]);
        $category->delete();

        return response()->json([
            'success' => true,
            'message' => 'Category deleted successfully!',
        ]);
    }
}