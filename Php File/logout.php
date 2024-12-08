<?php
try {
    require('conn.php');
    
    session_start();
    if (isset($_SESSION)) {
        session_destroy();
    }
    header("Location: signin.php");
    exit;
} catch(PDOException $e) {
    die($e->getMessage());
}
?>