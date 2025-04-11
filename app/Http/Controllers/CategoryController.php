<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller {
    public function createCategory( Request $request ) {
        $user_id = $request->header( 'id' );
        Category::create( [
            'name'    => $request->input( 'name' ),
            'user_id' => $user_id,
        ] );
        // return response()->json( [
        //     'status'  => 'success',
        //     'message' => 'Category created successfully',
        // ] );
        return redirect()->back()->with( [
            'message' => 'Category created successfully',
            'status'  => true,
            'error'   => '',
        ] );
    }

    public function categoryList( Request $request ) {
        $user_id = $request->header( 'id' );
        $categories = Category::where( 'user_id', $user_id )->get();
        return $categories;
    }

    public function categoryById( Request $request ) {
        $user_id = $request->header( 'id' );
        $category = Category::where( 'id', $request->input( 'id' ) )->where( 'user_id', $user_id )->first();
        if ( !$category ) {
            return response()->json( [
                'status'  => 'error',
                'message' => 'Category not found',
            ], 404 );
        }
        return $category;
    }

    public function updateCategory( Request $request ) {
        $user_id = $request->header( 'id' );
        $category = Category::where( 'id', $request->input( 'id' ) )->where( 'user_id', $user_id )->first();
        if ( !$category ) {
            // return response()->json( [
            //     'status'  => 'error',
            //     'message' => 'Category not found',
            // ], 404 );
            return redirect()->back()->with( [
                'message' => 'Category not found',
                'status'  => false,
                'error'   => '',
            ]);
        }
        $category->update( [
            'name' => $request->input( 'name' ),
        ] );
        // return response()->json( [
        //     'status'  => 'success',
        //     'message' => 'Category updated successfully',
        // ] );
        return redirect()->back()->with( [
            'message' => 'Category updated successfully',
            'status'  => true,
            'error'   => '',
        ] );
    }
    public function deleteCategory( Request $request, $id ) {
        $user_id = $request->header( 'id' );
        $category = Category::where( 'id', $id )->where( 'user_id', $user_id )->first();
        if ( !$category ) {
            // return response()->json( [
            //     'status'  => 'error',
            //     'message' => 'Category not found',
            // ], 404 );
            return redirect()->back()->with( [
                'message' => 'Category not found',
                'status'  => false,
                'error'   => '',
            ] );
        }
        $category->delete();
        // return response()->json( [
        //     'status'  => 'success',
        //     'message' => 'Category deleted successfully',
        // ] );
        return redirect()->back()->with( [
            'message' => 'Category deleted successfully',
            'status'  => true,
            'error'   => '',
        ] );
    }

    //For page Route
    public function categoryPage( Request $request ) {
        $user_id = $request->header( 'id' );
        $categories = Category::where( 'user_id', $user_id )->get();
        return Inertia::render( 'CategoryPage', [
            'categories' => $categories,
        ] );
    }

    public function categorySavePage( Request $request ) {
        $category_id = $request->query( 'id' );
        $user_id = $request->header( 'id' );
        $category = Category::where( 'id', $category_id )->where( 'user_id', $user_id )->first();
        return Inertia::render( 'CategorySavePage', [
            'category' => $category,
        ] );
    }
}
