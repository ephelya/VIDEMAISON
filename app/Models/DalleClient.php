<?php 
namespace Models;
use \Models\Admin;

class DalleClient {
    private $apiKey;
    private $apiUrl;

    public function __construct($apiKey, $apiUrl) {
        $this->apiKey = $apiKey;
        $this->apiUrl = $apiUrl;
    }

    public function generateImage($prompt) {
        // Utiliser cURL ou Guzzle ici
        $client = new GuzzleHttp\Client();
        $response = $client->request('POST', $this->apiUrl, [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json'
            ],
            'json' => [
                'prompt' => $prompt,
                'n' => 1 // Nombre d'images à générer
            ]
        ]);

        return json_decode($response->getBody(), true);
    }
}

?>