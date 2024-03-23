<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    // index
    public function index()
    {
        $products = Product::paginate(10);
        return view('pages.products.index', compact('products'));
    }

    // create
    public function create()
    {
        $categories = DB::table('categories')->get();
        return view('pages.products.create', compact('categories'));
    }

    // store
    public function store(Request $request)
    {
        // validate the request...
        $request->validate([
            'name' => 'required',
            'description' => 'required',
            'price' => 'required|numeric',
            'category_id' => 'required',
            'stock' => 'required|numeric',
            'status' => 'required|boolean',
            'is_favorite' => 'required|boolean',

        ]);

        // store the request...
        $product = new Product;
        $product->name = $request->name;
        $product->description = $request->description;
        $product->price = $request->price;
        $product->category_id = $request->category_id;
        $product->stock = $request->stock;
        $product->status = $request->status;
        $product->is_favorite = $request->is_favorite;

        $product->save();

        //save image
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $image->storeAs('public/products', $product->id . '.' . $image->getClientOriginalExtension());
            $product->image = 'storage/products/' . $product->id . '.' . $image->getClientOriginalExtension();
            $product->save();
        }

        return redirect()->route('products.index')->with('success', 'Product created successfully');
    }

    // show
    public function show($id)
    {
        return view('pages.products.show');
    }

    // edit
    public function edit($id)
    {
        $product = Product::findOrFail($id);
        $categories = DB::table('categories')->get();
        return view('pages.products.edit', compact('product', 'categories'));
    }

    // // update
    // public function update(Request $request, $id)
    // {
    //     // validate the request...
    //     $request->validate([
    //         'name' => 'required',
    //         'description' => 'required',
    //         'price' => 'required|numeric',
    //         'category_id' => 'required',
    //         'stock' => 'required|numeric',
    //         'status' => 'required|boolean',
    //         'is_favorite' => 'required|boolean',
    //     ]);

    //     // update the request...
    //     $product = Product::find($id);
    //     $product->name = $request->name;
    //     $product->description = $request->description;
    //     $product->price = $request->price;
    //     $product->category_id = $request->category_id;
    //     $product->stock = $request->stock;
    //     $product->status = $request->status;
    //     $product->is_favorite = $request->is_favorite;
    //     $product->save();

    //     //save image
    //     if ($request->hasFile('image')) {
    //         $image = $request->file('image');
    //         $image->storeAs('public/products', $product->id . '.' . $image->getClientOriginalExtension());
    //         $product->image = 'storage/products/' . $product->id . '.' . $image->getClientOriginalExtension();
    //         $product->save();
    //     }

    //     return redirect()->route('products.index')->with('success', 'Product updated successfully');
    // }

    // update
    // public function update(Request $request, $id)
    // {
    //     // validate the request...
    //     $request->validate([
    //         'name' => 'required',
    //         'description' => 'required',
    //         'price' => 'required|numeric',
    //         'category_id' => 'required',
    //         'stock' => 'required|numeric',
    //         'status' => 'required|boolean',
    //         'is_favorite' => 'required|boolean',
    //     ]);

    //     // find the product
    //     $product = Product::find($id);

    //     // delete old image if exists
    //     if ($request->hasFile('image')) {
    //         $oldImagePath = public_path($product->image);
    //         if (file_exists($oldImagePath)) {
    //             unlink($oldImagePath);
    //         }
    //     }

    //     // update the request...
    //     $product->name = $request->name;
    //     $product->description = $request->description;
    //     $product->price = $request->price;
    //     $product->category_id = $request->category_id;
    //     $product->stock = $request->stock;
    //     $product->status = $request->status;
    //     $product->is_favorite = $request->is_favorite;

    //     // save new image
    //     if ($request->hasFile('image')) {
    //         $image = $request->file('image');
    //         $imageName = $product->id . '.' . $image->getClientOriginalExtension();
    //         $image->storeAs('public/products', $imageName);
    //         $product->image = 'storage/products/' . $imageName;
    //     }

    //     $product->save();

    //     return redirect()->route('products.index')->with('success', 'Product updated successfully');
    // }
    public function update(Request $request, $id)
    {
        // Validasi permintaan...
        $request->validate([
            'name' => 'required',
            'description' => 'required',
            'price' => 'required|numeric',
            'category_id' => 'required',
            'stock' => 'required|numeric',
            'status' => 'required|boolean',
            'is_favorite' => 'required|boolean',
        ]);

        // Temukan produk
        $product = Product::find($id);

        // Hapus gambar lama jika ada
        if ($request->hasFile('image') && $product->image) {
            $oldImagePath = public_path($product->image);
            if (file_exists($oldImagePath)) {
                unlink($oldImagePath);
            }
        }

        // Perbarui permintaan...
        $product->name = $request->name;
        $product->description = $request->description;
        $product->price = $request->price;
        $product->category_id = $request->category_id;
        $product->stock = $request->stock;
        $product->status = $request->status;
        $product->is_favorite = $request->is_favorite;

        // Simpan gambar baru
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = $id . '.' . $image->getClientOriginalExtension();
            $image->storeAs('public/products', $imageName);
            $product->image = 'storage/products/' . $imageName;
        }

        $product->save();

        return redirect()->route('products.index')->with('success', 'Produk berhasil diperbarui');
    }



    // // destroy
    // public function destroy($id)
    // {
    //     // delete the request...
    //     $product = Product::find($id);
    //     $product->delete();

    //     return redirect()->route('products.index')->with('success', 'Product deleted successfully');
    // }
    // destroy
    public function destroy($id)
    {
        // delete the request...
        $product = Product::find($id);
        $imagePath = public_path($product->image); // get the full image path
        if (file_exists($imagePath)) {
            unlink($imagePath); // delete the image file
        }
        $product->delete();

        return redirect()->route('products.index')->with('success', 'Product deleted successfully');
    }
}
