<?php
include '../../../config.php';
include '../models/employeeModel.php';
include '../models/checkboxModel.php';
include '../models/attendanceModel.php';
include '../models/studentModel.php';
include '../models/batchModel.php';
include '../models/supportModel.php';
include '../models/testVideoModel.php';
include '../models/evaluationModel.php';


$employee = new EmployeeModel($conn);
$checkbox = new Checkbox($conn); 
$attendance = new Attendance($conn);
$student = new StudentModel($conn);
$batch = new BatchModel($conn);
$support = new SupportModel($conn);
$testVideo = new TestVideoModel($conn);
$evaluation = new EvaluationModel($conn);

session_start();
$student_id = isset($_SESSION['student_id']) ? $_SESSION['student_id'] : '';


if($_SERVER["REQUEST_METHOD"] == "POST"){
    if (isset($_POST['task'])) {
        $task = $_POST['task'];
    }

    if ($task === 'insertEmployee') {

        $name = $_POST['name'];
        $email = $_POST['email'];
        $role = $_POST['role'];
        $phone = $_POST['phone'];
        $address = $_POST['address'];
        $qualification = $_POST['qualification'];
        $uname = $_POST['uname'];
        $pass = $_POST['pass'];
        $DOB = $_POST['dob'];

        
        $response = $employee->insertEmployee($name, $email, $role, $phone, $address, $qualification, $uname, $pass, $DOB);
    
    }elseif ($task === 'checkbox') {
        $featureEnabled = ($_POST['featureEnabled'] === 'true') ? 1 : 0; // Convert to 1 or 0
        $id = $_POST['id'];
        $table = $_POST['table'];
        $idName = $_POST['idname'];

        $response = $checkbox->checkboc($table, $featureEnabled, $idName, $id);

    }elseif ($task === 'markAttendance') {

        $attendanceDate = $_POST['attendanceDate'];
        $personId = $_POST['personId'];
        $personIdName = $_POST['$personIdName'];

        $response = $attendance->markAttendance($attendanceDate, $personId, $personIdName);

    }elseif ($task === 'removeAttendance') {

        $attendanceId = $_POST['attendanceId'];
        
        $response = $attendance->removeAttendance($attendanceId);

    }elseif ($task === 'insertStudent') {

        $studentid = $_POST['studentid'];
        $name = $_POST['name'];
        $password = $_POST['password'];
        $phone = $_POST['phone'];
        $program = $_POST['program'];
        $batchid = $_POST['batchid'];
        $starton = $_POST['starton'];
         

        $response = $student->insertStudent($studentid, $name, $password, $phone, $program, $batchid, $starton);
    }elseif ($task === 'insertBatch') {

        $program = $_POST['program'];
        $class = $_POST['class'];
        $batchname = $_POST['batchname'];
        $timefrom = $_POST['timefrom'];
        $timeto = $_POST['timeto'];

        $response = $batch->insertBatch($program, $class, $batchname, $timefrom, $timeto);
    
    }elseif ($task === 'ticketCheck') {

        $ticketId = $_POST['ticketId'];
        $comment = $_POST['comment'];
        $status = $_POST['status'];
        $rating = $_POST['rating'];

        $response = $support->ticketCheck($ticketId, $comment, $status, $rating);
        
    }elseif ($task === 'testVideoAssigning') {
        if (isset($_POST['test']) && is_array($_POST['test'])) {
            $batchId = $_POST['batchId'];
            $table = $_POST['table'];
            $itemId = $_POST['itemId'];
            $item = $_POST['item'];
            try {
                $conn->autocommit(false); // Start a transaction
                
                foreach ($_POST['test'] as $testId => $isPresent) {
                    // Sanitize inputs and perform error checking as needed
                    $test1Id = intval($testId);
                    $isPresent = intval($isPresent);
    
                    $response = $testVideo->testVideoAssigning($batchId, $test1Id, $isPresent, $table, $itemId, $item);
                }
    
                 // Commit the transaction
            } catch (Exception $e) {
                $conn->rollback();// Rollback the transaction in case of an error
            } finally {
                $conn->autocommit(true);// Restore autocommit mode
            }
        }
        
    }elseif ($task === 'removeTheAssigning') {

        $batchId = $_POST['batchId'];
        $testId = $_POST['testId'];

        $response = $testVideo->removeTheAssigning($batchId, $testId, $student_id);
        
    }elseif ($task === 'update_score') {
        $type = $_POST['type'];
        $score_1 = $_POST['score-1'];
        $score_2 = $_POST['score-2'];
        $score_3 = $_POST['score-3'];
        $score_4 = $_POST['score-4'];
        $score_5 = $_POST['score-5'];
        $score_6 = $_POST['score-6'];
        $score_7 = $_POST['score-7'];
        $total = (int)$score_1 + (int)$score_2 + (int)$score_3 + (int)$score_4 + (int)$score_5 + (int)$score_6 + (int)$score_7;

        $answer_id = $_POST['answer_id'];

        $response = $evaluation->update_score($answer_id, $score_1, $score_2, $score_3, $score_4, $score_5, $score_6, $score_7, $type, $total);
        $response['total'] = $type;
        $response['score1'] = $score_1;
        $response['score2'] = $score_2;
        $response['score3'] = $score_3;
        $response['score4'] = $score_4;
        $response['score5'] = $score_5;
        $response['score6'] = $score_6;
        $response['score7'] = $score_7;
        $type = '';

    }elseif ($task === 'update_evaluated') {
        $test_id = $_POST['test_id'];
        $student_id = $_POST['student_id'];
        $response = $evaluation->update_evaluated($test_id, $student_id);
    }   

}

header('Content-Type: application/json');
echo json_encode($response);

$conn->close();
?>
