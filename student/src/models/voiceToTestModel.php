<?php

class VoiceToTestModel
{
    
//===============================================================================================================================================    

    public function voiceToTest($student_id){
        function upload_file($api_token, $path) {
            $url = 'https://api.assemblyai.com/v2/upload';
            $data = file_get_contents($path);
        
            $options = [
                'http' => [
                    'method' => 'POST',
                    'header' => "Content-type: application/octet-stream\r\nAuthorization: $api_token",
                    'content' => $data
                ]
            ];
        
            $context = stream_context_create($options);
            $response = file_get_contents($url, false, $context);
        
            if ($http_response_header[0] == 'HTTP/1.1 200 OK') {
                $json = json_decode($response, true);
                return $json['upload_url'];
            } else {
                echo "Error: " . $http_response_header[0] . " - $response";
                return null;
            }
        }
        
        // Function to create a transcript using AssemblyAI API
        function create_transcript($api_token, $audio_url) {
            $url = "https://api.assemblyai.com/v2/transcript";
        
            $headers = array(
                "authorization: " . $api_token,
                "content-type: application/json"
            );
        
            $data = array(
                "audio_url" => $audio_url
            );
        
            $curl = curl_init($url);
        
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
            curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        
            $response = json_decode(curl_exec($curl), true);
        
            curl_close($curl);
        
            $transcript_id = $response['id'];
        
            $polling_endpoint = "https://api.assemblyai.com/v2/transcript/" . $transcript_id;
        
            while (true) {
                $polling_response = curl_init($polling_endpoint);
        
                curl_setopt($polling_response, CURLOPT_HTTPHEADER, $headers);
                curl_setopt($polling_response, CURLOPT_RETURNTRANSFER, true);
        
                $transcription_result = json_decode(curl_exec($polling_response), true);
        
                if ($transcription_result['status'] === "completed") {
                    return $transcription_result;
                } else if ($transcription_result['status'] === "error") {
                    throw new Exception("Transcription failed: " . $transcription_result['error']);
                } else {
                    sleep(3);
                }
            }
        }


        try {
            // Your API token is already set in this variable
            $api_token = "7639cd33bc1f42adbef29b007f593780";
        
            $path = ".recording".$student_id.".wav";
            $upload_url = upload_file($api_token, $path);
        
            $transcript = create_transcript($api_token, $upload_url);
            $Question = $transcript['text']; 
        
            return $Question;
        
        } catch (Exception $e) {
            echo 'Error: ' . $e->getMessage();
        }
    }

//===============================================================================================================================================

   


//===============================================================================================================================================




    

}
?>
