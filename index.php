<?php
include_once "php/config.php";
include_once "php/database.php";

switch($_GET['page']) {
   case '': 
      include 'main.php';
      break;
   case 'result': 
      include 'result.php';
      break;
   default: 
      include 'error.php'; 
      break;
}
?>
