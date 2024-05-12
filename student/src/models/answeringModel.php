<?php

class AnsweringModel
{
    private $conn;

    public function __construct($conn)
    {
        $this->conn = $conn;
    }
    
//===============================================================================================================================================    

    public function Insert($test_id, $voice, $audioFile, $question_id, $student_id, $content, $pronun, $grammar, $vocabulary, $form, $word_set_1, $word_set_2, $json, $spell, $dsc, $glr, $total){
        
        try{
            $word_set_1 = mysqli_real_escape_string($this->conn, $word_set_1);
            $word_set_2 = mysqli_real_escape_string($this->conn, $word_set_2);

            $sql = "INSERT INTO answering (question_id, student_id, test_id, mp4File, userAnswer, content, pronunciation, grammar, vocabulary, form, totalScore, additional_words, missed_words, json_data, spelling, dsc, glr) 
                VALUES ('$question_id', '$student_id', '$test_id', '$audioFile', '$voice', '$content', '$pronun', '$grammar', '$vocabulary', '$form', '$total', '$word_set_1', '$word_set_2', '$json', '$spell', '$dsc', '$glr')";

    

            if ($this->conn->query($sql) === TRUE) {
                $response['success'] = true;
                $response['message'] = "data updated successfully!";
            } else {
                $response['success'] = false;
                $response['message'] = $sql;
            }

        }catch (Exception $e) {
            $response['message'] =  "Caught exception: " . $e->getMessage();
        }

        return $response;

    }

//===============================================================================================================================================

   


//===============================================================================================================================================




    

}
?>
