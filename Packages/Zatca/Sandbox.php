<?php

namespace Packages\Zatca;

use Illuminate\Support\Facades\Http;

class Sandbox
{
    //This Class is for Sandbox Environment Zatca API

    protected $username;
    protected $password;
    protected $uri = 'https://gw-fatoora.zatca.gov.sa/e-invoicing/developer-portal/';
    protected $endpoint = '/compliance';
    use InvoiceServices;
    public function __construct($endpoint )
    {
        // Constructor can be used for initialization if needed
        $this->endpoint = $endpoint ?: $this->endpoint; // Default endpoint if none is provided
    }
    /**
     * Summary of compliance
     * @param mixed $endpoint
     * @return array{csr: string}
     */
    public function compliance()
    { // This Function For Generate CSID For Zatca Sandbox
            // You can pass a custom endpoint or use the default one
        $this->username = "TUlJQ1BUQ0NBZU9nQXdJQkFnSUdBWXp6Z0VoTk1Bb0dDQ3FHU000OUJBTUNNQlV4RXpBUkJnTlZCQU1NQ21WSmJuWnZhV05wYm1jd0hoY05NalF3TVRFd01UTXhNVFUwV2hjTk1qa3dNVEE1TWpFd01EQXdXakIxTVFzd0NRWURWUVFHRXdKVFFURVdNQlFHQTFVRUN3d05VbWw1WVdSb0lFSnlZVzVqYURFbU1DUUdBMVVFQ2d3ZFRXRjRhVzExYlNCVGNHVmxaQ0JVWldOb0lGTjFjSEJzZVNCTVZFUXhKakFrQmdOVkJBTU1IVlJUVkMwNE9EWTBNekV4TkRVdE16azVPVGs1T1RrNU9UQXdNREF6TUZZd0VBWUhLb1pJemowQ0FRWUZLNEVFQUFvRFFnQUVvV0NLYTBTYTlGSUVyVE92MHVBa0MxVklLWHhVOW5QcHgydmxmNHloTWVqeThjMDJYSmJsRHE3dFB5ZG84bXEwYWhPTW1Obzhnd25pN1h0MUtUOVVlS09Cd1RDQnZqQU1CZ05WSFJNQkFmOEVBakFBTUlHdEJnTlZIUkVFZ2FVd2dhS2tnWjh3Z1p3eE96QTVCZ05WQkFRTU1qRXRWRk5VZkRJdFZGTlVmRE10WldReU1tWXhaRGd0WlRaaE1pMHhNVEU0TFRsaU5UZ3RaRGxoT0dZeE1XVTBORFZtTVI4d0hRWUtDWkltaVpQeUxHUUJBUXdQTXprNU9UazVPVGs1T1RBd01EQXpNUTB3Q3dZRFZRUU1EQVF4TVRBd01SRXdEd1lEVlFRYURBaFNVbEpFTWpreU9URWFNQmdHQTFVRUR3d1JVM1Z3Y0d4NUlHRmpkR2wyYVhScFpYTXdDZ1lJS29aSXpqMEVBd0lEU0FBd1JRSWhBSUY4akljeHp2Q3lxVURUcDVPbXY3MlVweFBBTG1vUnl0OURZMjRqV21CUUFpQTBiYVo2WXJwcDV5SjRhaG9vb1czK09hOGtrYjMxZXZBb0hkdmdEODA2M3c9PQ==";
        $this->password = "PKoGsSwpPx236yNS7CWDojV4doe1i0W+5mPodbMEW5k=";
        // Ensure the endpoint is set correctly
        $auth = base64_encode($this->username . ':' . $this->password);
        $headers = [
            'OTP' => '12345',
            'accept' => 'application/json',
            'Accept-Version' => 'V2',
            'Content-Type' => 'application/json',
        ];
        $data = $this->complianceData();
        $response = $this->uri($this->uri, $data, $headers); // Send the request to the Zatca API

        if ($response->successful()) {
            $json = $response->json();
            $binarySecurityToken = $json['binarySecurityToken'] ?? null;
            $secret = $json['secret'] ?? null;

            if ($binarySecurityToken && $secret) {
                $storagePath = storage_path('app/zatca_tokens.json');
                $tokens = [
                    'binarySecurityToken' => $binarySecurityToken,
                    'secret' => $secret,
                ];
                file_put_contents($storagePath, json_encode($tokens, JSON_PRETTY_PRINT)); // Save the tokens to a file in the storage directory
            }
        }
        return $response->json(); // Return the JSON response from the API;
    }
    /**
     * Summary of complianceData
     * @return array{csr: array|string|null}
     */
    private function complianceData(){
        $csr = $this->getCsr();
        $csrBase64 = base64_encode(($csr));
        return [
            'csr' => $this->cleanCsrString($csr)
        ];
}
  /**
   * Summary of cleanCsrString
   * @param string $csr
   * @return array|string|null
   */
  public function cleanCsrString(string $csr): string
    {
    return preg_replace('/\s+/', '', $csr); // Remove all whitespace characters (spaces, tabs, newlines)
    }
    /**
     * Summary of uri
     * @param mixed $url
     * @param array $data
     * @param array $headers
     * @return \Illuminate\Http\Client\Response
     */
    private function uri($url, array $data, array $headers = [])
    {
        $url = $this->uri . '/' . $this->endpoint; // Construct the full URL using the base URI and the endpoint
        $response = Http::withHeaders($headers) // Set the headers for the request
        ->withOptions(['verify' => false]) // Disable SSL verification for sandbox
        ->asJson() // Set the request body to be JSON
        ->post($url, $data); // Send a POST request to the specified URL with the provided data
        return $response;
    }
}
