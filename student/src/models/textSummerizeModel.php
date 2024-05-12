<?php

class TextSummerizeModel
{
   
    public function __construct()
    {
        
    }
    
//===============================================================================================================================================    

    public function summerize($paragraph){

        $encoded_string = urlencode($paragraph);

        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => "https://textanalysis-text-summarization.p.rapidapi.com/text-summarizer-text",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => "text=".$encoded_string.".&sentnum=1" , // Corrected concatenation operator
            CURLOPT_HTTPHEADER => [
                "X-RapidAPI-Host: textanalysis-text-summarization.p.rapidapi.com",
                "X-RapidAPI-Key: 5793a5110emshfc64aa12775dd98p12393djsn5abdf6452fc7",
                "Content-Type: application/x-www-form-urlencoded" // Corrected content type header
            ],
        ]);

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);
        if ($err) {
            echo "cURL Error #:" . $err;
        } else {
            $data = json_decode($response, true);
            $answer = $data['sentences'][0];
            $answer = str_replace("'", "\'", $answer);
            return $answer;
        }
        

    }

//===============================================================================================================================================

    

}
?>
