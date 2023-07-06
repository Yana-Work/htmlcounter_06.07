<?php include 'header.php';?>
<div class="wrapper">
   <section class="resultContainer">
      <h2>Result</h2>
      <div id="dataContainer">
      <?php 
         $url = $_GET['url'];
         $date = $_GET['date'];
         $responseTime = $_GET['responseTime'];
         $count = $_GET['count'];
         $element = htmlspecialchars($_GET['element']);
         $domain = $_GET['domain'];
         $totalCheckedURLs = $_GET['totalCheckedURLs'];
         $totalElementCountFromDomain = $_GET['totalElementCountFromDomain'];
         $totalElementCount = $_GET['totalElementCount'];
         $averageFetchTime = $_GET['averageFetchTime'];

         echo "
            <p>URL {$url} Fetched on {$date}, took {$responseTime}msec.</p>
            <p>Element {$element} appeared {$count} times on the page.</p>
            </br>
            <p>{$totalCheckedURLs} different URLs from {$domain} have been fetched.</p>
            <p>Average fetch time from {$domain} during the last 24 hours is {$averageFetchTime}ms.</p>    
            <p>There was a total of {$totalElementCountFromDomain} {$element} elements from {$domain}.</p>
            <p>Total of {$totalElementCount} {$element} elements counted in all requests ever made.</p>
         ";
         ?>
      </div>
   </section>
</div>
<?php include 'footer.php';?>
