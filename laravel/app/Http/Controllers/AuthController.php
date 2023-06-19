<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;


const CLIENT_ID = 2;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        request()->request->add([
            'grant_type' => 'password',
            'client_id' => CLIENT_ID,
            'client_secret' => env('PASSPORT_SECRET'),
            'username' => $request->username,
            'password' => $request->password,
            'scope'         => '',
        ]);


        if (Auth::attempt(['username' => $request->username, 'password' => $request->password])) {//verifica se os dados são válidos
            if (!(Auth::attempt(['username' => $request->username, 'password' => $request->password, 'deleted_at' => null]))) {//verifica se está deleted
                return response()->json(
                    ['msg' => 'User Deleted'],
                    422
                );
                
            }

            if (!(Auth::attempt(['username' => $request->username, 'password' => $request->password, 'blocked' => '0']))) {//verifica se está blocked
                return response()->json(
                    ['msg' => 'User Blocked'],
                    422
                );
            }
        }

        
        
        


        $request = Request::create(env('PASSPORT_SERVER_URL') . '/oauth/token', 'POST');
        $response = Route::dispatch($request);
        $errorCode = $response->getStatusCode();


        //fazer pedido á api do passport
        if ($errorCode == '200') {
            return json_decode((string) $response->content(), true);
        } else {
            return response()->json(
                ['msg' => 'User credentials are invalid'],
                $errorCode
            );
        }
    }

    public function logout(Request $request)
    {
        $accessToken = $request->user()->token();
        $token = $request->user()->tokens->find($accessToken);
        $token->revoke();
        $token->delete();
        return response(['msg' => 'Token revoked'], 200);
    }
}
