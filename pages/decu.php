<?php
session_start(); // Khởi tạo session nếu cần
include('../admin/config/config.php');

// Kiểm tra phương thức yêu cầu và dữ liệu id_truyen
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_truyen'])) {
    $id_truyen = $_POST['id_truyen'];

    // Chuẩn bị câu lệnh SQL để tránh SQL Injection
    $stmt = $mysqli->prepare("UPDATE tbl_truyen SET decu = decu + 1 WHERE id_truyen = ?");
    $stmt->bind_param("i", $id_truyen);

    // Kiểm tra xem câu lệnh SQL thực thi thành công không
    if ($stmt->execute()) {
        echo 'success';
    } else {
        echo 'error';
    }

    $stmt->close();
} else {
    echo 'Invalid request';
}
?>