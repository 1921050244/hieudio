<?php
// Kết nối cơ sở dữ liệu
include('../admin/config/config.php');

session_start(); // Start the session

if (isset($_SESSION['id_user'])) {
    $id_truyen = $_POST['id_truyen'];
    $id_chuong = $_POST['id_chuong'];
    $id_user = $_SESSION['id_user'];
    $action = $_POST['action'];

    if ($action === 'remove') {
        // Prepare the DELETE statement
        $stmt = $mysqli->prepare("DELETE FROM tbl_danhdau WHERE id_truyen = ? AND id_chuong = ? AND id_user = ?");
        $stmt->bind_param("iii", $id_truyen, $id_chuong, $id_user);

        if ($stmt->execute()) {
            echo "success"; // Return success message
        } else {
            echo "error"; // Return error message
        }

        $stmt->close();
    }
}

$mysqli->close();
?>