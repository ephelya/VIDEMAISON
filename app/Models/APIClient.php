<?php
namespace Models;
use \Models\Admin;
use Utils\Forms;

class APIClient {
    protected $apiKey;
    protected $apiUrl;

    public function __construct($apiKey, $apiUrl) {
        $this->apiKey = $apiKey;
        $this->apiUrl = $apiUrl;
    }

    protected function curlPost($url, $postData) {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $this->apiKey,
            'Content-Type: application/json'
        ]);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postData));
        $response = curl_exec($ch);

        if ($response === false) {
            $this->logError(curl_error($ch));
            curl_close($ch);
            return null;
        }

        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        if ($httpCode != 200) {
            $this->logError("HTTP status code: $httpCode, response: $response");
            curl_close($ch);
            return null;
        }

        curl_close($ch);
        return json_decode($response, true);
    }

    protected function logError($error) {
        file_put_contents('error_log.txt', date('Y-m-d H:i:s') . " - " . $error . PHP_EOL, FILE_APPEND);
    }
}

class OpenAIClient extends APIClient {
    public function generateText($prompt, $model = "text-davinci-003") {
        $postData = [
            'model' => $model,
            'prompt' => $prompt,
            'max_tokens' => 150
        ];
        return $this->curlPost($this->apiUrl, $postData);
    }
}

class DalleClient extends APIClient {
    public function generateImage($prompt) {
        $postData = [
            'prompt' => $prompt,
            'n' => 1  // Nombre d'images à générer
        ];
        return $this->curlPost($this->apiUrl, $postData);
    }
}


?>