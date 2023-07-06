<?php
include_once "config.php";
include_once "database.php";

$url = $db->escapeString($_POST['url']);
$element = $db->escapeString($_POST['element']);

if (!empty($url) && !empty($element)) {
   // Check if the URL is valid
   if (filter_var($url, FILTER_VALIDATE_URL)) {
      // Extract the domain name from the URL
      $parsedUrl = parse_url($url);
      $domain = isset($parsedUrl['host']) ? $parsedUrl['host'] : '';
         // Check if the domain name is valid
         if (!empty($domain)) {
            // Check if the element is a valid HTML tag
            if (validateHTMLTag($element)) {
               // Check if the element is valid HTML
               $dom = fetchUrl($url);
               $errors = libxml_get_errors(); // Get HTML validation errors
               libxml_clear_errors(); // Clear errors

               if (empty($errors)) {
                  // Define the ALTER TABLE statement
                  $db->addForeignKeys();
                  $domainId = $db->insertData('domain', 'name', $domain, $dom);
                  $urlId = $db->insertData('url', 'name', $url, $dom);
                  $elementId = $db->insertData('element', 'name', $element, $dom);

                  // Fetch the URL and get the response time and element count
                  $startTime = microtime(true);
                  $response = fetchUrl($url);
                  $endTime = microtime(true);
                  $responseTime = round(($endTime - $startTime) * 1000);
                  $date = date('Y-m-d H:i:s');

                  if (!empty($response)) {
                  // Insert the request data
                     $insert = $db->insertRequestData($domainId, $urlId, $elementId, $date, $responseTime);
                     if ($insert) {
                        //Counting the number of Elements with URL
                        $elCount = $db->countElements($url, $element);
                        //How many URLs of that domain have been checked till now
                        $totalCheckedURLs = $db->getTotalCheckedURLs($domain);
                        //Average page fetch time from that domain during the last 24 hours
                        $averageFetchTime = $db->getAverageFetchTime($domain);
                        //Total count of this element from this domain
                        $totalElementCountFromDomain = $db->getTotalElementCountFromDomain($domain, $element);
                        //Total count of Element from ALL requests ever made
                        $totalElementCount = $db->getTotalElementCount($element);

                        $urlInfo = [
                           'url' => $url,
                           'date' => date('d/m/Y g:i'),
                           'responseTime' => $responseTime,
                           'count' => $elCount,
                           'element' => $element,
                           'domain' => $domain,
                           'totalCheckedURLs' => $totalCheckedURLs,
                           'totalElementCountFromDomain' => $totalElementCountFromDomain,
                           'totalElementCount' => $totalElementCount,
                           'averageFetchTime' => $averageFetchTime,
                        ];

                        // Encode the array as JSON
                        $jsonData = json_encode($urlInfo);
                        echo $jsonData;

                     } else {
                        $error = "Error inserting request data";
                        echo json_encode(['error' => $error]);
                     }
                  } else {
                     $error = "Error fetching URL content";
                     echo json_encode(['error' => $error]);
                  }
               } else {
                  $error = "Error parsing HTML";
                  echo json_encode(['error' => $error]);
               }
         } else {
               $error = "Invalid HTML tag format";
               echo json_encode(['error' => $error]);
         }
      } else {
         $error = "Invalid domain format";
         echo json_encode(['error' => $error]);
      }
   } else {
      $error = "Invalid URL format";
      echo json_encode(['error' => $error]);
   }
} else {
   $error = "All input fields are required";
   echo json_encode(['error' => $error]);
}
?>
