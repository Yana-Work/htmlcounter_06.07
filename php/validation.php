<?php
include_once "config.php";
include_once "database.php";

function validateHTMLTag($tag) {
   // Remove leading and trailing whitespace
   $tag = trim($tag);
   // Define the regular expression pattern
   $pattern = '/^<([a-zA-Z]+)[^>]*>$/';
   // Perform the regular expression match
   return preg_match($pattern, $tag, $matches);
}

?>
