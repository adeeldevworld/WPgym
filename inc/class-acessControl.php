<?php

class WPGymAccessControl
{
    private static $apiKey = 'SEZWANxMOGYM';
    private static $baseUrl = 'https://mogymapi.easypay.mu';
    private static $ip = '179.0.0.198'; 
    private static $port = 4370;  
    private static $commKey = '0'; 

    /**
     * Static method to set the IP, port, and commKey.
     *
     * @param string $ip 
     * @param int $port
     * @param string $commKey 
     */
    public static function setConnectionDetails($ip, $port, $commKey)
    {
        self::$ip = $ip;
        self::$port = $port;
        self::$commKey = $commKey;
    }

    /**
     * Static method to add a new user.
     *
     * @param array $userData 
     * @return string .
     */
    public static function addUser($userData)
    {
        $endpoint = '/updateUser';
        $queryParameters = "?ip=" . self::$ip . "&port=" . self::$port . "&commKey=" . self::$commKey . "&isNewUser=true";
        return self::sendCurlRequest($endpoint . $queryParameters, 'POST', $userData);
    }

    /**
     * Static method to update an existing user.
     *
     * @param array $userData 
     * @return string 
     */
    public static function updateUser($userData)
    {
        $endpoint = '/updateUser';
        $queryParameters = "?ip=" . self::$ip . "&port=" . self::$port . "&commKey=" . self::$commKey . "&isNewUser=false";
        return self::sendCurlRequest($endpoint . $queryParameters, 'POST', $userData);
    }

    /**
     * Static method to delete a user.
     *
     * @param string $enrollNumber 
     * @return string 
     */
    public static function deleteUser($enrollNumber)
    {
        $endpoint = '/deleteUser';
        $queryParameters = "?ip=" . self::$ip . "&port=" . self::$port . "&commKey=" . self::$commKey . "&enrollNumber={$enrollNumber}";
        return self::sendCurlRequest($endpoint . $queryParameters, 'POST', null);
    }

    /**
     * Helper method to send a cURL request.
     *
     * @param string $endpoint 
     * @param string $method 
     * @param array|null $data 
     * @return string 
     */
    private static function sendCurlRequest($endpoint, $method, $data)
    {
        $url = self::$baseUrl . $endpoint;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        $headers = [
            'x-api-key: ' . self::$apiKey,
            'Content-Type: application/json',
        ];

        // Handle Content-Length header
        if ($data === null) {
            $headers[] = 'Content-Length: 0';
        } else {
            $jsonData = json_encode($data);
            $headers[] = 'Content-Length: ' . strlen($jsonData);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
        }

        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $response = curl_exec($ch);
        if (curl_errno($ch)) {
            $response = 'cURL Error: ' . curl_error($ch);
        }

        // Close the cURL session
        curl_close($ch);

        return $response;
    }
}

