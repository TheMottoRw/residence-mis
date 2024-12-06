<?php
function session_controller() {
    if(!isset($_SESSION)){
        session_start();
    }
    if(!isset($_SESSION["email"])){
        header("location: login.php");
    }
}
?>