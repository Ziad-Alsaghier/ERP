<?php

namespace App\Http\Controllers;
use Prgayman\Zatca\Facades\Zatca;

use Illuminate\Http\Request;

class TestingController extends Controller
{
    public function index(){
        $settings_data = \App\Models\Utility::settingsById(\Auth::user()->creatorId());
        $currentDate = gmdate('Y-m-d\TH:i:s\Z');
        $qrCode = Zatca::sellerName($settings_data['sellerName'])
                    ->vatRegistrationNumber($settings_data['vatRegistrationNumber'])
                    ->timestamp($currentDate)
                    ->totalWithVat('150.00')
                    ->vatTotal('22.50')
                    ->toQrCode();
        return $qrCode;
    }
}
