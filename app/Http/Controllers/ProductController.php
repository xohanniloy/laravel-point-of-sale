<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ProductController extends Controller {
    public function createProduct( Request $request ) {
        $user_id = $request->header( 'id' );

        $request->validate( [
            'name'        => 'required',
            'price'       => 'required|numeric',
            'unit'        => 'required',
            'category_id' => 'required|integer|exists:categories,id',
            'image'       => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ] );

        $data = [
            'name'        => $request->input( 'name' ),
            'price'       => $request->input( 'price' ),
            'unit'        => $request->input( 'unit' ),
            'category_id' => $request->input( 'category_id' ),
            'user_id'     => $user_id,
        ];

        // Handle image upload correctly
        if ( $request->hasFile( 'image' ) ) {
            $image = $request->file( 'image' );
            $fileName = time() . '.' . $image->getClientOriginalExtension();
            $destinationPath = public_path( 'uploads' );

            // Ensure the uploads directory exists
            if ( !file_exists( $destinationPath ) ) {
                mkdir( $destinationPath, 0777, true );
            }

            // Move the file to the public/uploads folder
            $image->move( $destinationPath, $fileName );
            $data['image'] = 'uploads/' . $fileName;
        }

        // Save product
        $product = Product::create( $data );

        // return response()->json( [
        //     'status'  => 'success',
        //     'message' => 'Product created successfully',
        //     'product' => $product,
        // ] );

        return redirect()->back()->with( [
            'message' => 'Product created successfully',
            'status'  => true,
            'error'   => '',
        ] );
    }
    public function productList() {
        $user_id = request()->header( 'id' );
        $products = Product::where( 'user_id', $user_id )->get();

        return $products;
    }
    public function productById( Request $request ) {
        $user_id = $request->header( 'id' );
        $product = Product::where( 'user_id', $user_id )->where( 'id', $request->input( 'id' ) )->first();
        if ( !$product ) {
            // return response()->json( [
            //     'status'  => 'error',
            //     'message' => 'Product not found',
            // ], 404 );
            return redirect()->back()->with( [
                'message' => 'Product not found',
                'status'  => false,
                'error'   => '',
            ] );
        }
        return $product;
    }
    // public function updateProduct( Request $request ) {
    //     $user_id = $request->header( 'id' );
    //     $product = Product::where( 'user_id', $user_id )->where( 'id', $request->input( 'id' ) )->first();
    //     if ( !$product ) {
    //         // return response()->json( [
    //         //     'status'  => 'error',
    //         //     'message' => 'Product not found',
    //         // ], 404 );
    //         return redirect()->back()->with( [
    //             'message' => 'Product not found',
    //             'status'  => false,
    //             'error'   => '',
    //         ] );
    //     }
    //     $request->validate( [
    //         'name'        => 'required',
    //         'price'       => 'required|numeric',
    //         'unit'        => 'required',
    //         'category_id' => 'required|integer|exists:categories,id',
    //         'image'       => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048', // Image is now nullable
    //     ] );

    //     $data = [
    //         'name'        => $request->input( 'name' ),
    //         'price'       => $request->input( 'price' ),
    //         'unit'        => $request->input( 'unit' ),
    //         'category_id' => $request->input( 'category_id' ),
    //     ];

    //     // Handle image upload
    //     if ( $request->hasFile( 'image' ) ) {
    //         // Delete old image if exists
    //         if ( $product->image && file_exists( public_path( $product->image ) ) ) {
    //             unlink( public_path( $product->image ) );
    //         }
    //         $image = $request->file( 'image' );
    //         $fileName = time() . '.' . $image->getClientOriginalExtension();
    //         $destinationPath = public_path( 'uploads' );
    //         // Move the file to public/uploads folder
    //         $image->move( $destinationPath, $fileName );

    //         // Save new image path
    //         $data['image'] = 'uploads/' . $fileName;
    //     }

    //     $product->update( $data );

    //     // return response()->json( [
    //     //     'status'  => 'success',
    //     //     'message' => 'Product updated successfully',
    //     //     'product' => $product,
    //     // ] );

    //     // return redirect( '/product-page' )->with( [
    //     //     'message' => 'Product updated successfully',
    //     //     'status'  => true,
    //     //     'error'   => '',
    //     // ] );

    //     $data = ['message' => 'Product updated successfully', 'status' => true, 'error' => ''];
    //     return redirect( '/product-page' )->with( $data );

    // }

    public function updateProduct(Request $request)
    {
        $user_id = $request->header('id');

        $request->validate([
            'name' => 'required',
            'price' => 'required',
            'unit' => 'required',
            'category_id' => 'required'
        ]);

        $product = Product::where('user_id', $user_id)->findOrFail($request->id);

        $product->name = $request->name;
        $product->price = $request->price;
        $product->unit = $request->unit;
        $product->category_id = $request->category_id;

        if($request->hasFile('image')){
            if($product->image && file_exists(public_path($product->image))){
                unlink(public_path($product->image));
            }

            $request->validate([
                'image' => 'image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048'
            ]);
            $image = $request->file('image');

            $fileName = time().'.'.$image->getClientOriginalExtension();
            $filePath = 'uploads/'.$fileName;

            $image->move(public_path('uploads'), $fileName);
            $product->image = $filePath;
        }

        $product->save();

        // return response()->json([
        //     'status' => 'success',
        //     'message' => 'Product Updated successfully'
        // ]);
        $data = ['message'=>'Product updated successfully','status'=>true,'error'=>''];
        return redirect('/product-page')->with($data);
    }//end method

    public function deleteProduct( Request $request, $id ) {
        try {
            $user_id = $request->header( 'id' );
            $product = Product::where( 'user_id', $user_id )->where( 'id', $id )->first();
            if ( !$product ) {
                // return response()->json( [
                //     'status'  => 'error',
                //     'message' => 'Product not found',
                // ], 404 );
                return redirect()->back()->with( [
                    'message' => 'Product not found',
                    'status'  => false,
                    'error'   => '',
                ] );
            }
            if ( $product->image && file_exists( public_path( $product->image ) ) ) {
                unlink( public_path( $product->image ) );
            }
            $product->delete();
            // return response()->json( [
            //     'status'  => 'success',
            //     'message' => 'Product deleted successfully',
            // ] );
            return redirect()->back()->with( [
                'message' => 'Product deleted successfully',
                'status'  => true,
                'error'   => '',
            ] );
        } catch ( \Exception $e ) {
            // return response()->json( [
            //     'status'  => 'error',
            //     'message' => 'Product not found',
            // ], 404 );
            return redirect()->back()->with( [
                'message' => 'Product not found',
                'status'  => false,
                'error'   => $e->getMessage(),
            ] );
        }
    }

    public function productPage() {
        $user_id = request()->header( 'id' );
        $products = Product::where( 'user_id', $user_id )->with( 'category' )->latest()->get();

        return Inertia::render( 'ProductPage', [
            'products' => $products,
        ] );
    }
    public function productSavePage( Request $request ) {
        $product_id = $request->query( 'id' );
        $user_id = $request->header( 'id' );
        $product = Product::where( 'id', $product_id )->where( 'user_id', $user_id )->first();
        $categories = Category::where( 'user_id', $user_id )->get();
        return Inertia::render( 'ProductSavePage', [
            'product'    => $product,
            'categories' => $categories,
        ] );
    }
}
