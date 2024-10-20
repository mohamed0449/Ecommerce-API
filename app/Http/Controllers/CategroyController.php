<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Exception;

class CategroyController extends Controller
{
    public function index()
    {
        $Category = Category::paginate();
        return response()->json($Category, 200);
    }

    public function show($id)
    {
        $Category = Category::find($id);
        if ($Category) {
            return response()->json($Category, 200);
        } else {
            return response()->json(['message' => 'Category not found'], 404);
        }
    }

    public function store(Request $request)
    {
        try{
            $validated = $request->validate([
                'name' => 'required|unique:brands,name',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
            ]);

            
            $Category = new Category();
            $Category->name = $request->name;
            $Category->save();
            return response()->json("Category added", 201);
        } catch (Exception $e) {
            return response()->json($e->getMessage(), 400);
        }
    }

    public function update_category(Request $request, $id)
    {
        try {
                $validated = $request->validate([
                    'name' => 'required|unique:brands,name',
                    'image' => 'required'
                ]);

                $category = Category::find($id);
                if($request->hasFile('image')){
                    $path = 'assets/uploads/category/' . $category->image;
                    if(File::exists($path)){
                        File::delete($path);
                    }
                    $file = $request->file('image');
                    $ext = $file->getClientOriginalExtension();
                    $filename = time() . '.' . $ext;
                    try{
                        $file->move('assets/uploads/category', $filename);
                    } catch (FileException $e) {
                        dd($e);
                    }
                    $category->image = $filename;
                }
                $category->name = $request->name;
                $category->update();
                return response()->json("Category updated", 200);
        } catch (Exception $e) {
            return response()->json($e->getMessage(), 400);
        }
    }

    public function delete_category($id)
    {
        $Category = Category::find($id);
        if ($Category) {
            $Category->delete();
            return response()->json("Category deleted", 200);
        } else {
            return response()->json(['message' => 'Category not found'], 404);
        }
    }
}
