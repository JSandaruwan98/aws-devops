<?php

class FetchAndDisplay
{
    private $conn;

    public function __construct($conn)
    {
        $this->conn = $conn;
    }
    
//===============================================================================================================================================    

    public function fetchAnsweringData($question_id){

        $sql = "SELECT a.*, q.solution, q.name, s.name FROM answering AS a, student AS s, question AS q  WHERE a.question_id = $question_id AND s.student_id = a.student_id AND q.question_id = a.question_id";
        
        $result = $this->conn->query($sql);
        while ($row = $result->fetch_assoc()) {
            $answer_id = $row['answer_id']; // Get answer_id from current row
            $sql_grammar_result = "SELECT * FROM grammar_result WHERE answer_id = $answer_id";
            $grammar_result_result = $this->conn->query($sql_grammar_result); // Execute grammar result query
            $grammar_results = array();
            while ($grammar_row = $grammar_result_result->fetch_assoc()) {
                $grammar_results[] = $grammar_row;
            }
            $row['grammar_results'] = $grammar_results; // Add grammar results to the current row
            $data[] = $row;
        }

        return $data;

    }

//===============================================================================================================================================

    


//===============================================================================================================================================




    

}
?>
