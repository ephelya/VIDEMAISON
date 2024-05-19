<?php 
namespace Models;
use \Models\Admin;


class OpenAIClient {
    private $apiKey;
    private $apiUrl;

    public function __construct($apiKey, $apiUrl) {
        $this->apiKey = $_ENV['OPENAI_API_KEY'] ?? getenv('OPENAI_API_KEY'); 
        $this->apiUrl = $apiUrl;
    }

    public function generateText($prompt, $model = "text-davinci-003") {
        // Ici, vous pouvez utiliser cURL ou Guzzle pour faire une requête POST à OpenAI
        $client = new GuzzleHttp\Client();
        $response = $client->request('POST', $this->apiUrl, [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json'
            ],
            'json' => [
                'model' => $model,
                'prompt' => $prompt,
                'max_tokens' => 150
            ]
        ]);

        return json_decode($response->getBody(), true);
    }


    public static function translateText($messages, $apiKey) {
       // error_log("oai api apiKey: " . $apiKey . "\n");

        $url = 'https://api.openai.com/v1/chat/completions';
    
        $postData = [
            'model' => 'gpt-3.5-turbo',
            'messages' => $messages,
        ];
    
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postData));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $apiKey,
        ]);
    
        $response = curl_exec($ch);
        curl_close($ch);
    
        if ($response === false) {
            error_log("Curl error: " . curl_error($ch) . "\n");
            return ['error' => 'Curl error: ' . curl_error($ch)];
        }
    
        $responseData = json_decode($response, true);
    
        if (isset($responseData['error'])) {
            error_log("OpenAI API error: " . $responseData['error']['message'] . "\n");
            return ['error' => 'OpenAI API error: ' . $responseData['error']['message']];
        } elseif (isset($responseData['choices'][0]['message']['content'])) {
            $message = $responseData['choices'][0]['message']['content'];
            $conso = $responseData['usage']['total_tokens'];
            //error_log("oai api message: " . $message . "\n");
            return ['message' => $message, 'conso' => $conso];
        } else {
            error_log("Unexpected response from OpenAI API\n");
            return ['error' => 'Unexpected response from OpenAI API'];
        }
    }
    



}
?>