<?php

namespace App\Helper;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class JWTToken {
    public static function createToken( $userEmail, $userId ) {
        $key = env( 'JWT_KEY' );
        $payload = [
            'iss'       => 'pointofsale',
            'iat'       => time(),
            'exp'       => time() + ( 60 * 60 * 24 ),
            'userEmail' => $userEmail,
            'userId'    => $userId,
        ];
        $token = JWT::encode( $payload, $key, 'HS256' );
        return $token;
    } //End Method

    public static function createTokenSetPassword( $userEmail ) {
        $key = env( 'JWT_KEY' );
        $payload = [
            'iss'       => 'pointofsale',
            'iat'       => time(),
            'exp'       => time() + ( 60 * 60 * 24 ),
            'userEmail' => $userEmail,
            'userId'    => '0',
        ];
        $token = JWT::encode( $payload, $key, 'HS256' );
        return $token;
    } //End Method

    public static function verifyToken( $token ) {
        try {
            if ( $token ) {
                $key = env( 'JWT_KEY' );
                $decoded = JWT::decode( $token, new Key( $key, 'HS256' ) );
                return $decoded;
            } else {
                return response()->json( [
                    'status'  => 'error',
                    'message' => 'Unauthorized',
                ] );
            }
        } catch ( Exception $e ) {
            return response()->json( [
                'status'  => 'error',
                'message' => 'Unauthorized',
            ] );
        }
    }

}