<?php

namespace Packages\Zatca;

use Carbon\Carbon;
use DateTimeZone;
use DOMDocument;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Encoding\Encoding;
use Exception;

class InvoiceFormat
{

    protected array $data;
    protected array $supplier;
    protected object $customer;
    protected array $delivery;
    protected $uuid;
    protected array $paymentMeans;
    protected array $allowancCharge;
    protected array $taxTotal;
    protected array $lines;
    public function __construct(array $data)
    {
        $this->data = $data;
        $this->supplier = $data['supplier'] ?? [];
        $this->customer = $data ['customer'] ?? [];
        $this->delivery = $data ['delivery'] ?? [];
        $this->lines= $data ['lines'] ?? [];
        $this->paymentMeans= $data ['payment_means'] ?? [];
        $this->allowancCharge= $data ['allowance_charge'] ?? [];
        $this->taxTotal= $data ['tax_total'] ?? [];
    }


    // Invoice Header
    /**
     * Generate the header elements for the invoice in ZATCA format.
     * @param DOMDocument $dom The DOMDocument instance to create elements in.
     * @return array
     */
    use InvoiceServices;


        /**     * Generate the UBL extensions for the invoice.
     * @param DOMDocument $dom The DOMDocument instance to create elements in.
     * @return \DOMElement The UBL extensions element.
     */
    public function ublExtensions(DOMDocument $dom)
    {
        // <ext:UBLExtensions>
        $ubleExtensions = $dom->createElement('ext:UBLExtensions');
        // <ext:UBLExtension>
        $ubleExtension = $dom->createElement('ext:UBLExtension');
        // <ext:ExtensionURI>
        $extensionURI = $dom->createElement('ext:ExtensionURI', 'urn:oasis:names:specification:ubl:dsig:enveloped:xades');
        // <ext:ExtensionContent>
        $extensionContent = $dom->createElement('ext:ExtensionContent');
        // <ext:ExtensionContent>
        $ubleExtension->appendChild($extensionURI); // Append extensionURI in ubleExtension
        $ubleExtension->appendChild($extensionContent); // Append extensionContent in ubleExtension
        $ubleExtensions->appendChild($ubleExtension); // append ubleExtension in ubleExtensions
        // <sig:UBLDocumentSignatures>
        $ublDocumentSignatures = $dom->createElementNs(
            'urn:oasis:names:specification:ubl:schema:xsd:CommonSignatureComponents-2',
            'sig:UBLDocumentSignatures'
        );
        $namespaces = [
            'xmlns:sig' => 'urn:oasis:names:specification:ubl:schema:xsd:CommonSignatureComponents-2',
            'xmlns:sac' => 'urn:oasis:names:specification:ubl:schema:xsd:SignatureAggregateComponents-2',
            'xmlns:sbc' => 'urn:oasis:names:specification:ubl:schema:xsd:SignatureBasicComponents-2'
        ];
        foreach ($namespaces as $attr => $value) {
            $ublDocumentSignatures->setAttributeNS('http://www.w3.org/2000/xmlns/', $attr, $value);
        }
        $extensionContent->appendChild($ublDocumentSignatures); // append UBLDocumentSignatures in extensionContent
        // <sac:SignatureInformation>
        $signatureInformation = $dom->createElement('sac:SignatureInformation');
        $ublDocumentSignatures->appendChild($signatureInformation); // Append SignatureInformation In UBLDocumentSignatures
        // <cbc:ID>
        $cbcSignature = $dom->createElement('cbc:ID', $this->uuid);
        $signatureInformation->appendChild($cbcSignature); // Append SignatureInfo In signatureInformation
        // <sbc:ReferencedSignatureID>
        $invoiceSignature = $dom->createElementNS('urn:oasis:names:specification:ubl:schema:xsd:SignatureBasicComponents-2', 'sbc:ReferencedSignatureID', 'urn:oasis:names:specification:ubl:signature:Invoice');
        $signatureInformation->appendChild($invoiceSignature); // Append referencedSignatureID In invoiceSignature
        // <ds:Signature>
        $digitalSignature = $dom->createElement('ds:Signature');
        $digitalSignature->setAttribute('xmlns:ds', 'http://www.w3.org/2000/09/xmldsig#');
        $signatureInformation->appendChild($digitalSignature); // Append referencedSignatureID In invoiceSignature
        // <ds:SignedInfo>
        $signatureInfo = $dom->createElement('ds:SignedInfo');
        $digitalSignature->appendChild($signatureInfo); // Append In digital Signature  => Info Signature
        // <ds:CanonicalizationMethod>
        $canonicalizationMethod = $dom->createElement('ds:CanonicalizationMethod');   // Appemd Signature Information => SignatureMethod
        $canonicalizationMethod->setAttribute('Algorithm', 'http://www.w3.org/2006/12/xml-c14n11'); // SignatureMethod Atributes
        $signatureInfo->appendChild($canonicalizationMethod); // Append In digital Signature => Info Signature
        // <ds:SignatureMethod>
        $signatureMethod = $dom->createElement('ds:SignatureMethod'); // Appemd Signature Information => SignatureMethod
        $signatureMethod->setAttribute('Algorithm', 'http://www.w3.org/2001/04/xmldsig-more#rsa-sha256'); // SignatureMethod Atributes
        $signatureInfo->appendChild($signatureMethod); // Append In digital Signature => Info Signature
        // <ds:Reference URI="#signature">
        $digitalSignatureReference = $dom->createElement('ds:Reference');
        $digitalSignatureReference->setAttribute('URI', '#invoiceSignedData'); // SignatureMethod Atributes
        $signatureInfo->appendChild($digitalSignatureReference); // Append In digital Signature => Info Signature
        // <ds:Transforms>
        $transforms = $dom->createElement('ds:Transforms');
        $digitalSignatureReference->appendChild($transforms); // Append In digital Signature => Info Signature
        $transformData = [
            ['Algorithm' => 'http://www.w3.org/TR/1999/REC-xpath-19991116', 'XPath' => 'not(//ancestor-or-self::ext:UBLExtensions)'],
            ['Algorithm' => 'http://www.w3.org/TR/1999/REC-xpath-19991116', 'XPath' => 'not(//ancestor-or-self::cac:Signature)'],
            ['Algorithm' => 'http://www.w3.org/TR/1999/REC-xpath-19991116', 'XPath' => 'not(//ancestor-or-self::cac:AdditionalDocumentReference[cbc:ID=\'QR\'])'],
        ];
        foreach ($transformData as $data) {
            $transform = $dom->createElement('ds:Transform');
            $transform->setAttribute('Algorithm', $data['Algorithm']);
            $transformXPath = $dom->createElement('ds:XPath', $data['XPath']);
            $transform->appendChild($transformXPath);
            $transforms->appendChild($transform);
        }
        // <ds:DigestMethod>
        $digestMethod = $dom->createElement('ds:DigestMethod');
        $digestMethod->setAttribute('Algorithm', 'http://www.w3.org/2001/04/xmlenc#sha256');
        $digitalSignatureReference->appendChild($digestMethod); // Append In digital Signature => Info Signature
        // <ds:DigestValue>
        $digestValue = $dom->createElement('ds:DigestValue', 'Hss2gNFjBY5OJn/5CEVZSSNUMrSf4QlCMxwsioPN6fA='); // Digest Value
        $digitalSignatureReference->appendChild($digestValue); // Append In digital Signature => Info Signature
        // <ds:Reference Type="http://www.w3.org/2000/09/xmldsig#SignatureProperties" URI="#xadesSignedProperties">
        $referenceSinature2 = $dom->createElement('ds:Reference');
        $referenceSinature2->setAttribute('Type', 'http://www.w3.org/2000/09/xmldsig#SignatureProperties'); // SignatureMethod Atributes
        $referenceSinature2->setAttribute('URI', '#xadesSignedProperties'); // SignatureMethod Atributes
        $signatureInfo->appendChild($referenceSinature2); // Append In digital Signature => Info Signature
        // <ds:DigestMethod>
        $digestMethod2 = $dom->createElement('ds:DigestMethod');
        $digestMethod2->setAttribute('Algorithm', 'http://www.w3.org/2001/04/xmlenc#sha256');
        $referenceSinature2->appendChild($digestMethod2); // Append In digital Signature => Info Signature
        // <ds:DigestValue>
        $digestValue2 = $dom->createElement('ds:DigestValue', 'NTUzMzVmMjExNWRjYzZkYzRlNjI1Y2Q1NDM1NWMwYjMzZjQ4MTZiYjlhOTZlMmY5ZDkzM2Q3ZDM1ODliNjE0ZA=='); // Digest Value
        $referenceSinature2->appendChild($digestValue2); // Append In digital Signature => Info Signature
        // <ds:SignatureValue>
        $signatureValue = $dom->createElement('ds:SignatureValue', 'MEUCIBxyR8rc4K8728wdSF4XSDqPs+rIL+3TFh9m+aNxQPtSAiEA6cHapItvp13yMSu66NbOg2CpomHwUSnYJ9h6uGQ65aY='); // Signature Value
        $digitalSignature->appendChild($signatureValue); // Append In digital Signature => Info Signature
        // <ds:KeyInfo>
        $keyInfo = $dom->createElement('ds:KeyInfo');
        $digitalSignature->appendChild($keyInfo); // Append In digital Signature => Info Signature
        // <ds:X509Data>
        $keyData = $dom->createElement('ds:X509Data');
        $keyInfo->appendChild($keyData); // Append In digital Signature => Info Signature
        // <ds:X509Certificate>
        $keyDataCertificate = $dom->createElement('ds:X509Certificate', 'MIIDXTCCAkWgAwIBAgIJALx+0X1v4J6OMA0GCSqGSIb3DQEBCwUAMIGVMQswCQYD');
        $keyData->appendChild($keyDataCertificate); // Append In digital Signature => Info Signature
        // <ds:Object>
        $digitalSignatureObject = $dom->createElement('ds:Object');
        $digitalSignature->appendChild($digitalSignatureObject); // Append In digital Signature => Info Signature
        // <xades:QualifyingProperties>
        $qualifyingProperties = $dom->createElement('xades:QualifyingProperties');
        $qualifyingProperties->setAttribute('Target', '#signature');
        $qualifyingProperties->setAttribute('xmlns:xades', 'http://www.w3.org/2000/09/xmldsig#');
        $digitalSignatureObject->appendChild($qualifyingProperties); // Append In digital Signature => Info Signature
        // <xades:SignedProperties>
        $SignedProperties = $dom->createElement('xades:SignedProperties');
        $SignedProperties->setAttribute('Id', 'xadesSignedProperties'); // signature Properties
        $qualifyingProperties->appendChild($SignedProperties); // Append In digital Signature => Info Signature
        // <xades:SignedSignatureProperties>
        $signSignatureProperties = $dom->createElement('xades:SignedSignatureProperties'); // Sign Signature Properties
        $SignedProperties->appendChild($signSignatureProperties); // Append In digital Signature => Info Signature
        // <xades:SigningTime>
        $signingTime = $dom->createElement('xades:SigningTime', $this->get_time_format(Carbon::now())); // Sign Signing Time
        $signSignatureProperties->appendChild($signingTime); // Append In digital Signature => Info Signature
        // <xades:SigningCertificate>
        $xadesCertificate = $dom->createElement('xades:SigningCertificate');
        $signSignatureProperties->appendChild($xadesCertificate); // Append In digital Signature => Info Signature
        // <xades:Cert>
        $xadesCert = $dom->createElement('xades:Cert');
        $xadesCertificate->appendChild($xadesCert); // Append In digital Signature => Info Signature
        // <xades:CertDigest>
        $xadesCertDegest = $dom->createElement('xades:CertDigest');
        $xadesCert->appendChild($xadesCertDegest); // Append In digital Signature => Info Signature
        // <xades:DigestMethod>
        $xadesCertDigestMethod = $dom->createElement('xades:DigestMethod');
        $xadesCertDigestMethod->setAttribute('Algorithm', 'http://www.w3.org/2001/04/xmlenc#sha256');
        $xadesCertDegest->appendChild($xadesCertDigestMethod); // Append In digital Signature => Info Signature
        // <xades:DigestValue>
        $xadesCertDigestValue = $dom->createElement('xades:DigestValue', 'ZDMwMmI0MTE1NzVjOTU2NTk4YzVlODhhYmI0ODU2NDUyNTU2YTVhYjhhMDFmN2FjYjk1YTA2OWQ0NjY2MjQ4NQ=='); // Digest Value
        $xadesCertDegest->appendChild($xadesCertDigestValue); // Append In digital Signature => Info Signature
        // <xades:IssuerSerial>
        $xadesIssuerSerial = $dom->createElement('xades:IssuerSerial');
        $xadesCert->appendChild($xadesIssuerSerial); // Append In digital Signature => Info Signature
        // <ds:X509IssuerName>
        $issuerName = $dom->createElement('ds:X509IssuerName', 'CN=PRZEINVOICESCA4-CA,DC=extgazt, DC=gov, DC=local');
        $xadesIssuerSerial->appendChild($issuerName); // Append In digital Signature => Info Signature
        // <ds:X509SerialNumber>
        $issueNumber = $dom->createElement('ds:X509SerialNumber', '379112742831380471835263969587287663520528387');
        $xadesIssuerSerial->appendChild($issueNumber); // Append In digital Signature => Info Signature
        // End Sign
        return $ubleExtensions;
    }

