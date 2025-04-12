<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Invoice;
use App\Models\InvoiceProduct;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class InvoiceController extends Controller {
    public function createInvoice( Request $request ) {
        DB::beginTransaction();
        try {
            $user_id = $request->header( 'id' );
            $data = [
                'user_id'     => $user_id,
                'customer_id' => $request->input( 'customer_id' ),
                'total'       => $request->input( 'total' ),
                'discount'    => $request->input( 'discount' ),
                'vat'         => $request->input( 'vat' ),
                'payable'     => $request->input( 'payable' ),
            ];
            $invoice = Invoice::create( $data );
            $products = $request->input( 'products' );
            foreach ( $products as $product ) {
                $existingProduct = Product::where( 'id', $product['id'] )->first();
                if ( !$existingProduct ) {
                    // return response()->json( [
                    //     'status'  => "Failed",
                    //     'message' => "Product with ID {$product['id']} not found",
                    // ] );
                    return redirect()->route( '/invoice.page' )->with( [
                        'message' => "Product with ID {$product['id']} not found",
                        'status'  => false,
                        'error'   => '',
                    ] );
                }
                if ( $existingProduct->unit < $product['unit'] ) {
                    // return response()->json( [
                    //     'status'  => "Failed",
                    //     'message' => "Only {$existingProduct->unit} units are available in stock for product with ID {$product['id']}, which is not enough to fulfill your request.",
                    // ] );
                    return redirect()->route( '/invoice.page' )->with( [
                        'message' => "Only {$existingProduct->unit} units are available in stock for product with ID {$product['id']}, which is not enough to fulfill your request.",
                        'status'  => false,
                        'error'   => '',
                    ] );
                }
                InvoiceProduct::create( [
                    'user_id'    => $user_id,
                    'product_id' => $product['id'],
                    'invoice_id' => $invoice->id,
                    'qty'        => $product['unit'],
                    'sale_price' => $product['price'],
                ] );
                Product::where( 'id', $product['id'] )->update( [
                    'unit' => $existingProduct->unit - $product['unit'],
                ] );
            }
            DB::commit();
            // return response()->json( [
            //     'status'  => "Success",
            //     'message' => "Invoice created successfully",
            // ] );
            return redirect()->route( 'invoice.page' )->with( [
                'message' => "Invoice created successfully",
                'status'  => true,
                'error'   => '',
            ] );
        } catch ( Exception $e ) {
            DB::rollBack();
            // return response()->json( [
            //     'status'  => "Failed",
            //     'message' => "Failed to create invoice: " . $e->getMessage(),
            // ] );
            return redirect()->route( 'invoice.page' )->with( [
                'message' => "Failed to create invoice: " . $e->getMessage(),
                'status'  => false,
                'error'   => '',
            ] );
        }
    } //End Method

    public function invoiceList( Request $request ) {
        $user_id = $request->header( 'id' );
        $invoices = Invoice::with( 'customer' )->where( 'user_id', $user_id )->get();
        return $invoices;
    } //End Method

    public function invoiceDetails( Request $request ) {
        $user_id = $request->header( 'id' );
        $customerDetails = Customer::where( 'user_id', $user_id )->where( 'id', $request->customer_id )->first();
        $invoiceDetails = Invoice::where( 'user_id', $user_id )->where( 'id', $request->invoice_id )->first();
        $invoiceProducts = InvoiceProduct::where( 'user_id', $user_id )->where( 'invoice_id', $request->invoice_id )->with( 'product' )->get();

        return response()->json( [
            'customerDetails' => $customerDetails,
            'invoiceDetails'  => $invoiceDetails,
            'invoiceProducts' => $invoiceProducts,
        ] );
    }
    public function deleteInvoice( Request $request, $id ) {
        DB::beginTransaction();
        try {
            $user_id = $request->header( 'id' );
            $invoiceProduct = InvoiceProduct::where( 'invoice_id', $id )->where( 'user_id', $user_id )->first();
            if ( !$invoiceProduct ) {
                // return response()->json( [
                //     'status'  => "Failed",
                //     'message' => "Invoice not found",
                // ] );
                return redirect()->route( 'invoice.page' )->with( [
                    'message' => "Invoice not found",
                    'status'  => false,
                    'error'   => '',
                ] );
            }
            InvoiceProduct::where( 'invoice_id', $id )->where( 'user_id', $user_id )->delete();
            Invoice::where( 'id', $id )->where( 'user_id', $user_id )->delete();

            DB::commit();
            // return response()->json( [
            //     'status'  => "Success",
            //     'message' => "Invoice deleted successfully",
            // ] );
            return redirect()->route( 'invoice.page' )->with( [
                'message' => "Invoice deleted successfully",
                'status'  => true,
                'error'   => '',
            ] );
        } catch ( Exception $e ) {
            DB::rollBack();
            // return response()->json( [
            //     'status'  => "Failed",
            //     'message' => "Failed to delete invoice: " . $e->getMessage(),
            // ] );
            return redirect()->route( 'invoice.page' )->with( [
                'message' => "Failed to delete invoice: " . $e->getMessage(),
                'status'  => false,
                'error'   => '',
            ] );
        }
    }

    public function invoicePage( Request $request ) {
        $user_id = $request->header( 'id' );
        $invoices = Invoice::with( 'customer', 'invoice_products.product' )
            ->where( 'user_id', $user_id )
            ->get();
        // return $invoices;
        return Inertia::render( 'InvoicePage', [
            'invoices' => $invoices,
        ] );
    }

}
