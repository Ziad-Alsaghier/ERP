<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ApiGenerate extends Controller
{
    public function generate_api(Request $request)
    {
        if(!$request->password){
            return response()->json([
                'status' => 'error',
                'message' => 'Password is required',
            ], 422);
        }
        $response = Http::post('https://api.thefuture-erp.com/public/api/auth/generate_key/user', [
            'email' => auth()->user()->email,
            'password' => $request->password,
        ]);
        $data = $response->json();
        if ($response->failed() || isset($data['Error'])) {
            return response()->json([
                'status' => 'error',
                'message' => $data['Error'] ?? 'Something went wrong',
            ], 422);
        }

        return response()->json([
            'status' => 'success',
            'api_key' => $data['Access Token']
        ]);
    }

}
