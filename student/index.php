<?php
session_start();

$url = isset($_GET['url']) ? $_GET['url'] : '/';
if($_SESSION['user'] != 'student'){
    header("Location: ../index.html");
}

$routes = [
    '/' => 'home.html',
    '/Exams' => 'pteTest.html',
    '/Evaluation' => 'evaluationSheet.html',
   
];
$student_id = isset($_SESSION['student_id']) ? $_SESSION['student_id'] : '';

echo "<script>";
echo "console.log('Student ID:', '$student_id');";
echo "</script>";

if (array_key_exists($url, $routes)) {
    include __DIR__ . '/' . $routes[$url];
} else {
    
    echo "404 Not Found";
}
