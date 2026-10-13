<?php
/* ====================================================================
   StudentHub - Database Connection File (db_connect.php)
   Connects to MySQL database using standard MySQLi statements.
   ==================================================================== */

// Database Configuration (Default credentials for XAMPP / WAMP)
$servername = "localhost";   // MySQL server host
$username   = "root";        // Default MySQL user in XAMPP
$password   = "";            // Default password in XAMPP is empty
$dbname     = "studenthub";  // Name of our database

// Step 1: Create connection using mysqli_connect statement
$conn = mysqli_connect($servername, $username, $password, $dbname);

// Step 2: Check connection
if (!$conn) {
    // If connection fails, output the error message and stop execution
    die("Connection failed: " . mysqli_connect_error());
}

// Optional: Set UTF-8 character encoding
mysqli_set_charset($conn, "utf8mb4");

// Note: If this file is included in other PHP scripts (e.g. process_registration.php),
// you can now execute queries using $conn like this:
//
// $result = mysqli_query($conn, "SELECT * FROM students");
// while ($row = mysqli_fetch_assoc($result)) {
//     echo $row['name'];
// }
?>
