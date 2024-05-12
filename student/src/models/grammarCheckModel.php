<?php

class GrammarCheckModel
{
    private $conn;

    public function __construct()
    {

    }

//===============================================================================================================================================    

    public function grammarChecker($answer) {

        $url = 'https://trinka-grammar-checker.p.rapidapi.com/v2/para-check/en';
        $headers = [
            'content-type: application/json',
            'X-RapidAPI-Key: 24c2caf12cmsh2931583209525f1p1e80b1jsn63d4d600054f',
            'X-RapidAPI-Host: trinka-grammar-checker.p.rapidapi.com',
        ];

        $data = [
            'paragraph' => $answer,
            'language'  => 'UK',
        ];

        $ch = curl_init($url);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            echo 'Error: ' . curl_error($ch);
        }

        curl_close($ch);

        return $response;

    }

//===============================================================================================================================================

   


//===============================================================================================================================================




    

}
?>
