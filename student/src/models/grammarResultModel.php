<?php

class GrammarResultModel
{
    private $conn;

    public function __construct($conn)
    {
        $this->conn = $conn;
    }
    
//===============================================================================================================================================    

    public function grammerResult($result_json){

        try{
            $data = json_decode($result_json, true);
            $sql_for_answer_id = $this->conn->query("SELECT MAX(answer_id) AS last_answer_id FROM answering");
            $result_array = $sql_for_answer_id->fetch_assoc();
            $new_id = $result_array['last_answer_id'];
            foreach ($data as $item) {
                if (!empty($item['result'])) {
                    foreach ($item['result'] as $result) {
                        $start_index = $result['start_index'];
                        $end_index = $result['end_index'];
                        $covered_text = str_replace("'", "`", json_encode($result['covered_text']));
                        $output = str_replace("'", "`", json_encode($result['output']));
                        $comment = str_replace("'", "`", json_encode($result['comment']));
                        $error_category = str_replace("'", "`", json_encode($result['error_category']));
                        
                        $sql = "INSERT INTO grammar_result (answer_id, start_index, end_index, covered_text, final_text, comment, error_category) 
                            VALUES ($new_id,$start_index,$end_index,'$covered_text','$output','$comment', '$error_category')";
                        
                        if ($this->conn->query($sql) === TRUE) {
                            $response['message'] = "grammar data updated successfully!";
                        } else {
                            $response['message'] = $sql;
                        } 
        
                    }
                }else{
                    $response['message'] = "no any grammer errors";
                }
                
            }
            

        }catch (Exception $e) {
            // Handle database connection or query errors here
            //$response['success'] = false;
            $response['message'] = "Error: " . $e->getMessage();
        }

        return $response;

    }

//===============================================================================================================================================

    public function fetchGrammarResult($answer_id){
    
        $sql = "SELECT * FROM grammar_result WHERE answer_id = $answer_id";
        $result = $this->conn->query($sql);
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }


        return $data;
    }


//===============================================================================================================================================

    


    

}
?>
