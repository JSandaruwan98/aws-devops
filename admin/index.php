<?php
session_start();
$url = isset($_GET['url']) ? $_GET['url'] : '/';
if($_SESSION['user'] != 'admin'){
    header("Location: ../index.html");
}

$routes = [
    '/' => 'index.html',
    '/historyEvaluation' => 'history_evaluationSheet.html',
    '/pendngEvaluation' => 'pending_evaluationSheet.html',
   
];



if (array_key_exists($url, $routes)) {
    include __DIR__ . '/' . $routes[$url];
} else {
    
    echo "404 Not Found";
}
