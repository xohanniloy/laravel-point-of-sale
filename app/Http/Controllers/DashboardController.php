<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Product;
use App\Models\Category;
use App\Models\Customer;
use Illuminate\Http\Request;

class DashboardController extends Controller {
    public function dashboardSummery( Request $request ) {
        $user_id = $request->header( 'id' );
        $products = Product::where( 'user_id', $user_id )->count();
        $categories = Category::where( 'user_id', $user_id )->count();
        $customers = Customer::where( 'user_id', $user_id )->count();
        $invoices = Invoice::where( 'user_id', $user_id )->count();
        $total = Invoice::where( 'user_id', $user_id )->sum( 'total' );
        $vat = Invoice::where( 'user_id', $user_id )->sum( 'vat' );
        $payable = Invoice::where( 'user_id', $user_id )->sum( 'payable' );
        $discount = Invoice::where( 'user_id', $user_id )->sum( 'discount' );
        $data = [
            'products' => $products,
            'categories' => $categories,
            'customers' => $customers,
            'invoices' => $invoices,
            'total' => $total,
            'vat' => $vat,
            'payable' => $payable,
            'discount' => $discount
        ];
        return $data;
    }
}
