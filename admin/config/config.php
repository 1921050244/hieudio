<?php
$mysqli = new mysqli("localhost","root","","webtruyen");
$mysqli->set_charset("utf8");

// Check connection
if ($mysqli -> connect_errno) {
  echo "Kết nối MYSQLi lỗi: " . $mysqli -> connect_error;
  exit();
}

?>