    public function header(DOMDocument $dom): array
    {
        $elements = [];
        // <cbc:ProfileID>
        // $profileID = $dom->createElement('cbc:ProfileID', $this->data['profile_id']);
        // $elements[] = $profileID;

        // <ProfileID>
        $profileID = $dom->createElement('cbc:ProfileID', $this->data['profile_id']);
        $elements[] = $profileID;
        // <cbc:ID>

        $id = $dom->createElement('cbc:ID', $this->data['id']);
        $elements[] = $id;

        // <cbc:UUID>
        $uuid = $dom->createElement('cbc:UUID', $this->data['uuid']);
        $elements[] = $uuid;
        $date = $this->data['issueDate'] ?? date('Y-m-d');
        $time = $this->data['issueTime'] ?? date('H:i');
        try {
            $timestamp = new \DateTime($date . ' ' . $time, new \DateTimeZone('Asia/Riyadh'));
            $timestamp = $timestamp->format('c'); // ISO 8601 format
        } catch (\Exception $e) {
            $timestamp = date('c'); // fallback to current date in ISO 8601 format
        }

        // <cbc:Issue Date>
        $issueDate = $dom->createElement('cbc:IssueDate',  date('Y-m-d', strtotime($date)));
        $elements[] = $issueDate;
        // <cbc:IssueTime>
        $issueTime = $dom->createElement('cbc:IssueTime',  date('H:i:s', strtotime($time)));
        $elements[] = $issueTime;

        // <cbc:InvoiceTypeCode>
        $typeCode = $dom->createElement('cbc:InvoiceTypeCode', $this->data['invoiceType']['type']);
        $typeCode->setAttribute('name', $this->data['invoiceType']['invoice']); // ممكن تغيرها حسب النوع
        $elements[] = $typeCode;

        // <cbc:DocumentCurrencyCode>
        $documentCurrencyCode = $dom->createElement('cbc:DocumentCurrencyCode', $this->data['DocumentCurrencyCode']);
        // $documentCurrencyCode->setAttribute('listID', 'ISO4217Alpha3');
        // $documentCurrencyCode->setAttribute('listAgencyID', '6');
        $elements[] = $documentCurrencyCode;
        // <cbc:TaxCurrencyCode>
        $taxCurrencyCode = $dom->createElement('cbc:TaxCurrencyCode', $this->data['taxCurrencyCode']);
        $elements[] = $taxCurrencyCode;
        return $elements;
    }


