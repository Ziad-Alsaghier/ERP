<?php

namespace Packages\Zatca;

use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Encoding\Encoding;

class ZatcaQRCodePhaseOne
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
    public function generateBase64QRCode(string $sellerName, string $vatRegistrationNumber, string $timestamp, float $totalAmount, float $vatAmountFormatted)
    {
        $tlv = $this->toTLV(1, $sellerName)
            . $this->toTLV(2, $vatRegistrationNumber)
            . $this->toTLV(3, $timestamp)
            . $this->toTLV(4, $totalAmount)
            . $this->toTLV(5, $vatAmountFormatted);
        $base64 = base64_encode($tlv);

        $result = Builder::create()
            ->writer(new \Endroid\QrCode\Writer\SvgWriter())
            ->data($base64)
            ->encoding(new Encoding('UTF-8'))
            ->build();
        return $result->getString();
    }
     function toTLV(int $tag, string $value): string
    {
        //  Tag length Value 🔢
        $value = trim($value); // Tag
        $valueBytes = mb_convert_encoding($value, 'UTF-8');// Value;
        $length = strlen($valueBytes); // length عدد البايتات الفعلية

        // TLV = Tag (1 byte) + Length (1 byte) + Value (n bytes)
        return pack('H*', sprintf('%02X%02X%s', $tag, $length, bin2hex($valueBytes)));
    }
}
