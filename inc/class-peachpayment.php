<?php
/**
 * Class for peachPyament Method integration
 */
namespace PeachPayments;
class PeachPayments
{
    private const BASE_URL = 'https://testapi-v2.peachpayments.com';
    private const ENTITY_ID = '8ac7a4c98850d6c70188526cfffe0439'; 
    private const USER_ID = 'c3bf66332ac611ee9b4906f4cbb0b715';
    private const PASSWORD = 'c3bf663b2ac611ee9b4906f4cbb0b715'; 
    public static function sendCurlRequest($url, $headers, $data=null)
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            return [
                'error' => true,
                'message' => 'cURL Error: ' . curl_error($ch),
            ];
        }
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        curl_close($ch);

        $responseData = json_decode($response, true);
        return [
            'error' => false,
            'httpCode' => $httpCode,
            'response' => $responseData,
        ];
    }

    public static function initiatePayment($amount, $currency, $paymentBrand, $paymentType, $shopperResultUrl)
    {
        $url = self::BASE_URL . '/payments';

        // Authentication details
        $authentication = [
            'userId' =>  self::USER_ID,
            'password' => self::PASSWORD,
            'entityId' => self::ENTITY_ID,
        ];
        // Request payload
        $data = [
            'authentication' => $authentication,
            'merchantTransactionId' => uniqid('wpgym_' , true),
            'amount' => $amount,
            'currency' => $currency,
            'paymentBrand' => $paymentBrand,
            'paymentType' => $paymentType,
            'shopperResultUrl' => $shopperResultUrl,
        ];

        $headers = [
            'Content-Type: application/json',
        ];

        $result = self::sendCurlRequest($url, $headers, $data);

        if ($result['error']) {
            return [
                'error' => true,
                'message' => $result['message'],
            ];
        }
        
        if ($result['httpCode'] !== 200) {
            return [
                'error' => true,
                'message' => 'API Error: HTTP ' . $result['httpCode'],
                'details' => $result['response'],
            ];
        }

        // Handle redirect if present in the response
        if (isset($result['response']['redirect']['url'])) {
            $redirectUrl = $result['response']['redirect']['url'];
            $parameters = $result['response']['redirect']['parameters'];

            $queryString = http_build_query(array_column($parameters, 'value', 'name'));
            $fullRedirectUrl = $redirectUrl . '?' . $queryString;

            return [
                'error' => false,
                'message' => 'Redirect required',
                'redirectUrl' => $fullRedirectUrl,
            ];
        }

        // Return the API response
        return [
            'error' => false,
            'message' => 'Payment initiated successfully',
            'response' => $result['response'],
        ];
    }
    /**
     * Static method to fetch transaction details by transaction ID.
     *
     * @param string $transactionId The unique transaction ID.
     * @return array|false 
     */
    public static function fetchTransactionDetails(string $transactionId)
    {
        $url = self::BASE_URL . '/payments/' . $transactionId;
        $queryParams = http_build_query([
            'authentication.entityId' => self::ENTITY_ID,
            'authentication.userId' => self::USER_ID,
            'authentication.password' => self::PASSWORD,
        ]);

        $fullUrl = $url . '?' . $queryParams;

        $ch = curl_init();

        curl_setopt_array($ch, [
            CURLOPT_URL => $fullUrl,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => ['Accept: application/json'],
            CURLOPT_TIMEOUT => 30,
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);

        curl_close($ch);

        // Check for cURL errors
        if ($curlError) {
            return ['success' => false, 'error' => "cURL Error: $curlError"];
        }

        // Check HTTP status code
        if ($httpCode !== 200) {
            return [
                'success' => false,
                'error' => "HTTP Code: $httpCode",
                'response' => $response
            ];
        }

        // Decode JSON response
        $decodedResponse = json_decode($response, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            return ['success' => false, 'error' => 'Invalid JSON response'];
        }

        // Extract transaction result code
        $transactionCode = $decodedResponse['result']['code'] ?? null;
        $transactionStatus = self::getTransactionStatus($transactionCode);
        $isSuccess = ($transactionStatus === 'Success');
        return [
            'success' => $isSuccess,
            'status' => $transactionStatus,
            'data' => $decodedResponse,
        ];
    }

    /**
     * Get transaction status message based on the result code.
     */
    private static function getTransactionStatus($code)
    {
        $statusMapping = [
            '000.000.000' => 'Success',
            '000.100.110' => 'Pending',
            '000.200.000' => 'Pending',

            '100.396.101' => 'Failed',
            '800.400.100' => 'Failed',
            '800.400.500' => 'Failed',
            '900.100.300' => 'Failed - Card expired',
            '200.300.404' => 'Failed - Invalid credentials',
            '000.400.104' => 'Failed - 3D Secure Configuration Issue',
        ];

        return $statusMapping[$code] ?? 'Unknown';
    }



}