    /**
     * Generate the additional document reference elements for the invoice in ZATCA format.
     * @param DOMDocument $dom The DOMDocument instance to create elements in.
     * @return array The additional document reference elements.
     */
    public function additionalDocumentReference(DomDocument $dom)
    {
        $invoiceData = $this->data['lines'];

        $generateQrcode = $this->qrCodeHash($invoiceData);

        $this->data['QR'] = $generateQrcode;
        //     echo  $totalAmount = number_format($this->data['legal_monetary_total']['taxInclusiveAmount'], 2, '.', '');
        //    echo  $vatAmountFormatted = number_format($this->data['tax_total']['taxAmount'], 2, '.', '');        // Start Create Elements XML AdditionalDocumentReference
        // Start Create Elements XML AdditionalDocumentReference

        $data = $this->data['additional_document_reference'];
        foreach ($data as $additional) {
            $counter = $invoiceNumber ?? 1;
            $generateICV = $this->generateICV($counter);
            $base64 = base64_encode($generateICV);
            $invoiceNumber = $this->data['id'] ?? 00000 ?? null;
            if ($additional['id'] === "PIH") {
                $sha256Hex = hash('sha256', (string) $invoiceNumber);
                $additional['EmbeddedDocumentBinaryObject']['content'] = $sha256Hex;
            } else if ($additional['id'] === "QR") {
                $additional['EmbeddedDocumentBinaryObject']['content'] = $generateQrcode;
            }
            $additionalDocRef = $dom->createElement('cac:AdditionalDocumentReference'); // Create AdditionalDocumentReference
            $cbcId = $dom->createElement('cbc:ID', $additional['id']); // Create ID
            // Start Append
            $additionalDocRef->appendChild($cbcId); // append ubleExtension in ubleExtensions
            $additionalData[] = $additionalDocRef;
            if (isset($additional['EmbeddedDocumentBinaryObject'])) {
                $cacAttachmen = $dom->createElement('cac:Attachment');

                $cbcEmbeddedDocumentBinaryObject = $dom->createElement('cbc:EmbeddedDocumentBinaryObject',  $additional['EmbeddedDocumentBinaryObject']['content']); // Create EmbeddedDocumentBinaryObject
                // $qrCode->setAttribute('encodingCode', 'Base64'); // set schemeID
                // Append Elements
                $additionalDocRef->appendChild($cacAttachmen); // append ubleExtension in ubleExtensions
                $cacAttachmen->appendChild($cbcEmbeddedDocumentBinaryObject); // append ubleExtension in ubleExtensions
                $cbcEmbeddedDocumentBinaryObject->setAttribute('mimeCode', 'text/plain'); // set mimeCode
            } else {
                $filterNumber = (int)filter_var($invoiceNumber, FILTER_SANITIZE_NUMBER_INT);

                $cbcUuid = $dom->createElement('cbc:UUID', $filterNumber); // Create UUID
                $additionalDocRef->appendChild($cbcUuid); // append ubleExtension in ubleExtensions

            }
            // End Digital Signature
        }
        return $additionalData;
        // End Sign
    }

