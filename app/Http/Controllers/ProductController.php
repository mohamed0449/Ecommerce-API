<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::paginate(10);
        if ($products){
            return response()->json($products, 200);
        }
        else{
            return response()->json('No products found', 404);
        }
    }

    public function show($id)
    {
        $product = Product::find($id);
        if ($product){
            return response()->json($product, 200);
        }
        else{
            return response()->json('Product not found', 404);
        }
    }

    public function store(Request $request)
    {
        Validator::make($request->all(), [
            'name' => 'required',
            'category_id' => 'required|numeric',
            'brand_id' => 'required|numeric',
            'price' => 'required|numeric',
            'amount' => 'required|numeric',
            'discount' => 'numeric',
            'image' => 'required',
        ]);

        $product = new Product();
        $product->name = $request->name;
        $product->category_id = $request->category_id;
        $product->brand_id = $request->brand_id;
        $product->price = $request->price;
        $product->amount = $request->amount;
        $product->discount = $request->discount;
        if($request->hasFile('image')){
            $path = 'assets/uploads/product/' . $product->image;
            if(File::exists($path)){
                File::delete($path);
            }
            $file = $request->file('image');
            $ext = $file->getClientOriginalExtension();
            $filename = time() . '.' . $ext;
            try{
                $file->move('assets/uploads/product', $filename);
            } catch (FileException $e) {
                dd($e);
            }
            $product->image = $filename;
        }
        $product->save();
        return response()->json('Product added', 201);
    }

    public function update(Request $request, $id)
    {
        Validator::make($request->all(), [
            'name' => 'required',
            'category_id' => 'required|numeric',
            'brand_id' => 'required|numeric',
            'price' => 'required|numeric',
            'amount' => 'required|numeric',
            'discount' => 'required|numeric',
            'image' => 'required',
        ]);

        $product = Product::find($id);
        if($product){
        $product->name = $request->name;
        $product->category_id = $request->category_id;
        $product->brand_id = $request->brand_id;
        $product->price = $request->price;
        $product->amount = $request->amount;
        $product->discount = $request->discount;

        if($request->hasFile('image')){
            $path = 'assets/uploads/product/' . $product->image;
            if(File::exists($path)){
                File::delete($path);
            }
            $file = $request->file('image');
            $ext = $file->getClientOriginalExtension();
            $filename = time() . '.' . $ext;
            try{
                $file->move('assets/uploads/product', $filename);
            } catch (FileException $e) {
                dd($e);
            }
            $product->image = $filename;
        }
        $product->save();
        return response()->json('Product updated', 200);
    }
    else{
        return response()->json('Product not found', 404);
    }
    }

    public function destroy($id)
    {
        $product = Product::find($id);
        if($product){
            $product->delete();
            return response()->json('Product deleted', 200);
        }
        else{
            return response()->json('Product not found', 404);
        }
    }
}
      

