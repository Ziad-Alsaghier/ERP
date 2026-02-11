<?php
namespace Packages\Zatca;

use DOMDocument;

class InvoiceXML{


    private array $data;
    /**
     * InvoiceXML constructor.
     * @param array $data
     */
    public function __construct(array $data)
    {
        $this->data = $data;
    }
    public function build(): string
    {
        $dom = new DOMDocument('1.0', 'UTF-8');
        $dom->formatOutput = true;
        $invoiceFormat = new InvoiceFormat($this->data);
        $invoice = $dom->createElement('Invoice');
        $invoice->setAttribute('xmlns', 'urn:oasis:names:specification:ubl:schema:xsd:Invoice-2');
        $invoice->setAttribute('xmlns:cac', 'urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2');
        $invoice->setAttribute('xmlns:cbc', 'urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2');
        $invoice->setAttribute('xmlns:ext', 'urn:oasis:names:specification:ubl:schema:xsd:CommonExtensionComponents-2');

        // Extensions
        $ext = $invoiceFormat-> ublExtensions($dom);
        $invoice->appendChild($ext);

        // Header info
        $header = $invoiceFormat-> header($dom);
        foreach ($header as $el) {
            $invoice->appendChild($el);
        }
             // Extensions
             $ext = $invoiceFormat->additionalDocumentReference($dom);
            foreach ($ext as $el) {
                $invoice->appendChild($el);
            }
        // Supplier
        //  $signature = $invoiceFormat-> Signature($dom['signature']);
        // $invoice->appendChild(node: $signature);
         // Supplier
        $supplier = $invoiceFormat-> Supplier($dom);
        $invoice->appendChild($supplier);

        // Customer
        $customer = $invoiceFormat-> customer($dom);
        $invoice->appendChild($customer);
        // Delivery
        $delivery = $invoiceFormat-> delivery($dom);
        $invoice->appendChild($delivery);
        // Payment Means
        $paymentMeans = $invoiceFormat-> paymentMeans($dom);
        $invoice->appendChild($paymentMeans);
        // Allowance Charges
        $allowanceCharges = $invoiceFormat-> allowanceCharge($dom);
        foreach ($allowanceCharges as $el) {
            $invoice->appendChild($el);
        }
        // Tax Total
        $taxTotal = $invoiceFormat-> taxTotal($dom);
        foreach ($taxTotal as $el) {
            $invoice->appendChild($el);
        }

        // Legal Monitory Total
        $legalMonitoryTotal = $invoiceFormat-> legalMonetaryTotal($dom);
        $invoice->appendChild($legalMonitoryTotal);

        $dom->appendChild($invoice);
     // Invoice Lines
     $lines = $invoiceFormat-> invoiceLine($dom);
     foreach ($lines as $el) {
     $invoice->appendChild($el);
     }

        return $dom->saveXML();
    }









}











