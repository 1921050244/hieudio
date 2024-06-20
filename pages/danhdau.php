<?php
// Kết nối cơ sở dữ liệu
include('../admin/config/config.php');

session_start(); // Make sure to start the session to access $_SESSION variables

if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

if (isset($_SESSION['id_user'])) { // Check if the user is logged in
    $id_truyen = $_POST['id_truyen'];
    $id_chuong = $_POST['id_chuong'];
    $id_user = $_SESSION['id_user']; // Use the session variable for user ID
    $thoigian = date('Y-m-d H:i:s');

    // Prepare the INSERT statement
    $stmt = $mysqli->prepare("INSERT INTO tbl_danhdau (id_truyen, id_chuong, id_user, thoigian) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("iiis", $id_truyen, $id_chuong, $id_user, $thoigian);

    if ($stmt->execute()) {
        echo "success"; // Return success message
    } else {
        echo "error"; // Return generic error message
    }

    $stmt->close();
} else {
    echo "login"; // Return login required message
}
$mysqli->close();
?>