        /**
        * Generate the supplier element for the invoice in ZATCA format.
        * @param DOMDocument $dom The DOMDocument instance to create elements in.
        * @return \DOMElement The accounting supplier party element.
        */

    public function supplier(DOMDocument $dom): \DOMElement
    {
    $accountingSupplierParty = $dom->createElement('cac:AccountingSupplierParty');
    $party = $dom->createElement('cac:Party');

    // PartyIdentification
    $cacPartyIdentification =$party->appendChild($dom->createElement('cac:PartyIdentification'));
       $supplierID = $dom->createElement('cbc:ID', htmlspecialchars($this->supplier['registration_number'], ENT_XML1 |
       ENT_QUOTES, 'UTF-8'));
       $supplierID->setAttribute('schemeID', htmlspecialchars('CRN', ENT_XML1 | ENT_QUOTES, 'UTF-8'));
       $cacPartyIdentification->appendChild($supplierID);
    $accountingSupplierParty->appendChild($party);
    // End PartyIdentification

    // PostalAddress
    $country = $dom->createElement('cac:Country');
    $partyName = $dom->createElement('cac:PostalAddress');
    $partyName->appendChild($dom->createElement('cbc:StreetName', htmlspecialchars($this->supplier['company_address_bill'],
    ENT_XML1 |
    ENT_QUOTES, 'UTF-8')));
    $partyName->appendChild($dom->createElement('cbc:BuildingNumber', htmlspecialchars($this->supplier['building_number'], ENT_XML1 |
    ENT_QUOTES, 'UTF-8')));
   //  $partyName->appendChild($dom->createElement('cbc:PlotIdentification', htmlspecialchars($this->supplier['plotIdentification'], ENT_XML1 |
   //  ENT_QUOTES, 'UTF-8')));
    $partyName->appendChild($dom->createElement('cbc:CitySubdivisionName', htmlspecialchars($this->supplier['company_state_bill'], ENT_XML1 |
    ENT_QUOTES, 'UTF-8')));
    $partyName->appendChild($dom->createElement('cbc:CityName', htmlspecialchars($this->supplier['company_city'], ENT_XML1 |
    ENT_QUOTES, 'UTF-8')));
    $partyName->appendChild($dom->createElement('cbc:PostalZone', htmlspecialchars($this->supplier['company_zipcode'], ENT_XML1 |
    ENT_QUOTES, 'UTF-8')));
    $country->appendChild($dom->createElement('cbc:IdentificationCode',
    htmlspecialchars($this->supplier['site_currency_symbol'], ENT_XML1 |
    ENT_QUOTES, 'UTF-8')));
    $party->appendChild($partyName);
    $partyName->appendChild($country);



    //  Tax Number
   // regeistration Number
   $partyTaxScheme = $dom->createElement('cac:PartyTaxScheme');
        $registrationNumber = $dom->createElement('cbc:CompanyID', $this->supplier['vat_number']);
       //  $registrationNumber->setAttribute('schemeID',$this->supplier['schemeID']);
        $partyTaxScheme->appendChild($registrationNumber);
        $party->appendChild($partyTaxScheme);
   // regeistration Number
   $taxScheme = $dom->createElement('cac:TaxScheme');
           $textSchema = $dom->createElement('cac:TaxScheme');
    $partyTaxScheme->appendChild($textSchema)->appendChild($dom->createElement('cbc:ID',htmlspecialchars('VAT',
    ENT_XML1 | ENT_QUOTES, 'UTF-8')));


    // PartyIdentification
    $party->appendChild($dom->createElement('cac:PartyLegalEntity'))->appendChild($dom->createElement('cbc:RegistrationName',htmlspecialchars($this->supplier['sellerName'],
    ENT_XML1 | ENT_QUOTES, 'UTF-8')));
    $accountingSupplierParty->appendChild($party);
    // End PartyIdentification

    return $accountingSupplierParty;
    }

