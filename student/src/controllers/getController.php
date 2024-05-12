<?php
include '../models/examSpaceModel.php';
include '../../../config.php';
include '../models/fetchAndDisplayModel.php';
include '../models/grammarResultModel.php';
include '../models/evaluationSheet.php';
include '../models/testModel.php';


$examSpace = new ExamSpaceModel($conn);
$fetchAndDisplay = new FetchAndDisplay($conn);
$grammar_result = new GrammarResultModel($conn);
$evaluation_sheet = new EvaluationSheet($conn);
$test = new TestModel($conn);

session_start();
$student_id = isset($_SESSION['student_id']) ? $_SESSION['student_id'] : '';

if (isset($_GET['data_type'])) {
    $data_type = $_GET['data_type'];
    if ($data_type === 'questionDisplay') {

        $page = isset($_GET['page']) ? $_GET['page'] : 1;
        $perPage = isset($_GET['per_page']) ? $_GET['per_page'] : 5;
        $offset = ($page - 1) * $perPage;
        $test_id = $_GET['test_id'];
        $type = $_GET['type'];
        //$data = $type;
        $data = $examSpace->questionDisplay($perPage, $offset, $test_id, $type, $student_id);

    }elseif ($data_type === 'startPage'){
        $test_id = $_GET['test_id'];
        $data = $examSpace->startPage($test_id, $student_id);
    }elseif ($data_type === 'fetchAndDisplay') {

        $question_id = $_GET['question_id'];

        if(isset($_GET['answer_id'])){
            $answer_id = $_GET['answer_id'];
            //$data = $fetchAndDisplay->fetchAnsweringEachData($question_id, $answer_id);
        }else{
            $data = $fetchAndDisplay->fetchAnsweringData($question_id);
        }

        

    }elseif ($data_type === 'grammar_result') {

        $answer_id = $_GET['answer_id'];
        $data = $grammar_result->fetchGrammarResult($answer_id);

    }elseif ($data_type === 'evaluationSheet'){

        $test_id = $_GET['paid_test_id'];
        $data = $evaluation_sheet->evaluationSheet($test_id, $student_id);

    }elseif ($data_type === 'completeTest'){
        $category = $_GET['category'];
        $data = $test->completeTest($category, $student_id);
    }elseif ($data_type === 'pendingTest'){

        $category = $_GET['category'];
        $data = $test->pendingTest($category, $student_id);

    }elseif ($data_type === 'checked_completed'){
        $test_id = $_GET['test_id'];
        $data = $test->checked_completed($test_id, $student_id);
    }elseif ($data_type === 'user_name'){
        $sql = "SELECT name FROM student WHERE student_id = "+ $student_id;
        $data = $sql;
    }


    header('Content-Type: application/json');
    echo json_encode($data);

}else {
    echo "Specify data_type parameter (batch or class)";
}


$conn->close();