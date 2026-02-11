<?php

namespace Packages\Zatca;

use Carbon\Carbon;
use DateTimeZone;
use Illuminate\Support\Facades\Auth;

trait InvoiceServices
{
    /**
     * Converts the provided data into a base64 encoded QR code string.
     *
     * @param string $sellerName The name of the seller.
     * @param string $vatRegistrationNumber The VAT registration number.
     * @param string $timestamp The timestamp of the invoice.
     * @param float $totalAmount The total amount of the invoice including VAT.
     * @param float $vatAmountFormatted The formatted VAT amount.
     * @return string The base64 encoded QR code string.
     */
    protected $allowanceTotalAmount;
    protected $chargesTotalAmount;
    protected $taxExclusiveAmount;
    protected $lineExtensionAmount;
    protected $lineExtension;
    protected float $vat;

    public function qrCodeHash(array $invoiceLines): string
    {
        $amount = 0;
        $vatAmount = 0;

        foreach ($invoiceLines as $line) {
            $lineAmount = $line['priceAmount'] * $line['quantity'];
            $amount += $lineAmount;

            if (!empty($line['item']['classifiedTaxCategory'])) {
                foreach ($line['item']['classifiedTaxCategory'] as $tax) {
                    if (isset($tax['percent'])) {
                        $vatAmount += ($tax['percent'] / 100) * $lineAmount;
                    }
                }
            }
        }

        $amountWithTax = $amount + $vatAmount;

        $sellerName = trim($this->data['supplier']['registrationName'] ?? '');
        if (str_contains($sellerName, '|')) {
            $sellerName = trim(explode('|', $sellerName)[1]);
        }

        $vatRegistrationNumber = trim($this->data['supplier']['CompanyID'] ?? '');

        $date = $this->data['issueDate'] ?? date('Y-m-d');
        $time = $this->data['issueTime'] ?? date('H:i:s');

        // تحديد ما إذا كان التوقيت يحتوي على "Z" (أي UTC)
        $rawTime = trim($this->data['issueTime'] ?? '');
        $isUtc = str_ends_with($rawTime, 'Z');

        try {
            if ($isUtc) {
                // ⏰ XML كان UTC — نحوله إلى KSA
                $datetime = new Carbon("{$date} {$time}", new DateTimeZone('UTC'));
                $datetime->setTimezone('Asia/Riyadh');
            } else {
                // ⏰ XML محلي بالفعل — نستخدمه كما هو
                $datetime = new Carbon("{$date} {$time}", new DateTimeZone('Asia/Riyadh'));
            }

            $timestamp = $datetime->format('Y-m-d\TH:i:sP'); // بصيغة +03:00
        } catch (\Exception $e) {
            $timestamp = Carbon::now(new DateTimeZone('Asia/Riyadh'))->format('Y-m-d\TH:i:sP');
        }

        $totalAmount = number_format($amountWithTax, 2, '.', '');
        $vatAmountFormatted = number_format($vatAmount, 2, '.', '');
        // $base64 = Zatca::sellerName($sellerName)
        // ->vatRegistrationNumber($vatRegistrationNumber)
        // ->timestamp($timestamp)
        // ->totalWithVat($totalAmount)
        // ->vatTotal($vatAmountFormatted)
        // ->toBase64();
        $csr = $this->getCsr();
        $tlv_2 = $this->toTLV(1, $sellerName)
            . $this->toTLV(2, $vatRegistrationNumber)
            . $this->toTLV(3, $timestamp)
            . $this->toTLV(4, $totalAmount)
            . $this->toTLV(5, $vatAmountFormatted);
        $base64 = base64_encode($tlv_2);

        return $base64;
    }
    function toTLV(int $tag, string $value): string
    {
        $value = trim($value);
        $valueBytes = mb_convert_encoding($value, 'UTF-8');
        $length = strlen($valueBytes); // عدد البايتات الفعلية

        // TLV = Tag (1 byte) + Length (1 byte) + Value (n bytes)
        return pack('H*', sprintf('%02X%02X%s', $tag, $length, bin2hex($valueBytes)));
    }

