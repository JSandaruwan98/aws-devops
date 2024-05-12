<?php
include '../models/employeeModel.php';
include '../../../config.php';
include '../models/attendanceModel.php';
include '../models/batchModel.php';
include '../models/studentModel.php';
include '../models/classModel.php';
include '../models/transactionModel.php';
include '../models/supportModel.php';
include '../models/testVideoModel.php';
include '../models/evaluationModel.php';

$support = new SupportModel($conn);
$class = new ClassModel($conn);
$employee = new EmployeeModel($conn);
$attendance = new Attendance($conn);
$batch = new BatchModel($conn);
$student = new StudentModel($conn);
$transaction = new TransactionModel($conn);
$testVideo = new TestVideoModel($conn);
$evaluation = new EvaluationModel($conn);

if (isset($_GET['data_type'])) {
    $data_type = $_GET['data_type'];

    if ($data_type === 'viewEmployee') {
        $data = $employee->viewEmployee();
    }elseif ($data_type === 'attendance') {
        $data = $attendance->viewAttendance();
    }elseif ($data_type === 'viewMarkAttendance') {
        $data = $attendance->viewMarkAttendance();
    }elseif ($data_type === 'getBatch') {
        $data = $batch->getBatch();
    }elseif ($data_type === 'getStudentId') {
        $data = $student->getStudentId();
    }elseif ($data_type === 'getStudentPassword') {
        $data = $student->getStudentPassword();
    }elseif ($data_type === 'viewStudent') {
        $data = $student->viewStudent();
    }elseif ($data_type === 'viewBatch') {
        $data = $batch->viewBatch();
    }elseif ($data_type === 'getClass') {
        $data = $class->getClass();
    }elseif ($data_type === 'transaction') {
        $data = $transaction->transaction();
    }elseif ($data_type === 'balance') {
        $data = $transaction->balance();
    }elseif ($data_type === 'support') {
        $data = $support->support();
    }elseif ($data_type === 'testVideoPresenting') {
        $data = $testVideo->testVideoPresenting();
    }elseif ($data_type === 'pendingEvaluation') {
        $data = $evaluation->pendingEvaluation();
    }elseif ($data_type === 'evaluationHistory') {
        $data = $evaluation->evaluationHistory();
    }elseif ($data_type === 'evaluationSheet') {
        $data = $evaluation->evaluationSheet();
    }elseif ($data_type === 'evaluationSheet1'){
        $test_id = $_GET['test_id'];
        $student_id = $_GET['student_id'];
        $data = $evaluation->evaluationSheet1($test_id, $student_id);
    }


    header('Content-Type: application/json');
    echo json_encode($data);

}else {
    echo "Specify data_type parameter (batch or class)";
}


$conn->close();