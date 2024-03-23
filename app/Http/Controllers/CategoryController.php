<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use Illuminate\Support\Facades\Storage;

class CategoryController extends Controller
{
    //index
    public function index()
    {
        $categories = Category::paginate(10);
        return view('pages.categories.index', compact('categories'));
    }

    //create
    public function create()
    {
        return view('pages.categories.create');
    }

    //store
    public function store(Request $request)
    {
        //validate the request...
        $request->validate([
            'name' => 'required',
            'description' => 'required',
            'image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            // 'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ], [
            'name.required' => 'Nama harus diisi.',
            'description.required' => 'Description harus diisi',
            // 'image.required' => 'Gambar harus diisi.',
            // 'image.image' => 'File harus berupa gambar.',
            // 'image.mimes' => 'Gambar harus dalam format: jpeg, png, jpg, gif, atau svg.',
            // 'image.max' => 'Ukuran gambar tidak boleh melebihi 2MB.',
        ]);

        //store the request...
        $category = new Category;
        $category->name = $request->name;
        $category->description = $request->description;
        $category->save();

        //save image
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $image->storeAs('public/categories', $category->id . '.' . $image->getClientOriginalExtension());
            $category->image = 'storage/categories/' . $category->id . '.' . $image->getClientOriginalExtension();
            $category->save();
        }

        return redirect()->route('categories.index')->with('success', 'Category created successfully');
    }
    // store
    // public function store(Request $request)
    // {
    //     // validate the request...
    //     $request->validate([
    //         'name' => 'required',
    //         'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
    //     ]);

    //     // store the request...
    //     $category = new Category;
    //     $category->name = $request->name;
    //     $category->description = $request->description;

    //     // save image with ID as filename
    //     if ($request->hasFile('image')) {
    //         $image = $request->file('image');
    //         $imageName = time() . '_' . $category->id . '.' . $image->getClientOriginalExtension(); // Using timestamp and category ID as the filename
    //         $image->storeAs('public/categories', $imageName);
    //         $category->image = 'storage/categories/' . $imageName;
    //     }

    //     $category->save();

    //     return redirect()->route('categories.index')->with('success', 'Category created successfully');
    // }


    //show
    public function show($id)
    {
        return view('pages.categories.show');
    }

    //edit
    public function edit($id)
    {
        $category = Category::find($id);
        return view('pages.categories.edit', compact('category'));
    }

    //update
    // public function update(Request $request, $id)
    // {
    //     //validate the request...
    //     $request->validate([
    //         'name' => 'required',
    //         // 'image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
    //     ]);

    //     //update the request...
    //     $category = Category::find($id);
    //     $category->name = $request->name;
    //     $category->description = $request->description;

    //     //save image
    //     if ($request->hasFile('image')) {
    //         $image = $request->file('image');
    //         $image->storeAs('public/categories', $category->id . '.' . $image->getClientOriginalExtension());
    //         $category->image = 'storage/categories/' . $category->id . '.' . $image->getClientOriginalExtension();
    //         $category->save();
    //     }

    //     return redirect()->route('categories.index')->with('success', 'Category updated successfully');
    // }
    // update
    public function update(Request $request, $id)
    {
        // validate the request...
        $request->validate([
            'name' => 'required',
            'image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        // update the request...
        $category = Category::find($id);
        $category->name = $request->name;
        $category->description = $request->description;

        // save image and delete old image if exists
        if ($request->hasFile('image')) {
            $image = $request->file('image');

            // delete old image
            if ($category->image) {
                Storage::delete('public/categories/' . basename($category->image));
            }

            // store new image
            $imageName = $category->id . '.' . $image->getClientOriginalExtension();
            $image->storeAs('public/categories', $imageName);
            $category->image = 'storage/categories/' . $imageName;
        }

        $category->save();

        return redirect()->route('categories.index')->with('success', 'Category updated successfully');
    }


    //destroy
    // public function destroy($id)
    // {
    //     //delete the request...
    //     $category = Category::find($id);
    //     $category->delete();
    //     return redirect()->route('categories.index')->with('success', 'Category deleted successfully');
    // }
    public function destroy($id)
    {
        // delete the request...
        $category = Category::find($id);

        // Delete image file from storage
        if ($category->image) {
            Storage::delete('public/categories/' . basename($category->image));
        }

        $category->delete();

        return redirect()->route('categories.index')->with('success', 'Category deleted successfully');
    }
    // public function destroy($id)
    // {
    //     // Delete the related records first
    //     $category = Category::find($id);
    //     $category->category_id()->delete();

    //     // Delete the image file from storage
    //     if ($category->image) {
    //         Storage::delete('public/categories/' . basename($category->image));
    //     }

    //     // Then delete the category itself
    //     $category->delete();

    //     return redirect()->route('categories.index')->with('success', 'Category deleted successfully');
    // }
}
