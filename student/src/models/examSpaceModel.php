<?php

class ExamSpaceModel
{
    private $conn;

    public function __construct($conn)
    {
        $this->conn = $conn;
    }

//===============================================================================================================================================    

    public function questionDisplay($perPage, $offset, $test_id, $type, $student_id) {
            
        try{

            $sql = "SELECT type, question, solution, imageFile, question_id, mp4File, key_words FROM question WHERE question_id NOT IN (SELECT question_id FROM answering AS a WHERE a.student_id = '$student_id' AND a.test_id =$test_id) AND test_id = $test_id LIMIT $offset, 1";

            $sqlCount = "SELECT COUNT(*) AS Count FROM question WHERE test_id = $test_id";


            /*if($type === 'null'){
                $sql = "SELECT type, question, solution, imageFile, question_id, mp4File, key_words FROM question WHERE question_id NOT IN (SELECT question_id FROM answering AS a WHERE a.student_id = 2 AND a.test_id =$test_id) AND test_id = $test_id LIMIT $offset, $perPage";
                $sqlCount = "SELECT COUNT(*) AS Count FROM question WHERE test_id = $test_id";
                $sqlDiff = "SELECT (COUNT(*) + 1) AS start_page FROM question WHERE question_id IN (SELECT question_id FROM answering AS a WHERE a.student_id = 2 AND a.test_id =$test_id) AND test_id = $test_id";
            }else{
                $sql = "SELECT type, question, solution, imageFile, question_id, mp4File, key_words FROM question WHERE test_id = $test_id AND type = '$type' LIMIT $offset, $perPage";
                $sqlCount = "SELECT COUNT(*) AS Count FROM question WHERE test_id = $test_id AND type = '$type'";
            }*/
            
            $result = $this->conn->query($sql);
            $count = $this->conn->query($sqlCount)->fetch_assoc();

            $data = array();

            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $data[] = array(
                        'type' => $row['type'],
                        'question' => $row['question'],
                        'solution' => $row['solution'],
                        'imageFile' => $row['imageFile'],
                        'question_id' => $row['question_id'],
                        'key_words' => $row['key_words'],
                        'audio' => $row['mp4File'],
                    );
                }
            }

            
        $response = [
            'data' => $data,
            'totalItems' => $count,
            'offset' => $offset,
            'perpage' => $perPage
        ];

        }catch(Exception $e){
            // Handle database connection or query errors here
            $response['success'] = false;
            $response['message'] = "Error: " . $e->getMessage();
        }

        

        return  $response;
    }

//===============================================================================================================================================

   
    public function startPage($test_id, $student_id) {

        $sqlDiff = "SELECT (COUNT(*) + 1) AS start_page FROM question WHERE question_id IN (SELECT question_id FROM answering AS a WHERE a.student_id = '$student_id' AND a.test_id =$test_id) AND test_id = $test_id";

        $diff = $this->conn->query($sqlDiff)->fetch_assoc();

        return  $diff;

    }


//===============================================================================================================================================




    

}
?>
