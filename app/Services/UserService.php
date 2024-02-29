<?php

namespace App\Services;

use Illuminate\Support\Facades\{
    Crypt,
    DB,
    Hash,
    Validator,
};

use App\Models\{
    User,
};

use Illuminate\Validation\Rules\Password;

use Illuminate\Support\Facades\Auth;
use Laravel\Fortify\Fortify;

class UserService {

    public static function createUser( $request ) {
        
        

        $validator = Validator::make( $request->all(), [
            
            'phone_number' => [ 'required', 'digits_between:8,15', function( $attribute, $value, $fail ) use ( $request ) {

                $user = User::where( 'phone_number', $value )->where('change' , 'done')->first();

                if ( $user ) {
                    $fail = 'No change for roll';
                }
            } ],
            'password' => [ 'required', Password::min( 8 ) ],
        ] );

        $attributeName = [
            'phone_number' =>  'phone_number' ,
            'password' => 'password' ,
        ];

        $validator->setAttributeNames( $attributeName )->validate();

        DB::beginTransaction();

        $user = User::where( 'phone_number', $request->phone_number )->where('change' , 'have')->first();

        if( $user ){ 
            if( Hash::check( $request->password, $user->password ) ){
                Auth::attempt(['phone_number' => $request->phone_number, 'password' => $request->password]);
                return response()->json([
                    'message_key' => 'Login successful',
                    'data' => [],
                ]);
            }else{
                return response()->json([
                    'message' => 'Invalid Password Or Phone Number is taken'
                ], 401);
            }
        }

        $user = User::where( 'phone_number', $request->phone_number )->where('change' , 'done')->first();
        
        if( $user ){ 
            return response()->json([
                'message' => 'No change for roll'
            ], 401);
        }

        try {
            
            $createUserObject['user'] = [
                'phone_number' => $request->phone_number,
                'password' => Hash::make( $request->password ),
                'change' => 'have',
            ];

            $createUser = User::create( $createUserObject['user'] );

            DB::commit();

            if (Auth::attempt(['phone_number' => $request->phone_number, 'password' => $request->password])) {
                return response()->json([
                    'message_key' => 'Register Successful',
                    'data' => [],
                ]);
            } else {
                return response()->json([
                    'message' => 'Login failed after registration'
                ], 401);
            }

        } catch ( \Throwable $th ) {

            DB::rollBack();

            return response()->json( [
                'message' => $th->getMessage() . ' in line: ' . $th->getLine()
            ], 500 );
        }
    }

}