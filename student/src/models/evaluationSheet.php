<?php

class EvaluationSheet
{
    private $conn;

    public function __construct($conn)
    {
        $this->conn = $conn;
    }
    
//===============================================================================================================================================    

    public function evaluationSheet($test_id, $student_id){

        $sql = "SELECT a.*, q.solution, q.type, q.imageFile, q.mp4File AS Q_audio, q.key_words FROM answering AS a, question AS q WHERE a.test_id = $test_id AND a.student_id = '$student_id' AND q.question_id = a.question_id";
        
        $result = $this->conn->query($sql);
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }

        return $data;

    }

//===============================================================================================================================================

    

}
?>