    /**
     * Generate the customer element for the invoice in ZATCA format.
     * @param DOMDocument $dom The DOMDocument instance to create elements in.
     * @return \DOMElement The accounting customer party element.
     */
    public function customer(DOMDocument $dom): \DOMElement
    {
    $customer = $this->customer;

    // Create root element
    $accountingCustomerParty = $dom->createElement('cac:AccountingCustomerParty');

    // <cac:Party>
        $party = $dom->createElement('cac:Party');

        // <cac:PartyIdentification>
            $partyIdentification = $dom->createElement('cac:PartyIdentification');
            $customerID = $dom->createElement('cbc:ID', $customer['registration_number'] ?? '1010620023');
            $customerID->setAttribute('schemeID', "CRN");
            $partyIdentification->appendChild($customerID);
            $party->appendChild($partyIdentification);

            // <cac:PostalAddress>
                $postalAddress = $dom->createElement('cac:PostalAddress');
                $postalAddress->appendChild($dom->createElement('cbc:StreetName', $customer['company_address_bill'] ??
                'King Fahd Road'));
                $postalAddress->appendChild($dom->createElement('cbc:BuildingNumber', $customer['billing_zip'] ??
                '123'));
                $postalAddress->appendChild($dom->createElement('cbc:CitySubdivisionName', $customer['billing_state'] ??
                'Riyadh'));
                $postalAddress->appendChild($dom->createElement('cbc:CityName', $customer['billing_city'] ?? 'Riyadh'));
                $postalAddress->appendChild($dom->createElement('cbc:PostalZone', $customer['billing_zip'] ?? '11564'));

                // Country block with IdentificationCode inside
                $country = $dom->createElement('cac:Country');
                $identificationCode = $dom->createElement('cbc:IdentificationCode', 'SA');
                $country->appendChild($identificationCode);
                $postalAddress->appendChild($country);

                $party->appendChild($postalAddress);

                // <cac:PartyTaxScheme>
                    $companyId = $customer['vat_number'] ?? '';

                    if (!ctype_digit($companyId) || strlen($companyId) !== 15 || !str_starts_with($companyId, '3') ||
                    !str_ends_with($companyId, '3')) {
                    throw new Exception("❌ Invalid VAT number in Customer Section");
                    }

                    $partyTaxScheme = $dom->createElement('cac:PartyTaxScheme');
                    $companyID = $dom->createElement('cbc:CompanyID', $companyId);

                    $taxScheme = $dom->createElement('cac:TaxScheme');
                    $cbcId = $dom->createElement('cbc:ID', 'VAT');
                    $cbcId->setAttribute('schemeID', 'UN/ECE 5153');
                    $cbcId->setAttribute('schemeAgencyID', '6');
                    $taxScheme->appendChild($cbcId);

                    $partyTaxScheme->appendChild($companyID);
                    $partyTaxScheme->appendChild($taxScheme);
                    $party->appendChild($partyTaxScheme);

                    // <cac:PartyLegalEntity>
                        $partyLegalEntity = $dom->createElement('cac:PartyLegalEntity');

                        $registrationName = $dom->createElement('cbc:RegistrationName', $customer['name'] ?? 'كفيل
                        أحمد');
                        $partyLegalEntity->appendChild($registrationName);

                        // Optional CompanyID under LegalEntity
                        $companyID2 = $dom->createElement('cbc:CompanyID', $customer['registration_number'] ??
                        '1010620023');
                        $partyLegalEntity->appendChild($companyID2);

                        $party->appendChild($partyLegalEntity);

                        // Finalize
                        $accountingCustomerParty->appendChild($party);

                        return $accountingCustomerParty;
                        }




    /**
     * Generate the delivery element for the invoice in ZATCA format.
     * @param DOMDocument $dom The DOMDocument instance to create elements in.
     * @return \DOMElement The delivery element.
     */
    public function delivery(DOMDocument $dom){
        $delivery = $dom->createElement('cac:Delivery');
        $delivery->appendChild($dom->createElement('cbc:ActualDeliveryDate', htmlspecialchars($this->delivery['actualDeliveryDate'], ENT_XML1 | ENT_QUOTES, 'UTF-8')));
        return $delivery;
    }

    /**
     * Generate the payment means element for the invoice in ZATCA format.
     * @param DOMDocument $dom The DOMDocument instance to create elements in.
     * @return \DOMElement The payment means element.
     */
    public function paymentMeans(DOMDocument $dom): \DOMElement
    {
        $paymentMeansData = $this->paymentMeans;
        $paymentMeans = $dom->createElement('cac:PaymentMeans');
        $paymentMeans->appendChild($dom->createElement('cbc:PaymentMeansCode', htmlspecialchars($paymentMeansData['paymentMeansCode'], ENT_XML1 | ENT_QUOTES, 'UTF-8')));
        return $paymentMeans;
    }

    /**
     * Generate the allowance and charge elements for the invoice in ZATCA format.
     * @param \DOMDocument $dom The DOMDocument instance to create elements in.
     * @return array The allowance and charge elements.
     */
    public function allowanceCharge(DOMDocument $dom)
{


    foreach ($this->allowancCharge as $allowCharge) {
        $allowanceCharge = $dom->createElement('cac:AllowanceCharge');
        // ChargeIndicator

        $chargeIndicator = $dom->createElement('cbc:ChargeIndicator', $allowCharge['charge_indicator']);
        // AllowanceChargeReason
        $allowanceChargeReason = $dom->createElement('cbc:AllowanceChargeReasonCode', $allowCharge['allowance_charge_reason_code']);
        // Amount
        $amount = $dom->createElement('cbc:Amount', number_format($allowCharge['amount'], 2, '.', ''));
        $amount->setAttribute('currencyID', $allowCharge['currency_id']);
        // TaxCategory
        $taxCategory = $dom->createElement('cac:TaxCategory');
        // cbc:ID
        $taxCategoryID = $dom->createElement('cbc:ID', $allowCharge['tax_category']['id']);
        $taxCategoryID->setAttribute('schemeID', $allowCharge['tax_category']['scheme_id']);
        $taxCategoryID->setAttribute('schemeAgencyID', $allowCharge['tax_category']['scheme_agency_id']);
        // cbc:Percent
        $taxCategoryPercent = $dom->createElement('cbc:Percent', $allowCharge['tax_category']['percent']);
        // cbc:TaxScheme
        $taxScheme = $dom->createElement('cac:TaxScheme');
        // cbc:ID
        $taxSchemeID = $dom->createElement('cbc:ID', $allowCharge['tax_category']['tax_scheme']['id']);
        $taxSchemeID->setAttribute('schemeID', $allowCharge['tax_category']['tax_scheme']['scheme_id']);
        $taxSchemeID->setAttribute('schemeAgencyID', $allowCharge['tax_category']['tax_scheme']['scheme_agency_id']);
        // End TaxCategory
    //    $dataXml =  $dom->appendChild($allowanceCharge);
       $catgory[] = $allowanceCharge;
       // Append Elements
        $allowanceCharge->appendChild($chargeIndicator); // append ubleExtension in ubleExtensions
        $allowanceCharge->appendChild($allowanceChargeReason); // append ubleExtension in ubleExtensions
        $allowanceCharge->appendChild($amount); // append ubleExtension in ubleExtensions
        $allowanceCharge->appendChild($taxCategory); // append ubleExtension in ubleExtensions
        $taxCategory->appendChild($taxCategoryID); // append ubleExtension in ubleExtensions
        $taxCategory->appendChild($taxCategoryPercent); // append ubleExtension in ubleExtensions
        $taxCategory->appendChild($taxScheme); // append ubleExtension in ubleExtensions
        $taxScheme->appendChild($taxSchemeID); // append ubleExtension in ubleExtensions

        $catgory[]= $allowanceCharge;
    }


    return $catgory;
}



    /**
     * Generate the tax total elements for the invoice in ZATCA format.
     * @param DOMDocument $dom The DOMDocument instance to create elements in.
     * @return array The tax total elements.
     */
  public function TaxTotal(DOMDocument $dom)
    {
    $taxes = $this->taxTotal;
    $taxesTotal = [];

    foreach ($taxes as $taxe) {
    $totalTaxAmount = 0;
    $taxableAmount = 0;
    $percent = 0;

    foreach ($this->lines as $line) {
    $lineExtensionAmount = $line['priceAmount'] * $line['quantity'];
    $discount = 0;

    if (isset($line['price']['allowanceCharges'])) {
    foreach ($line['price']['allowanceCharges'] as $allowance) {
    if ($allowance['isCharge'] === 'false') {
    $discount += $allowance['amount'];
    }
    }
    }

    $netAmount = $lineExtensionAmount - $discount;
    $vatRate = $line['item']['classifiedTaxCategory'][0]['percent'] ?? 15;

    $lineTaxAmount = round(($netAmount * $vatRate) / 100, 2);

    $totalTaxAmount += $lineTaxAmount;
    $taxableAmount += $netAmount;
    $percent = $vatRate;
    }

    // 1. TaxTotal
    $taxTotal = $dom->createElement('cac:TaxTotal');

    // 2. TaxAmount (لازم ييجي قبل TaxSubtotal)
    $taxAmount = $dom->createElement('cbc:TaxAmount', number_format($totalTaxAmount, 2, '.', ''));
    $taxAmount->setAttribute('currencyID', 'SAR');
    $taxTotal->appendChild($taxAmount);

    // 3. TaxSubtotal
    if (isset($taxe['tax_subtotal']) && is_array($taxe['tax_subtotal'])) {
    $taxSubtotal = $dom->createElement('cac:TaxSubtotal');

    $taxable = $dom->createElement('cbc:TaxableAmount', number_format($taxableAmount, 2, '.', ''));
    $taxable->setAttribute('currencyID', 'SAR');
    $taxSubtotal->appendChild($taxable);

    $subTaxAmount = $dom->createElement('cbc:TaxAmount', number_format($totalTaxAmount, 2, '.', ''));
    $subTaxAmount->setAttribute('currencyID', 'SAR');
    $taxSubtotal->appendChild($subTaxAmount);

    // TaxCategory
    $taxCategory = $dom->createElement('cac:TaxCategory');
    $taxCategoryID = $dom->createElement('cbc:ID', 'S');
    $taxCategoryID->setAttribute('schemeID', 'SAG');
    $taxCategoryID->setAttribute('schemeAgencyID', '6');
    $taxCategory->appendChild($taxCategoryID);

    $taxPercent = $dom->createElement('cbc:Percent', $percent);
    $taxCategory->appendChild($taxPercent);

    $taxScheme = $dom->createElement('cac:TaxScheme');
    $schemeID = $dom->createElement('cbc:ID', 'VAT');
    $schemeID->setAttribute('schemeID', 'SAG');
    $taxScheme->appendChild($schemeID);

    $taxCategory->appendChild($taxScheme);
    $taxSubtotal->appendChild($taxCategory);

    $taxTotal->appendChild($taxSubtotal);
    }

    $taxesTotal[] = $taxTotal;
    }

    return $taxesTotal;
    }

    /**
     * Build the legal monetary total element for the invoice in ZATCA format.
     * @param \DOMDocument $dom The DOMDocument instance to create elements in.
     * @return \DOMElement The legal monetary total element.
     */
    public function legalMonetaryTotal(DOMDocument $dom): \DOMElement
    {

        $chargesTotalAmount = $this->totalInvoiceChargers($this->allowancCharge,'chargeAmount');
        $allowancesTotalAmount = $this->allowanceLineItems();
        $allowanceTotalAmount = $this->totalInvoiceChargers($this->allowancCharge,'allowanceAmount');
        $lineExtensionAmount = $this->calculateLineExtensionAmount();

        $taxExclusiveAmount = $this->taxExclusiveAmount();

        $taxAmount = $this->calculateTaxAmount();
        $taxInclusiveAmount = $taxExclusiveAmount + $this->totalTax($lineExtensionAmount);
        $vat = $this->totalTax($lineExtensionAmount);
        $prepaidAmountValue = 0.00;
        $payableAmountValue = $taxInclusiveAmount - $prepaidAmountValue;

        $totalLegal = $dom->createElement('cac:LegalMonetaryTotal');
        $elements = [
            'cbc:LineExtensionAmount' => $lineExtensionAmount,
            'cbc:TaxExclusiveAmount' => $taxExclusiveAmount,
            'cbc:TaxInclusiveAmount' => $taxInclusiveAmount,
            'cbc:AllowanceTotalAmount' => $allowanceTotalAmount,
            // 'cbc:ChargeTotalAmount' => $chargesTotalAmount, // <-- إضافة هذا العنصر
            'cbc:PrepaidAmount' => $prepaidAmountValue,
            'cbc:PayableAmount' => $payableAmountValue,
        ];

        foreach ($elements as $tag => $value) {
            $el = $dom->createElement($tag, number_format($value, 2, '.', ''));
            $el->setAttribute('currencyID', 'SAR');
            $totalLegal->appendChild($el);
        }

        return $totalLegal;
    }





    /**
     * Generate the invoice line elements for the invoice in ZATCA format.
     * @param DOMDocument $dom The DOMDocument instance to create elements in.
     * @return array The invoice line elements.
     */
    public function invoiceLine(DOMDocument $dom): array
    {

        try {
            $elements = [];
            $key = 0;
            foreach ($this->lines as $key => $line) {
                $invoiceLine = $dom->createElement('cac:InvoiceLine');
                $elements[] = $invoiceLine;
                $key++;
                $key += 0;
                $priceAmount = $line['priceAmount'];
                $quantity = $line['quantity'];
                // Create Line ID
                $lineID = $dom->createElement('cbc:ID', $key);
                $invoiceLine->appendChild($lineID);
                // Create Section InvoicedQuantity
                $invoicedQuantity = $dom->createElement('cbc:InvoicedQuantity', number_format($line['quantity'], 5, '.', ''));
                $invoicedQuantity->setAttribute('unitCode', $line['unitCode']);
                $invoiceLine->appendChild($invoicedQuantity);
                // End Section InvoicedQuantity
                // Create Section LineExtensionAmount
                $this->lineExtension = 0.00;
                $allowanceAmount = $this->calculationAllwanc('allowance'); // Get All Allwoance
                $chargeAmount = $this->calculationAllwanc('charge'); // Get All Charge Amount
                $allowances = $line['price']['allowanceCharges'];

                $this->lineExtension = ($priceAmount * $quantity) + $chargeAmount['charge'] - $allowanceAmount['allowance'];
                $lineExtensionAmount = $dom->createElement('cbc:LineExtensionAmount', number_format($this->lineExtension, 2, '.', ''));
                $lineExtensionAmount->setAttribute('currencyID', 'SAR');
                $invoiceLine->appendChild($lineExtensionAmount);
                // End Section LineExtensionAmount



                // Section Tax Inclusive Amount
                // $this->taxInclusiveAmount = $this->calculationTaxInclusive() ;
                // $taxInclusive = $dom->createElement('cbc:TaxInclusiveAmount', round($this->taxInclusiveAmount,2));
                // $taxInclusive->setAttribute('currencyID', 'SAR');
                // $invoiceLine->appendChild($taxInclusive);
                // End Section Tax Inclusive Amount



                // Create Section TaxTotal
                $totalTax = 0.00;
                $classifiedTaxCategories = $line['item']['classifiedTaxCategory'];
                foreach ($classifiedTaxCategories as $classifiedTaxCategory) {
                    $totalTax = $classifiedTaxCategory['percent'] / 100;
                }
                $totalTaxAmount = $totalTax * $this->lineExtension;
                $taxTotal = $dom->createElement('cac:TaxTotal');
                // Create Section TaxAmount
                $vat = $line['taxTotal']['taxAmount'] / 100;
                $taxAmountValue = round($totalTaxAmount, 2);
                $taxAmount = $dom->createElement('cbc:TaxAmount', number_format( $totalTaxAmount,2, '.', ''));
                $taxAmount->setAttribute('currencyID', 'SAR');
                $roundingAmount = $this->lineExtension + $taxAmountValue;
                $roundingElement = $dom->createElement('cbc:RoundingAmount', number_format($roundingAmount,2,'.','')); // Rounding Amount
                $roundingElement->setAttribute('currencyID', 'SAR'); // Rounding Amount Currency
                $taxTotal->appendChild($taxAmount);
                $taxTotal->appendChild($roundingElement);
                // End Section TaxAmount
                // Create Section RoundingAmount
                // $roundingAmount = $dom->createElement('cbc:RoundingAmount', $line['taxTotal']['roundingAmount']);
                // $roundingAmount->setAttribute('currencyID', 'SAR');
                // $taxTotal->appendChild($roundingAmount);
                // End Section RoundingAmount
                $invoiceLine->appendChild($taxTotal);
                // End Section TaxTotal
                // Create Section Item
                $item = $dom->createElement('cac:Item');
                // Create Section Name
                $name = $dom->createElement('cbc:Name', $line['item']['name']);
                $item->appendChild($name);
                // End Section Name
                // Create Section ClassifiedTaxCategory
                $classifiedTaxCategory = $dom->createElement('cac:ClassifiedTaxCategory');
                foreach ($line['item']['classifiedTaxCategory'] as $category) {
                    // Create Section ID
                    $id = $dom->createElement('cbc:ID', $category['taxScheme']['ID']);
                    $classifiedTaxCategory->appendChild($id);
                    // End Section ID
                    // Create Section Percent
                    $percent = $dom->createElement('cbc:Percent', number_format($category['percent'], 2, '.', ''));
                    $classifiedTaxCategory->appendChild($percent);
                    // End Section Percent
                    // Create Section TaxScheme
                    $taxScheme = $dom->createElement('cac:TaxScheme');
                    // Create Section ID
                    $validCodes = ['S', 'Z', 'E', 'O'];

                    if (!in_array($category['taxScheme']['ID'], $validCodes)) {
                        // throw error or handle the case
                        throw new Exception("Invalid VAT category code: must be one of S, Z, E, O");
                    }
                    $taxSchemeID = $dom->createElement('cbc:ID', $category['taxScheme']['cbcType']);
                    $taxScheme->appendChild($taxSchemeID);
                    $classifiedTaxCategory->appendChild($taxScheme);
                    // End Section  TaxScheme


                }
                // End Section ID
                $item->appendChild($classifiedTaxCategory);
                // End Section ClassifiedTaxCategory
                // Create Section Price
                $price = $dom->createElement('cac:Price');
                // Create Section PriceAmount

                $totalPriceAmount = $dom->createElement('cbc:PriceAmount', number_format($priceAmount, 2,'.',''));
                $totalPriceAmount->setAttribute('currencyID', 'SAR');
                $price->appendChild($totalPriceAmount);
                // End Section PriceAmount
                // Create Section AllowanceCharge
                $allowanceCharge = $dom->createElement('cac:AllowanceCharge');
                // Create Section ChargeIndicator
                foreach ($line['price']['allowanceCharges'] as $charge) {
                    $chargeIndicator = $dom->createElement('cbc:ChargeIndicator', $charge['isCharge']);
                    $allowanceCharge->appendChild($chargeIndicator);
                    // End Section ChargeIndicator
                    // Create Section AllowanceChargeReason
                    $allowanceChargeReason = $dom->createElement('cbc:AllowanceChargeReasonCode', $charge['reason']);
                    $allowanceCharge->appendChild($allowanceChargeReason);
                    // End Section AllowanceChargeReason
                    // Create Section Amount
                        $amount = $dom->createElement('cbc:Amount', number_format($charge['amount'], 2, '.', ''));
                        $amount->setAttribute('currencyID', 'SAR');
                        $allowanceCharge->appendChild($amount);
                    // End Section Amount
                }

                $invoiceLine->appendChild($item); // End Section Item
                $price->appendChild($allowanceCharge); // End Section AllowanceCharge
                $invoiceLine->appendChild($price); // End Section Price
            }

            return $elements;
        } catch (\Throwable $th) {
            throw new Exception("SomeThing Wrong ❌ " . $th->getMessage() . " In file " . $th->getLine() . " In File" . $th->getFile());
        }
    }










}