    /**
     * Parses a base64 encoded TLV (Tag-Length-Value) string.
     *
     * @param string $base64 The base64 encoded TLV string.
     * @return array An associative array where keys are tags and values are the corresponding values.
     */
    function parseTLV($base64)
    {
        $binary = base64_decode($base64);
        $i = 0;
        $result = [];
        while ($i < strlen($binary)) {
            $tag = ord($binary[$i++]);
            $length = ord($binary[$i++]);
            $value = substr($binary, $i, $length);
            $i += $length;
            $result[$tag] = $value;
        }
        return $result;
    }

    /**
     * Decodes a ZATCA QR code from a base64 encoded string.
     *
     * @param string $base64 The base64 encoded string of the ZATCA QR code.
     * @return array An associative array containing the decoded values.
     */
    public function decodeZatcaQr(string $base64)
    {
        $binary = base64_decode($base64);
        $i = 0;
        $result = [];

        while ($i < strlen($binary)) {
            $tag = ord($binary[$i++]);
            $length = ord($binary[$i++]);
            $value = substr($binary, $i, $length);
            $i += $length;

            switch ($tag) {
                case 1:
                    $result['Seller Name'] = $value;
                    break;
                case 2:
                    $result['VAT Number'] = $value;
                    break;
                case 3:
                    $result['Timestamp'] = $value;
                    break;
                case 4:
                    $result['Invoice Total (With VAT)'] = $value;
                    break;
                case 5:
                    $result['VAT Amount'] = $value;
                    break;
                default:
                    $result["Tag $tag"] = $value;
            }
        }

        return $result;
    }

    /**
     * Generates an ICV (Invoice Counter Value) based on the provided counter.
     *
     * @param int $counter The counter value to generate the ICV for.
     * @return string The generated ICV as a base64 encoded string.
     */
    function generateICV($counter)
    {
        return base64_encode(hash('sha256', (string) $counter, true));
    }


