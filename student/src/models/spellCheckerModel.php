<?php

class SpellCheckModel
{
    private $conn;

    public function __construct()
    {

    }

//===============================================================================================================================================    

    public function spellChecker($answer) {

        $endpoint = "https://api.edenai.run/v2/text/spell_check";
        $api_key = "Bearer eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJ1c2VyX2lkIjoiYTU1YjcyMDktMDJhNS00NjM1LWI0MWYtYzlmYTRmMTMyNzU5IiwidHlwZSI6ImFwaV90b2tlbiJ9.0_QaPNAIIOw_rmKT2WzN3ygqL5XS57-pYR38T4MvYec";

        $data = [
            'providers' => 'openai',
            'text' => $answer, // Corrected typo in the text
            'language' => 'en',
            'fallback_providers' => '',
        ];

        $options = [
            CURLOPT_URL => $endpoint,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'Authorization: ' . $api_key,
            ],
        ];

        $curl = curl_init();
        curl_setopt_array($curl, $options);
        $response = curl_exec($curl);
        $error = curl_error($curl);

        curl_close($curl);

        if ($error) {
            echo "Error: " . $error;
        } 

        return $response;

    }

//===============================================================================================================================================

   






    

}
?>
