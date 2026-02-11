<?php

namespace App\Traits;

use Carbon\Carbon;


/*
|--------------------------------------------------------------------------
| Api Responser Trait
|--------------------------------------------------------------------------
|
| This trait will be used for any response we sent to clients.
|
*/

trait ZatcaServices
{
    public function getCsr()
    {
    $csrPath = storage_path(path: 'app/public/cert/request.csr');

    // Check if the CSR file exists
    if (file_exists($csrPath)) {
    $csr = file_get_contents($csrPath);
    return $csr;
    } else {
    throw new \Exception("❌ CSR file not found.");
    }
    }

    /**
    * Retrieves the private key from the storage path.
    *
    * @return string The contents of the private key file.
    * @throws \Exception If the private key file does not exist.
    */
    public function getPrivateKey()
    {

    $privateKeyPath = storage_path('app/public/cert/private.key');
    if (file_exists($privateKeyPath)) {
    $privateKeyPath = file_get_contents($privateKeyPath);
    // Get String between "-----BEGIN CERTIFICATE REQUEST-----" and "-----END CERTIFICATE REQUEST-----"
    $privateKeyPath = preg_replace('/-----BEGIN PRIVATE KEY-----\s*(.*?)\s*-----END PRIVATE KEY-----/s', '$1',
    $privateKeyPath);
    return $privateKeyPath;
    } else {
    throw new \Exception("❌ Private key file not found.");
    }
    }


    public function data($customer, $settings_data, $iteams, $invoice){
       $information =  [
            'uuid' => '6f4d20e0-6bfe-4a80-9389-7dabe6620f12',
            'custom_id' => $customer->id,
            'model' => 'IOS',
            'CRN_number' => $settings_data['CRN_number'] ?? '',
            'VAT_name' => "مؤسسة حلول الطباعة",
            'VAT_number' => $settings_data['vatRegistrationNumber'] ?? '',
            'location' => [
                'city' => $settings_data['city'] ?? 'Riyadh',
                'city_subdivision' => $settings_data['company_address_bill'] ?? 'Riyadh',
                'street' => $settings_data['company_address_bill'] ?? 'Riyadh',
                'plot_identification' => $settings_data['building_number'] ?? 'Riyadh',
                'building' => $settings_data['building_number'] ?? 'Riyadh',
                'postal_zone' => $settings_data['company_zipcode_bill'] ?? '12345',
            ],
            'branch_name' => 'My Branch Name',
            'branch_industry' => 'Food',
            'cancelation' => [
                'cancelation_type' => 'INVOICE',
                'canceled_invoice_number' => '',
            ],
        ];

        $line_item = array();
        foreach($iteams as $item){

                $line_item[] = [
                'id' => $item->id,
                'name' => $item->product->name ?? 'Product Name',
                'quantity' => $item->quantity,
                'tax_exclusive_price' => $item->price,
                'VAT_percent' => 0.15,
                'other_taxes' => [
                    ['percent_amount' => 1]
                ],
                'discounts' => [
                    ['amount' =>$item->discount, 'reason' => 'A discount'],

                ],
            ];

        }
        $invoice = [
            'invoice_counter_number' => $invoice->invoice_id,
            'invoice_serial_number' => \Auth::user()->invoiceNumberFormat($invoice->invoice_id),
            'issue_date' => date('Y:m:d', strtotime($invoice->issue_date)),
            'issue_time' => date('H:i:s', strtotime($invoice->issue_date)),
            'previous_invoice_hash' => 'NWZlY2ViNjZmZmM4NmYzOGQ5NTI3ODZjNmQ2OTZjNzljMmRiYzIzOWRkNGU5MWI0NjcyOWQ3M2EyN2ZiNTdlOQ==', // AdditionalDocumentReference/PIH
            'line_items' => $line_item,
        ];
        return [
            'information' => $information,
            'items' => $line_item ,
            'invoice' => $invoice,
        ];
    }







}
