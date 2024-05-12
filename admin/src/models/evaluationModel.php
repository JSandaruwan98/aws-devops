<?php

class EvaluationModel
{
    private $conn;

    public function __construct($conn)
    {
        $this->conn = $conn;
    }

//===============================================================================================================================================    

    public function pendingEvaluation() {
        $page = isset($_GET['page']) ? intval($_GET['page']) : 1;

        $itemsPerPage = 10; // Number of items to display per page
        $offset = ($page - 1) * $itemsPerPage;

        $sql = "SELECT ta.assigned_on, pt.student_id, s.name AS student_name, pt.attempted_on, t.name AS test_name, t.test_id, s.student_id 
                FROM testass as ta, paidtest AS pt, student AS s, test AS t WHERE ta.test_id = pt.test_id AND pt.attempted = 1 AND pt.evaluated != 1  AND ta.batch_id IN (SELECT batch_id FROM assignstudent AS astu 
                WHERE astu.student_id = pt.student_id) AND pt.student_id =s.student_id AND t.test_id = pt.test_id 
                LIMIT $offset, $itemsPerPage";
        $result = $this->conn->query($sql);
        $data = array();
        $i=1;
        while ($row = $result->fetch_assoc()) {
            $row['serial_number'] = ($page - 1) * $itemsPerPage + $i;
            $data[] = $row;
            $i++;
        }

        $totalItemsQuery = "SELECT COUNT(*) AS total FROM paidtest AS pt WHERE pt.attempted = 1 AND pt.evaluated != 1";
        $totalItemsResult = mysqli_query($this->conn, $totalItemsQuery);
        $totalItems = mysqli_fetch_assoc($totalItemsResult)['total'];


        $response = [
            'data' => $data,
            'totalItems' => $totalItems
        ];

        return $response;
    }
    
//===============================================================================================================================================

    public function evaluationHistory() {
        $page = isset($_GET['page']) ? intval($_GET['page']) : 1;

        $itemsPerPage = 10; // Number of items to display per page
        $offset = ($page - 1) * $itemsPerPage;

        $sql = "SELECT ta.assigned_on, pt.student_id, s.name AS student_name, pt.attempted_on, t.name AS test_name, t.test_id AS test_id, s.student_id AS stu_id, pt.evaluation_on 
                FROM testass as ta, paidtest AS pt, student AS s, test AS t WHERE ta.test_id = pt.test_id AND pt.evaluated = 1 AND ta.batch_id IN (SELECT batch_id FROM assignstudent AS astu 
                WHERE astu.student_id = pt.student_id) AND pt.student_id =s.student_id AND t.test_id = pt.test_id 
                LIMIT $offset, $itemsPerPage";
        $result = $this->conn->query($sql);
        $data = array();
        $i=1;
        while ($row = $result->fetch_assoc()) {
            $row['serial_number'] = ($page - 1) * $itemsPerPage + $i;
            $data[] = $row;
            $i++;
        }

        $totalItemsQuery = "SELECT COUNT(*) AS total FROM paidtest AS pt WHERE pt.evaluated = 1";
        $totalItemsResult = mysqli_query($this->conn, $totalItemsQuery);
        $totalItems = mysqli_fetch_assoc($totalItemsResult)['total'];


        $response = [
            'data' => $data,
            'totalItems' => $totalItems
        ];

        return $response;
    }
    
    //===============================================================================================================================================

    public function evaluationSheet() {
        $page = isset($_GET['page']) ? intval($_GET['page']) : 1;
        $studentId = $_GET['student_id'];

        $itemsPerPage = 10; // Number of items to display per page
        $offset = ($page - 1) * $itemsPerPage;

        $sql = "SELECT q.imageFile, q.type, a.content, a.pronunciation, a.student_id, a.oral_fluency, a.totalScore,q.mp4File as Q_audio, a.mp4File as S_audio, a.userAnswer, q.question, q.solution, a.additional_words, a.missed_words FROM answering AS a, question AS q WHERE q.question_id = a.question_id AND a.student_id = $studentId";
        $result = $this->conn->query($sql);
        $data = array();
        $word_set_1 = array();
        //$word_set_2 = array();

        $i=1;
        while ($row = $result->fetch_assoc()) {
            $row['serial_number'] = ($page - 1) * $itemsPerPage + $i;
            $data[] = $row;
            $i++;
        }

        $totalItemsQuery = "SELECT COUNT(*) as total FROM answering AS a, question AS q WHERE q.question_id = a.question_id AND a.student_id = $studentId";
        $totalItemsResult = mysqli_query($this->conn, $totalItemsQuery);
        $totalItems = mysqli_fetch_assoc($totalItemsResult)['total'];

        
        //$additional_words = array_values($word_set_1);
        //$missed_words = array_values(unserialize($word_set_2));

        $response = [
            'data' => $data,
            'totalItems' => $totalItems,
            //'additional_words1' => $additional_words,
            //'missed_words' => $missed_words
        ];

        return $response;
        $page=0;
    }

    public function evaluationSheet1($test_id, $student_id){

        $sql = "SELECT a.*, q.solution, q.type, q.imageFile, q.mp4File AS Q_audio, t.name, q.key_words FROM answering AS a, question AS q, test AS t WHERE a.test_id = $test_id AND a.student_id = '$student_id' AND q.question_id = a.question_id AND t.test_id = a.test_id";
        
        $result = $this->conn->query($sql);
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }

        return $data;

    }


    public function update_score($answer_id, $score_1, $score_2, $score_3, $score_4, $score_5, $score_6, $score_7, $type, $total){
        if($type == 'Summarize Written Text' || $type == 'Summarize Spoken Text'){
            $sql = "UPDATE answering SET content = $score_1, form = $score_2, grammar = $score_3, vocabulary = $score_4, totalScore = $total WHERE answer_id = $answer_id";
        }else if($type == 'Write Essay'){
            $sql = "UPDATE answering SET content = $score_1, form = $score_2, grammar = $score_3, vocabulary = $score_4, spelling = $score_5, dsc = $score_6, glr = $score_7, totalScore = $total WHERE answer_id = $answer_id";
        }else{
            $sql = "UPDATE answering SET content = $score_1, pronunciation = $score_2, oral_fluency = $score_3, totalScore = $total WHERE answer_id = $answer_id";
        }
        
        $result = $this->conn->query($sql);
        if ($result == TRUE) {
            $response['success'] = true;
            $response['message'] = "Score Adding Successfull";
        }else{
            $response['success'] = true;
            $response['message'] = "Score Adding Failed";
        }

        return $response;

    }

    public function update_evaluated($test_id, $student_id){

        $sql = "UPDATE paidtest SET evaluation_on = CURRENT_TIMESTAMP, evaluated = 1 WHERE test_id = $test_id AND student_id = '$student_id'";
        
        $result = $this->conn->query($sql);
        if ($result == TRUE) {
            $response['success'] = true;
            $response['message'] = "Evaluated Update Successfull";
        }else{
            $response['success'] = true;
            $response['message'] = "$sql";
        }

        return $response;

    }
    
}
?>
