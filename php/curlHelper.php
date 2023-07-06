<?php
include_once "config.php";


function fetchUrl($url) {
   $ch = curl_init($url);
   curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
   curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1); // Используем HTTP 1.1
   $content = curl_exec($ch);
   if ($content === false) {
      // Error occurred while fetching the URL
      exit;
   }
   curl_close($ch);
   $dom = new DOMDocument; // Создаем объект DOMDocument
   libxml_use_internal_errors(true); // Включаем обработку ошибок libxml
   $dom->loadHTML($content); // Используем уже загруженный объект DOMDocument
   libxml_clear_errors(); // Очищаем ошибки libxml
   return $dom;
}

?>
