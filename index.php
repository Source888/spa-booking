<?php
require_once 'api-handler.php';
echo find_customer();
if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $name = $_POST['name'];
    $email = $_POST['email'];
    $message = $_POST['message'];
} else {
    $content = require_once 'form-page.php';
    echo $content;
}
?>