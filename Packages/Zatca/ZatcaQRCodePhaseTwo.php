<?php

namespace Packages\Zatca;

use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Encoding\Encoding;
use Illuminate\Support\Facades\Storage;

class ZatcaQRCodePhaseTwo
{
    // Class implementation will Start Generate QRCode

// Start Generate Qrcode

    /**
     * Summary of generateBase64QRCode
     * @param string $sellerName
     * @param string $vatRegistrationNumber
     * @param string $timestamp
     * @param float $totalAmount
     * @param float $vatAmountFormatted
     * @return string
     */
    use InvoiceServices;
    public function generateBase64QRCode(string $sellerName, string $vatRegistrationNumber, string $timestamp, float $totalAmount, float $vatAmountFormatted,$invoiceData)
    {

        // Start Generate Invoice XML
        $invoiceXML = new InvoiceXML($this->data($invoiceData));
        // Save the generated XML to storage
        $xmlContent = $invoiceXML->build();
        Storage::disk('public')->put('invoices/' . $invoiceData['invoice']['invoice_id'] . '.xml', $xmlContent);
        $invoiceContent = file_get_contents(storage_path('app/public/invoices/' . $invoiceData['invoice']['invoice_id'] . '.xml'));

        // Generate SHA256 hash of the invoice XML content
        $invoiceHashBase64 = base64_encode(hash('sha256', $invoiceContent, true));
        $getDegitalCertificate = $this->getDegitalCertificate();

          // ✅ Don't decode — use as-is (base64 encoded)
        // Use the base64-encoded X.509 certificate as the signature (binarySecurityToken)
        $signatureBase64 = $getDegitalCertificate['binarySecurityToken'];
          $publicKeyBase64 = $getDegitalCertificate['secret'];

          // Step 6: Build TLV tags 1-8
          $tlv =
          $this->toTLV(1, $sellerName) .
          $this->toTLV(2, $vatRegistrationNumber) .
          $this->toTLV(3, $timestamp) .
          $this->toTLV(4, number_format($totalAmount, 2, '.', '')) .
          $this->toTLV(5, number_format($vatAmountFormatted, 2, '.', '')) .
          $this->toTLV(6, $invoiceHashBase64) .
          $this->toTLV(7, $signatureBase64) .
          $this->toTLV(8, $publicKeyBase64);

            $base64 = base64_encode($tlv);

        $result = Builder::create()
            ->writer(new \Endroid\QrCode\Writer\SvgWriter())
            ->data($base64)
            ->encoding(new Encoding('UTF-8'))
            ->build();
        return $result->getString();
    }
    private function toTLV(int $tag, string $value): string
    {
    $length = mb_strlen($value, '8bit');
    return pack('C2', $tag, $length) . $value;
    }


    public function getDegitalCertificate()
    {
        // This function should return the digital certificate as a string
        // For example, it could read from a file or return a hardcoded value
        $sandBox = new Sandbox('/compliance');
        $compliance = $sandBox->compliance();

        // Store the binarySecurityToken in storage
        if (isset($compliance['binarySecurityToken'])) {
            $certificateContent = "-----BEGIN CERTIFICATE-----\n" . $compliance['binarySecurityToken'] . "\n-----END CERTIFICATE-----";
            Storage::disk('local')->put('app/public/cert/certificate.pem', $certificateContent);
        }
        return $compliance;
    }
}
