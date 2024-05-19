<?php 
namespace Models;
use \Models\Admin;

class DreamVisualization {
    private $openAiClient;
    private $dalleClient;

    public function __construct($openAiApiKey, $openAiApiUrl, $dalleApiKey, $dalleApiUrl) {
        $this->openAiClient = new OpenAIClient($openAiApiKey, $openAiApiUrl);
        $this->dalleClient = new DalleClient($dalleApiKey, $dalleApiUrl);
    }

    public function createDreamVisualization($userPhoto, $dreamDescription) {
        // Étape 1: Analyser la photo et obtenir une description (simulé ici)
        $userDescription = $this->openAiClient->generateText("Description de la personne sur la photo: " . $userPhoto);

        // Étape 2: Générer la description du rêve
        $dreamSceneDescription = $this->openAiClient->generateText($dreamDescription, "text-davinci-002");

        // Étape 3: Générer l'image du rêve
        $generatedImages = $this->dalleClient->generateImage($dreamSceneDescription['choices'][0]['text']);

        return $generatedImages;
    }
}


?>