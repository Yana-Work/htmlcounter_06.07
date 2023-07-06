<?php
include_once "config.php";
include_once "curlHelper.php";
include_once "validation.php";

class Database {
   private $conn;

   public function __construct($host, $username, $password, $database) {
      $this->conn = mysqli_connect($host, $username, $password, $database);

      if (!$this->conn) {
         throw new Exception("Database connection failed: " . mysqli_connect_error());
      }
   }

   public function escapeString($value) {
      return mysqli_real_escape_string($this->conn, $value);
   }

   public function addForeignKeys() {
      $sqlCheckKeys = "
         SELECT CONSTRAINT_NAME
         FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE
         WHERE REFERENCED_TABLE_NAME = 'domain' 
            AND REFERENCED_TABLE_SCHEMA = 'counter'
      ";
      $result = mysqli_query($this->conn, $sqlCheckKeys);
      $keysExist = mysqli_num_rows($result) > 0;
      if ($keysExist) {
         return;
      }
      $sqlAddForeignKeys = "
         ALTER TABLE request
         ADD FOREIGN KEY (domain_id) REFERENCES domain(id),
         ADD FOREIGN KEY (url_id) REFERENCES url(id),
         ADD FOREIGN KEY (element_id) REFERENCES element(id)
      ";
      mysqli_query($this->conn, $sqlAddForeignKeys);
   }

   public function insertData($table, $columnName, $value, $dom) {
      $sql = "INSERT INTO $table ($columnName) VALUES (?)";
      $stmt = mysqli_prepare($this->conn, $sql);
      mysqli_stmt_bind_param($stmt, "s", $value);
      mysqli_stmt_execute($stmt);
      $id = mysqli_insert_id($this->conn);
      mysqli_stmt_close($stmt);

      return $id;
   }

   public function insertRequestData($domainId, $urlId, $elementId, $date, $responseTime) {
      $sql = "INSERT INTO request (domain_id, url_id, element_id, time, duration) VALUES (?, ?, ?, ?, ?)";
      $stmt = mysqli_prepare($this->conn, $sql);
      mysqli_stmt_bind_param($stmt, "iiiss", $domainId, $urlId, $elementId, $date, $responseTime);
      $insert = mysqli_stmt_execute($stmt);
      mysqli_stmt_close($stmt);
   
      return $insert;
   }

   //Statistical methods 
   public function countElements($url, $element) {
      $sqlCountElements = "SELECT COUNT(*) AS element_count
                           FROM request
                           JOIN url ON url.id = request.url_id
                           JOIN element ON element.id = request.element_id
                           WHERE url.name = ? AND element.name = ?";
      $stmt = mysqli_prepare($this->conn, $sqlCountElements);
      mysqli_stmt_bind_param($stmt, "ss", $url, $element);
      mysqli_stmt_execute($stmt);
      $resultCountElements = mysqli_stmt_get_result($stmt);
      $rowCountElements = mysqli_fetch_assoc($resultCountElements);
      $elementCount = $rowCountElements['element_count'];
      mysqli_stmt_close($stmt);

      return $elementCount;
   }

   public function getTotalCheckedURLs($domain) {
      $sqlTotalCheckedURLs = "SELECT COUNT(DISTINCT url_id) AS total_checked_urls
                              FROM request
                              JOIN domain ON domain.id = request.domain_id
                              WHERE domain.name = ?";
      $stmt = mysqli_prepare($this->conn, $sqlTotalCheckedURLs);
      mysqli_stmt_bind_param($stmt, "s", $domain);
      mysqli_stmt_execute($stmt);
      $resultTotalCheckedURLs = mysqli_stmt_get_result($stmt);
      $rowTotalCheckedURLs = mysqli_fetch_assoc($resultTotalCheckedURLs);
      $totalCheckedURLs = $rowTotalCheckedURLs['total_checked_urls'];
      mysqli_stmt_close($stmt);

      return $totalCheckedURLs;
   }
   
   public function getAverageFetchTime($domain) {
      $currentDateTime = date('Y-m-d H:i');
      $intervalDateTime = date('Y-m-d H:i', strtotime('-24 hours'));
      $sqlAverageFetchTime = "SELECT AVG(duration) AS average_fetch_time
                              FROM request
                              JOIN domain ON domain.id = request.domain_id
                              WHERE domain.name = ? AND request.time BETWEEN ? AND ?";
      $stmt = mysqli_prepare($this->conn, $sqlAverageFetchTime);
      mysqli_stmt_bind_param($stmt,"sss", $domain, $intervalDateTime, $currentDateTime);
      mysqli_stmt_execute($stmt);
      $resultAverageFetchTime = mysqli_stmt_get_result($stmt);
      $rowAverageFetchTime = mysqli_fetch_assoc($resultAverageFetchTime);
      $averageFetchTime = round($rowAverageFetchTime['average_fetch_time']);
      mysqli_stmt_close($stmt);
   
      return $averageFetchTime;
   }
   
   public function getTotalElementCountFromDomain($domain, $element) {
      $sqlTotalElementCountFromDomain = "SELECT COUNT(*) AS total_element_count_domain
                                          FROM request
                                          JOIN domain ON domain.id = request.domain_id
                                          JOIN element ON element.id = request.element_id
                                          WHERE domain.name = ? AND element.name = ?";
      $stmt = mysqli_prepare($this->conn, $sqlTotalElementCountFromDomain);
      mysqli_stmt_bind_param($stmt,"ss", $domain, $element);
      mysqli_stmt_execute($stmt);
      $resultTotalElementCountFromDomain = mysqli_stmt_get_result($stmt);
      $rowTotalElementCountFromDomain = mysqli_fetch_assoc($resultTotalElementCountFromDomain);
      $totalElementCountFromDomain = round($rowTotalElementCountFromDomain['total_element_count_domain']);
      mysqli_stmt_close($stmt);
         
      return $totalElementCountFromDomain;
   }
   
   public function getTotalElementCount($element) {
      $sqlTotalElementCount = "SELECT COUNT(*) AS total_element_count
                              FROM request
                              JOIN element ON element.id = request.element_id
                              WHERE element.name = ?";
      $stmt = mysqli_prepare($this->conn, $sqlTotalElementCount);
      mysqli_stmt_bind_param($stmt,"s", $element);
      mysqli_stmt_execute($stmt);
      $resultTotalElementCount = mysqli_stmt_get_result($stmt);
      $rowTotalElementCount = mysqli_fetch_assoc($resultTotalElementCount);
      $totalElementCount = round($rowTotalElementCount['total_element_count']);
      mysqli_stmt_close($stmt);
   
      return $totalElementCount;
   }
}

?>
