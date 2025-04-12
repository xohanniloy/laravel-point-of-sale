<?php

namespace App\Http\Controllers;

use App\Mail\OTPMail;
use App\Models\Category;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\Product;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Response;
use Inertia\Inertia;

class UserController extends Controller {
    public function userRegistration( Request $request ) {
        try {
            $request->validate( [
                'name'     => 'required',
                'email'    => 'required|email|unique:users,email',
                'password' => 'required',
                'phone'    => 'required',
            ] );
            $user = User::create( [
                'name'     => $request->input( 'name' ),
                'email'    => $request->input( 'email' ),
                'password' => $request->input( 'password' ),
                'phone'    => $request->input( 'phone' ),
            ] );
            // return response()->json( [
            //     'status'  => 'success',
            //     'message' => 'User created successfully',
            //     'data'    => $user,
            // ] );
            return redirect( '/login' )->with( [
                'message' => 'User created successfully',
                'status'  => true,
                'error'   => '',
            ] );
        } catch ( Exception $e ) {
            return redirect( '/register' )->with( [
                'message' => 'User not created',
                'status'  => false,
                'error'   => '',
            ] );
        }
    } //End Method
    public function userLogin( Request $request ) {

        // Validate request
        $request->validate( [
            'email'    => 'required|email',
            'password' => 'required',
        ] );

        // Find user by email
        $user = User::where( 'email', $request->input( 'email' ) )->first();

        // if ( !$user ) {
        //     return response()->json( [
        //         'status'  => 'error',
        //         'message' => 'User not found',
        //     ], 404 );
        // }

        // if ( !Hash::check( $request->input( 'password' ), $user->password ) ) {
        //     return response()->json( [
        //         'status'  => 'error',
        //         'message' => 'Incorrect password',
        //     ], 401 );
        // }

        // // Generate JWT token
        // $token = JWTToken::createToken( $user->email, $user->id );

        // // Return response with cookie
        // return response()->json( [
        //     'status'  => 'success',
        //     'message' => 'User logged in successfully',
        //     'token'   => $token,
        // ], 200 )->cookie( 'token', $token, 60 * 24 * 30, '/', null, false, true );

        // Check if user exists
        if ( !$user || !Hash::check( $request->input( 'password' ), $user->password ) ) {
            // Login failed (Either user not found or incorrect password)
            return redirect( '/login' )->with( [
                'message' => 'Login failed. Please check your credentials.',
                'status'  => false,
                'error'   => '',
            ] );
        }

        $email = $request->input( 'email' );
        $user_id = $user->id;
        $request->session()->put( 'email', $email );
        $request->session()->put( 'user_id', $user_id );
        $data = [
            'message' => 'User logged in successfully',
            'status'  => true,
            'error'   => '',
        ];
        return redirect( '/dashboard' )->with( [
            'message' => 'User login successfully',
            'status'  => true,
            'error'   => '',
        ] );

    } //End Method
    public function dashboardPage( Request $request ) {
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
            'products'   => $products,
            'categories' => $categories,
            'customers'  => $customers,
            'invoices'   => $invoices,
            'total'      => round( $total ),
            'vat'        => round( $vat ),
            'payable'    => round( $payable ),
            'discount'   => $discount,
        ];
        return Inertia::render( 'DashboardPage', [
            'dashboard' => $data
        ] );
    } //End Method
    public function userLogout( Request $request ) {
        // return response()->json( [
        //     'status'  => 'success',
        //     'message' => 'User logged out successfully',
        // ], 200 )->cookie( 'token', '', -1, '/', null, false, true );
        $request->session()->flush();
        return redirect( '/login' )->with( [
            'message' => 'User logout successfully',
            'status'  => true,
            'error'   => '',
        ] );
    } //End Method
    public function sendOtp( Request $request ) {
        $user = User::where( 'email', $request->input( 'email' ) )->first();
        if ( $user ) {
            $otp = rand( 1000, 9999 );
            $user->update( ['otp' => $otp] );
            Mail::to( $user->email )->send( new OTPMail( $otp ) );
            // return response()->json( [
            //     'status'  => "success",
            //     'message' => "OTP {$otp} sent successfully",

            // ] );
            $request->session()->put( 'email', $user->email );
            return redirect( '/verify-otp' )->with( [
                'message' => 'OTP sent successfully',
                'status'  => true,
                'error'   => '',
            ] );
        } else {
            // return response()->json( [
            //     'status'  => 'error',
            //     'message' => 'User not found',
            // ], 404 );
            return redirect( '/send-otp' )->with( [
                'message' => 'User not found',
                'status'  => false,
                'error'   => '',
            ] );
        }
    } //End Method

    public function verifyOtp( Request $request ) {
        // $email = $request->input( 'email' );
        $email = $request->session()->get( 'email' );
        $user = User::where( 'email', $email )->first();
        if ( $user ) {
            if ( $user->otp === $request->input( 'otp' ) ) {
                $user->update( ['otp' => 0] );
                // $token = JWTToken::createTokenSetPassword( $user->email );
                $request->session()->put( 'otp_verified', true );
                // return response()->json( [
                //     'status'  => "success",
                //     'message' => "OTP verified successfully",
                //     'token'   => $token,
                // ], 200 )->cookie( 'token', $token, 60 * 24 * 30, '/', null, false, true );
                return redirect( '/reset-password' )->with( [
                    'message' => 'OTP verified successfully',
                    'status'  => true,
                    'error'   => '',
                ] );
            } else {
                // return response()->json( [
                //     'status'  => 'error',
                //     'message' => 'OTP not matched',
                // ], 404 );
                return redirect( '/verify-otp' )->with( [
                    'message' => 'OTP not matched',
                    'status'  => false,
                    'error'   => '',
                ] );
            }
        } else {
            return response()->json( [
                'status'  => 'error',
                'message' => 'User not found',
            ], 404 );
        }
    } //End Method

    public function resetPassword( Request $request ) {

        try {
            // $email = $request->header( 'email' );
            $email = $request->session()->get( 'email' );
            $password = $request->input( 'password' );
            $user = User::where( 'email', $email )->first();
            if ( $user ) {
                $otp_verified = $request->session()->get( 'otp_verified', 'default' );
                if ( $otp_verified === true ) {
                    $user->update( ['password' => Hash::make( $password )] );
                    $request->session()->forget( 'otp_verified' );
                    // return response()->json( [
                    //     'status'  => "success",
                    //     'message' => "Password reset successfully",
                    // ], 200 );
                    return redirect( '/login' )->with( [
                        'message' => 'Password reset successfully',
                        'status'  => true,
                        'error'   => '',
                    ] );
                } else {
                    return redirect( '/reset-password' )->with( [
                        'message' => 'OTP not verified',
                        'status'  => false,
                        'error'   => '',
                    ] );
                }
            } else {
                // return response()->json( [
                //     'status'  => 'error',
                //     'message' => 'User not found',
                // ], 404 );
                return redirect( '/reset-password' )->with( [
                    'message' => 'User not found',
                    'status'  => false,
                    'error'   => '',
                ] );
            }

        } catch ( Exception $e ) {
            // return response()->json( [
            //     'status'  => 'error',
            //     'message' => $e->getMessage(),
            // ], 500 );
            return redirect( '/reset-password' )->with( [
                'message' => $e->getMessage(),
                'status'  => false,
                'error'   => '',
            ] );
        }
    } //End Method

    // Page Methode
    public function loginPage() {
        // Check if user is already authenticated
    if (session('user_id')) {
        // Redirect to dashboard if already logged in
        return redirect()->route('dashboard.page');
    }
        return Inertia::render( 'LoginPage' );
    }
    public function registerPage() {
        return Inertia::render( 'RegisterPage' );
    } //End Method
    public function resetPasswordPage() {
        return Inertia::render( 'ResetPasswordPage' );
    } //End Method
    public function sendOtpPage() {
        return Inertia::render( 'SendOtpPage' );
    } //End Method
    public function verifyOtpPage() {
        return Inertia::render( 'VerifyOtpPage' );
    } //End Method

    public function profilePage(Request $request) {
        $user_id = $request->header( 'id' );
        $user = User::where( 'id', $user_id )->first();
        return Inertia::render( 'ProfilePage', [
            'user' => $user
        ] );
    }

    public function updateProfile(Request $request) {
        $user_id = $request->header( 'id' );
        $user = User::where( 'id', $user_id )->first();
        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'phone' => 'required|string|max:255',
            // Add more fields as needed, but DO NOT include 'phone'
        ]);
        $user->update($validated);
        return redirect( '/profile' )->with( [
            'message' => 'Profile updated successfully',
            'status'  => true,
            'error'   => '',
        ]);
    }

}
