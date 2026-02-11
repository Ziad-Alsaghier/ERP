<?php

namespace App\Http\Controllers;

use App\Models\Utility;
use Illuminate\Http\Request;

class userinfoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $setting = Utility::settings();
        $logo = Utility::get_file('uploads/logo/');
        $company_logos = $setting['company_logo_light'] ?? '';
        $image = $logo . '/' . (isset($company_logos) && !empty($company_logos) ? $company_logos : 'logo-dark.png');
        
        $data = [
            'background' => $image,
            'logo' => $image,
            'settings' =>$setting['header_bill'],
        ];
    
        return response()->json($data);
    }
    

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
