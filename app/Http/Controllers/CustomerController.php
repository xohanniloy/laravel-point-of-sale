<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller {
    public function createCustomer( Request $request ) {
     
        $user_id = $request->header( 'id' );
        $request->validate( [
            'name'  => 'required',
            'email' => 'required|email|unique:customers,email',
            'phone' => 'required',
        ] );

        $customer = Customer::create( [
            'name'    => $request->input( 'name' ),
            'email'   => $request->input( 'email' ),
            'phone'   => $request->input( 'phone' ),
            'user_id' => $user_id,
        ] );

        // return response()->json( [
        //     'status'  => 'success',
        //     'message' => 'Customer created successfully',
        //     'data'    => $customer,
        // ] );
        return redirect()->back()->with( [
            'message' => 'Customer created successfully',
            'status'  => true,
            'error'   => '',
        ]);
    }
    public function customerList( Request $request ) {
        $user_id = $request->header( 'id' );
        $customers = Customer::where( 'user_id', $user_id )->get();
        return $customers;
    }
    public function customerById( Request $request ) {
        $user_id = $request->header( 'id' );
        $customer = Customer::where( 'user_id', $user_id )->where( 'id', $request->input( 'id' ) )->first();
        if ( !$customer ) {
            // return response()->json( [
            //     'status'  => 'error',
            //     'message' => 'Customer not found',
            // ], 404 );
            return redirect()->back()->with( [
                'message' => 'Customer not found',
                'status'  => false,
                'error'   => '',
            ]);
        }
        return $customer;
    }
    public function updateCustomer( Request $request ) {
        $user_id = $request->header( 'id' );
        $customer = Customer::where( 'user_id', $user_id )->where( 'id', $request->input( 'id' ) )->first();
        if ( !$customer ) {
            return response()->json( [
                'status'  => 'error',
                'message' => 'Customer not found',
            ], 404 );
        }
        $customer->update( [
            'name'  => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
        ] );
        // return response()->json( [
        //     'status'  => 'success',
        //     'message' => 'Customer updated successfully',
        //     'data'    => $customer,
        // ] );
        return redirect()->back()->with( [
            'message' => 'Customer updated successfully',
            'status'  => true,
            'error'   => '',
        ]);
    }
    public function deleteCustomer( Request $request, $id ) {
        $user_id = $request->header( 'id' );
        $customer = Customer::where( 'user_id', $user_id )->where( 'id', $id )->first();
        if ( !$customer ) {
            return response()->json( [
                'status'  => 'error',
                'message' => 'Customer not found',
            ], 404 );
        }
        $customer->delete();
        // return response()->json( [
        //     'status'  => 'success',
        //     'message' => 'Customer deleted successfully',
        // ] );
        return redirect()->back()->with( [
            'message' => 'Customer deleted successfully',
            'status'  => true,
            'error'   => '',
        ]);
    }
    public function customerPage(Request $request) {
        $user_id = $request->header( 'id' );
        $customers = Customer::where( 'user_id', $user_id )->get();
        return Inertia::render( 'CustomerPage', [
            'customers' => $customers,
        ] );
    }
    public function customerSavePage(Request $request) {
        $customer_id = $request->query( 'id' );
        $user_id = $request->header( 'id' );
        $customer = Customer::where( 'id', $customer_id )->where( 'user_id', $user_id )->first();
        return Inertia::render( 'CustomerSavePage', [
            'customer' => $customer,
        ] );
    }
}
