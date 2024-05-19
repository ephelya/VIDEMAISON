<?php 
namespace Models;
use \Models\Admin;

class Remaker {
    private $apiKey;
    private $baseUrl = 'https://developer.remaker.ai/api/remaker/v1/';

    public function __construct($apiKey) {
        $this->apiKey = $apiKey;
    }

    public function faceSwap($targetImagePath, $swapImagePath) {
        $url = $this->baseUrl . 'face-swap/create-job';
        $ch = curl_init($url);

        $headers = [
            'Authorization' => 'Bearer ' . $this->apiKey,
            'Accept' => 'application/json'
        ];

        $postData = [
            'target_image' => new CURLFile($targetImagePath),
            'swap_image' => new CURLFile($swapImagePath)
        ];

        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);

        $response = curl_exec($ch);
        curl_close($ch);

        return json_decode($response, true);
    }

    public function multipleFaceSwap($targetImagePath, $modelFacesZipPath) {
        $url = $this->baseUrl . 'face-detect/create-swap';
        $ch = curl_init($url);

        $headers = [
            'Authorization' => 'Bearer ' . $this->apiKey,
            'Accept' => 'application/json'
        ];

        $postData = [
            'target_image' => new CURLFile($targetImagePath),
            'model_face' => new CURLFile($modelFacesZipPath)
        ];

        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);

        $response = curl_exec($ch);
        curl_close($ch);

        return json_decode($response, true);
    }
}


?>