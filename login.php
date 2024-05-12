<?php
include 'config.php';
session_start();

$sql = "SELECT student_id, name, password FROM student";

$result = $conn->query($sql);
$data = array();
while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

function getNumericPart($studentId) {
    $numericPart = ltrim($studentId, 'STU');
    $numericValue = (int)$numericPart;
    return $numericValue;
}

function check_credentials($username, $password, $credentials) {
    foreach ($credentials as $credential) {
        $user = $credential['student_id']; 
        $pwd = $credential['password']; 
        if ($username == $user && $password === $pwd) {
            $_SESSION['name'] = $credential['name']; // Moved this line before return
            return $credential['student_id'];
        }
    }
    return false; 
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['username']) && isset($_POST['password'])) {
        if($_POST['username'] == 'Admin' && $_POST['password'] == 'Admin'){
            $_SESSION['user'] = 'admin';
            header("Location: admin/index.php");
            exit(); // Add exit after header redirect
        } else {
            $username = $_POST['username'];
            $password = $_POST['password'];
    
            $student_id = check_credentials($username, $password, $data);
            if ($student_id !== false) {
                $_SESSION['user'] = 'student';
                $_SESSION['student_id'] = $student_id;
                header("Location: student/index.php");
                exit(); // Add exit after header redirect
            } else {
                echo 'Invalid username or password.';
                header("Location: index.php#1");

            }
        }
    } else {
        echo 'Username and password are required.';
        exit;
    }
}
?>