    /**
     * Retrieves the Certificate Signing Request (CSR) from the storage path.
     *
     * @return string The contents of the CSR file.
     * @throws \Exception If the CSR file does not exist.
     */
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
            $privateKeyPath = preg_replace('/-----BEGIN PRIVATE KEY-----\s*(.*?)\s*-----END PRIVATE KEY-----/s', '$1', $privateKeyPath);
            return $privateKeyPath;
        } else {
            throw new \Exception("❌ Private key file not found.");
        }
    }

    /**     * Tests if the certificate and private key match.
     * @return string A message indicating whether the certificate and private key match or not.
     * @throws \Exception If the certificate or private key file does not exist or is invalid.
     */
    public function testCertificateAndKey()
    {
        $certificatePath = storage_path('app\csr\cert.crt');
        $privateKeyPath = storage_path('app/csr/private.key');

        // قراءة الملفات
        $certificateContent = file_get_contents($certificatePath);
        $privateKeyContent = file_get_contents($privateKeyPath);

        if (!$certificateContent || !$privateKeyContent) {
            return '❌ فشل في قراءة الشهادة أو المفتاح الخاص.';
        }

        // تحميل الشهادة
        $certificate = openssl_x509_read($certificateContent);
        if (!$certificate) {
            return '❌ الشهادة غير صالحة.';
        }

        // تحميل المفتاح الخاص
        $privateKey = openssl_pkey_get_private($privateKeyContent);
        if (!$privateKey) {
            return '❌ المفتاح الخاص غير صالح أو يحتاج إلى كلمة مرور.';
        }

        // مقارنة المفتاح مع الشهادة
        $keyDetails = openssl_pkey_get_details($privateKey);
        $certDetails = openssl_x509_parse($certificate);

        if (isset($keyDetails['key']) && strpos($certificateContent, $keyDetails['key']) !== false) {
            return '✅ المفتاح الخاص يطابق الشهادة!';
        }

        return '⚠️ تم تحميل الشهادة والمفتاح بنجاح، لكن لم يتم التأكد من التطابق الكامل.';
    }

    /**
     * Formats the provided time string into a standard format.
     *
     * @param string $time The time string to format.
     * @return string The formatted time string.
     */
    public function get_time_format($time)
    {
        if (strpos($time, 'Z') !== false) {
            // UTC time
            $time = str_replace('Z', '', $time);
            return gmdate('H:i:s', strtotime($time));
        } else {
            // Local time
            return date('H:i:s', strtotime($time));
        }
    }



    /**
     *  This Section For Legal Monetary Total
     */

    protected function calculateLineExtensionAmount(): float
    {
        $total = 0.0;
        foreach ($this->lines as $line) {
            $total += $line['priceAmount'] * $line['quantity'];
        }
        $this->lineExtensionAmount = $total + $this->chargesTotalAmount - $this->allowanceTotalAmount;
        return round($this->lineExtensionAmount, 2);
    }
    protected function allowanceLineItems()
    {

        $totalAllowance = 0.0; // Total Allowance Amount
        foreach ($this->lines as $line) { // Loop through each line item
            $quantity = $line['quantity']; // Get the quantity of the line item
            if (isset($line['price']['allowanceCharges'])) { // Check if allowanceCharges exist
                foreach ($line['price']['allowanceCharges'] as $allowance) { // Loop through each allowance/charge
                    if ($allowance['isCharge'] === "false") { // Check if it is an allowance
                        $totalAllowance += $allowance['amount'] * $quantity; // Multiply by quantity to get total allowance amount
                    } else {
                        $totalAllowance -= number_format($allowance['amount'], 2, '.', '');
                    } // If it is a charge, we do not add it to the total allowance
                } // End Alowances loop
            } // End check for allowanceCharges
        } // End Lines loop
        $this->allowanceTotalAmount = number_format($totalAllowance, 2, '.', ''); // Format the total allowance amount

        return $this->allowanceTotalAmount;
    }


    protected function taxExclusiveAmount()
    {
        foreach ($this->lines as $line) {
            $allowance = $line['price']['allowanceCharges'] ?? [];
            $allowanceLine = 0.0;
            foreach ($allowance as $allowanceItem) {
                if ($allowanceItem['isCharge'] === "false") { // Check if it is an allowance
                    $allowanceLine += $allowanceItem['amount'] * $line['quantity']; // Multiply by quantity to get total allowance amount
                }
            }
            return $this->taxExclusiveAmount += $this->lineExtensionAmount - $allowanceLine; // Subtract allowance from line extension amount
        }


        $totalAllowance = 0.00;
        $totalCharge = 0.00;

        $this->taxExclusiveAmount = $this->lineExtensionAmount + $totalCharge - $totalAllowance;
        return $this->taxExclusiveAmount;
    }

    protected function calculateTaxAmount(): float
    {
        $totalTax = 0.0;
        $allowanceLine = $this->allowanceLineItems();
        $taxExclusiveAmount = $this->calculateLineExtensionAmount() - $allowanceLine;

        // Get the tax percent from the first line item
        $taxPercent = 0.0;
        foreach ($this->lines as $line) {
            if (isset($line['item']['classifiedTaxCategory']) && count($line['item']['classifiedTaxCategory']) > 0) {
                $taxPercent = $line['item']['classifiedTaxCategory'][0]['percent'] ?? 0.0;
                break;
            }
        }

        $totalTax = ($taxPercent / 100) * $taxExclusiveAmount;

        return round($totalTax, 2);
    }


    function calculateChargesTotalAmount(array $items): float
    {
        $totalCharges = 0.0;
        foreach ($items as $item) {
            foreach ($item['price']['allowanceCharges'] as $charge) {
                if ($charge['isCharge'] === "true") {
                    $totalCharges += $charge['amount'] * $item['quantity'];
                }
            }
        }
        $this->chargesTotalAmount = round($totalCharges);
        return round($totalCharges, 2);
    }

    public function totalInvoiceChargers($invoiceAllowance, $type): float
    {
        $charges = $invoiceAllowance;
        $totalAmount = 0.00;
        foreach ($charges as $charge) {
            $isCharge = $charge['charge_indicator'];
            if ($type == 'chargeAmount') {

                if ($isCharge === "true") {
                    $totalAmount += $charge['amount'];
                }
            } elseif ($type == 'allowanceAmount') {
                if (!$isCharge === "true") {
                    $totalAmount += $charge['amount'];
                }
            }
        }

        return $totalAmount;
        ;
    }
    // End Legal Monetary Total









    protected function data($data)
    {
        $uuid = \Ramsey\Uuid\Uuid::uuid4();
        $invoice = $data['invoice'];
        $invoiceData = [
            'profile_id' => 'reporting:1.0',
            'uuid' => $uuid,
            'id' => Auth::user()->invoiceNumberFormat($invoice->invoice_id), // old id format SME00010
            'issueDate' => Carbon::now('Asia/Riyadh')->format('Y-m-d H:i:s'),
            'issueTime' => Carbon::now('Asia/Riyadh')->format('H:i:s'),
            'DocumentCurrencyCode' => 'SAR',
            'taxCurrencyCode' => 'SAR',
            'invoiceType' => [
                'invoice' => '0100000',
                'type' => '388'
            ],
            'supplier' => $data['supplier'],


            'customer' => $data['customer'],
            'delivery' => [
                'actualDeliveryDate' => '2024-09-07',
            ],

            'payment_means' => [
                'id' => '1',
                'paymentMeansCode' => '30',
                'paymentDueDate' => '2024-09-07',
            ],
            'lines' => [
                [
                    'id' => 1,
                    'unitCode' => 'PCE',
                    'quantity' => 33.00000,
                    'priceAmount' => 3.00,
                    'pricing_reference' => [
                        'alternative_condition_price' => [
                            'amount' => 1150.00,
                            'currency' => 'SAR',
                            'price_type_code' => '01'
                        ]
                    ],
                    'item' => [
                        'name' => 'كتاب',
                        'classifiedTaxCategory' => [
                            [
                                'percent' => 15,
                                'taxScheme' => [
                                    'ID' => 'S',
                                    'cbcType' => 'VAT'
                                ]
                            ]
                        ],
                    ],
                    'price' => [
                        'amount' => 2,
                        'unitCode' => 'UNIT',
                        'allowanceCharges' => [
                            [
                                'isCharge' => 'false',
                                'reason' => 'discount',
                                'amount' => 0.00
                            ]
                        ]
                    ],
                    'taxTotal' => [
                        'taxAmount' => 0.5,
                        'roundingAmount' => 4.60
                    ]
                ],
                [
                    'id' => 2,
                    'unitCode' => 'PCE',
                    'quantity' => 3.000000,
                    'priceAmount' => 34.00,
                    'pricing_reference' => [
                        'alternative_condition_price' => [
                            'amount' => 1150.00,
                            'currency' => 'SAR',
                            'price_type_code' => '01'
                        ]
                    ],
                    'item' => [
                        'name' => 'قلم',
                        'classifiedTaxCategory' => [
                            [
                                'percent' => 15,
                                'taxScheme' => [
                                    'ID' => 'S',
                                    'cbcType' => 'VAT'
                                ]
                            ]
                        ],
                    ],
                    'price' => [
                        'amount' => 2,
                        'unitCode' => 'UNIT',
                        'allowanceCharges' => [
                            [
                                'isCharge' => 'false',
                                'reason' => 'discount',
                                'amount' => 0.00
                            ]
                        ]
                    ],
                    'taxTotal' => [
                        'taxAmount' => 0.5,
                        'roundingAmount' => 4.60
                    ]
                ],

            ],
            'tax_total' => [
                [
                    'amount' => 200,
                    'currencyID' => 'SAR',
                ],
                [
                    'amount' => 200,
                    'currencyID' => 'SAR',
                    'tax_subtotal' => [
                        'taxable_amount' => 300,
                        'sub_tax_amount' => 45,
                        'scheme_agency_id' => '6',
                        'amount' => 200,
                        'currencyID' => 'SAR',
                        'tax_category' => [
                            'ID' => 'S',
                            'scheme_agency_id' => '6',
                            'schemeID' => 'SAG',
                            'percent' => 15.00,
                            'tax_scheme' => [
                                'ID' => 'VAT',
                                'schemeID' => 'SAG',
                                'scheme_agency_id' => '6',
                            ]
                        ]
                    ]
                ]
            ],
            'monetary' => [
                'line_extension' => 200,
                'tax_exclusive' => 200,
                'tax_inclusive' => 230,
                'payable' => 230,
            ],
            'signature' => [
                'cbcId' => 'urn:oasis:names:specification:ubl:signature:Invoice',
                'signatureMethod' => 'urn:oasis:names:specification:ubl:dsig:enveloped:xades',
            ],
            'additional_document_reference' => [
                [
                    'id' => 'ICV',
                    'uuid' => '23',
                ],
                [
                    'id' => 'PIH',
                    'uuid' => $this->generateUuid(),
                    'EmbeddedDocumentBinaryObject' => [
                        'content' =>
                            'NWZlY2ViNjZmZmM4NmYzOGQ5NTI3ODZjNmQ2OTZjNzljMmRiYzIzOWRkNGU5MWI0NjcyOWQ3M2EyN2ZiNTdlOQ==',
                    ]
                ],
                [
                    'id' => 'QR',
                    'uuid' => $this->generateUuid(),
                    'EmbeddedDocumentBinaryObject' => [
                        'content' => 'AW/YtNix2YPYqSDYqtmI2LHZitivINin2YTYqtmD2YbZiNmE2YjYrNmK2Kcg2KjYo9mC2LXZiSDYs9ix2LnYqSDYp9mE2YXYrdiv2YjYr9ipIHwgTWF4aW11bSBTcGVlZCBUZWNoIFN1cHBseSBMVEQCDzM5OTk5OTk5OTkwMDAwMwMTMjAyMi0wOC0xN1QxNzo0MTowOAQGMjMxLjE1BQUzMC4xNQYsSHNzMmdORmpCWTVPSm4vNUNFVlpTU05VTXJTZjRRbENNeHdzaW9QTjZmQT0HYE1FVUNJUUNzK0ROUTF2bHo3Sm9vdkE3SlJqYWtuNHRVczBKbENjQW9KTmgvSjY1Rkh3SWdLcHB0MitEZmNMWHRLUTZ5UjQ5dGNWeWRncy9NU1kyeVY5dkFUemNwVXE0PQhYMFYwEAYHKoZIzj0CAQYFK4EEAAoDQgAEoWCKa0Sa9FIErTOv0uAkC1VIKXxU9nPpx2vlf4yhMejy8c02XJblDq7tPydo8mq0ahOMmNo8gwni7Xt1KT9UeAlHMEUCIQCxP4nIZp1lwlClG3Gt8nIvKKsGi7xXR1Y0K73iPbqgGwIgPYQuDPI4DAQAz0s5ndrojyQOoCkdyxNN1O+Xqmwv61w=',
                    ]
                ]
            ],
            'allowance_charge' => [

                [
                    'charge_indicator' => 'false',
                    'allowance_charge_reason_code' => 'discount',
                    'allowance_charge_reason' => 'Shipping fees',
                    'amount' => 0.00,
                    'scheme_agency_id' => '6',
                    'currency_id' => 'SAR',
                    'tax_category' => [
                        'id' => 'S',
                        'scheme_id' => 'UN/ECE 5305',
                        'scheme_agency_id' => '6',
                        'percent' => 15,
                        'tax_scheme' => [
                            'id' => 'VAT',
                            'scheme_agency_id' => '6',
                            'scheme_id' => 'SAG'
                        ]
                    ]
                ]
            ],
            'legalMonetaryTotal' => [
                'priceAmount' => 4,
                'taxExclusiveAmount' => 4,
                'taxInclusiveAmount' => 4.60,
                'prepaidAmount' => 0,
                'payableAmount' => 4.60,
                'allowanceTotalAmount' => 0
            ],
        ];

        return $invoiceData;
    }


    public function generateUuid()
    {
        return (string) \Illuminate\Support\Str::uuid();
    }

    // Invoice Lines Service
    protected function calculationTaxInclusive()
    {
        $lines = $this->lines;
        foreach ($lines as $line) {
            $item = $line['item'];
            $taxes = $item['classifiedTaxCategory'];
            $vat = 0.00;
            foreach ($taxes as $tax) {
                $vat += $tax['percent'];
            }
        }
        $totalVat = $this->lineExtension * $vat / 100;
        $taxExeclusiveAmount = $this->lineExtension + $totalVat;
        return $taxExeclusiveAmount;
    }
    protected function totalTax($lineExtensionAmount): float
    {
        $vat = 0.00;
        $lines = $this->lines;
        foreach ($lines as $line) {
            $itemsTax = $line['item']['classifiedTaxCategory'];
            foreach ($itemsTax as $tax) {
                $vat = $tax['percent'] / 100;
            }
        }
        $this->vat = $lineExtensionAmount * $vat;
        return $this->vat;
    }

    protected function calculationAllwanc($type)
    {
        $lines = $this->lines;
        $allwoances = 0.00;
        $charge = 0.00;
        $amount = 0.00;
        foreach ($lines as $line) {
            if (isset($line['price']['allowanceCharges'])) {
                foreach ($line['price']['allowanceCharges'] as $allowance) {
                    if ($type === "allowance") {
                        if ($allowance['isCharge'] === 'false') {
                            $allwoances += $allowance['amount'];
                        }
                    } else {
                        if ($allowance['isCharge'] === 'true') {
                            $charge += $allowance['amount'];
                        }
                    }
                }
            }
        }

        return [
            'allowance' => $allwoances,
            'charge' => $charge,
        ];
    }


    private function getInvoiceDataFromXml($xmlContent)
    {
        if (!$xmlContent) {
            throw new \Exception('XML content is empty or unreadable');
        }
        if (!$xmlContent) {
            throw new \Exception('XML content is empty or unreadable');
        }

        $dom = new \DOMDocument();
        $dom->preserveWhiteSpace = false;
        $dom->formatOutput = false;
        $dom->loadXML($xmlContent);
        $dom = new \DOMDocument();
        $dom->preserveWhiteSpace = false;
        $dom->formatOutput = false;
        $dom->loadXML($xmlContent);

        // إعداد XPath
        $xpath = new \DOMXPath($dom);
        $xpath->registerNamespace("ext", "urn:oasis:names:specification:ubl:schema:xsd:CommonExtensionComponents-2");
        $xpath->registerNamespace("cbc", "urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2");
        $xpath->registerNamespace("cac", "urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2");
        // إعداد XPath
        $xpath = new \DOMXPath($dom);
        $xpath->registerNamespace("ext", "urn:oasis:names:specification:ubl:schema:xsd:CommonExtensionComponents-2");
        $xpath->registerNamespace("cbc", "urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2");
        $xpath->registerNamespace("cac", "urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2");

        // إزالة العناصر المستثناة من التوقيع والهاش
        $tagsToRemove = [
            '//ext:UBLExtensions',
            "//cac:AdditionalDocumentReference[cbc:ID='QR']",
        ];
        // إزالة العناصر المستثناة من التوقيع والهاش
        $tagsToRemove = [
            '//ext:UBLExtensions',
            "//cac:AdditionalDocumentReference[cbc:ID='QR']",
        ];

        foreach ($tagsToRemove as $expression) {
            foreach ($xpath->query($expression) as $node) {
                $node->parentNode->removeChild($node);
            }
        }
        foreach ($tagsToRemove as $expression) {
            foreach ($xpath->query($expression) as $node) {
                $node->parentNode->removeChild($node);
            }
        }

        $rawXmlWithoutHeader = $dom->saveXML($dom->documentElement);
        $rawXmlWithoutHeader = $dom->saveXML($dom->documentElement);

        // Canonicalize باستخدام C14N 1.1
        $domClean = new \DOMDocument();
        $domClean->loadXML($rawXmlWithoutHeader);
        $canonicalXml = $domClean->C14N(true, false); // بدون تعليقات
        // Canonicalize باستخدام C14N 1.1
        $domClean = new \DOMDocument();
        $domClean->loadXML($rawXmlWithoutHeader);
        $canonicalXml = $domClean->C14N(true, false); // بدون تعليقات

        // حساب SHA256 بصيغة Base64 مباشرة
        $sha256HashBase64 = base64_encode(hash('sha256', $canonicalXml, true));
        // حساب SHA256 بصيغة Base64 مباشرة
        $sha256HashBase64 = base64_encode(hash('sha256', $canonicalXml, true));

        // استخراج UUID
        $uuidNodes = $domClean->getElementsByTagName('UUID');
        if ($uuidNodes->length === 0) {
            throw new \Exception('UUID not found in the XML');
        }
        $uuid = $uuidNodes->item(0)->nodeValue;
        // استخراج UUID
        $uuidNodes = $domClean->getElementsByTagName('UUID');
        if ($uuidNodes->length === 0) {
            throw new \Exception('UUID not found in the XML');
        }
        $uuid = $uuidNodes->item(0)->nodeValue;


        return [
            'uuid' => $uuid,
            'invoiceHash' => $sha256HashBase64,
            'invoice' => base64_encode($canonicalXml),
        ];
    }
}
