<?php

class SaveAudioModel
{
    private $conn;

    public function __construct($conn)
    {
        $this->conn = $conn;
    }

//===============================================================================================================================================    

    public function save_audio($audio,$student_id){


        $uploadDirectory1 = '.';
        $audioFile1 = $uploadDirectory1 . 'recording'.$student_id.'.wav';
           
        exec("ffmpeg -i $audio -vn -acodec pcm_s16le -ar 44100 -ac 2 $audioFile1");
        

        $response['message'] = "success";
        $response['audioFile2'] = $audioFile1;

        
        
        return $response;
    }

//===============================================================================================================================================

   


//===============================================================================================================================================




    

}
?>
