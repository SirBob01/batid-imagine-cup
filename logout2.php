<?php
  if(session_id() == '' || !isset($_SESSION)) {
    // session isn't started
    session_start();
  }
  setcookie(session_name(), '', 100);
  session_unset();
  session_destroy();
  $_SESSION = array();
  header("location: index.php");
?>
