<?php 
namespace Models;
use \Models\Admin;

class FacePlusPlus {
    private $apiKey;
    private $apiSecret;
    private $baseUrl = 'https://api-us.faceplusplus.com/facepp/v3/';

    public function __construct($apiKey, $apiSecret) {
        $this->apiKey = $apiKey;
        $this->apiSecret = $apiSecret;
    }

    public function compareFaces($imageFile1, $imageFile2) {
        $url = $this->baseUrl . 'compare';
        $ch = curl_init($url);

        $postData = [
            'api_key' => $this->apiKey,
            'api_secret' => $this->apiSecret,
            'image_file1' => new CURLFile($imageFile1),
            'image_file2' => new CURLFile($imageFile2)
        ];

        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);

        $response = curl_exec($ch);
        curl_close($ch);

        return json_decode($response, true);
    }
}

?>