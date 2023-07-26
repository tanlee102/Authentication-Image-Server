<?php
$servername = "127.0.0.1";
$username = "root";
$password = "1234";
$dbname = "ForumDB";
// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT * FROM MemberProfile";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
  // output data of each row
  while($row = $result->fetch_assoc()) {
    echo " - Name: " . $row["name"] . "<br>";
  }
} else {
  echo "0 results";
}
$conn->close();
?>