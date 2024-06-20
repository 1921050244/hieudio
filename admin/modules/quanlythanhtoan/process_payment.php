<?php
session_start(); // Ensure session is started
include('../../config/config.php');
// Kiểm tra kết nối
if ($mysqli->connect_error) {
    echo json_encode(['success' => false, 'message' => 'Kết nối không thành công: ' . $mysqli->connect_error]);
    exit();
}

$id_gold_theothang = isset($_POST['id']) ? intval($_POST['id']) : 0;

if ($id_gold_theothang == 0) {
    echo json_encode(['success' => false, 'message' => 'ID không hợp lệ.']);
    exit();
}

// Cập nhật trạng thái thanh toán
$query = "UPDATE tbl_gold_theothang SET status = 1 WHERE id_gold_theothang = ?";
$stmt = $mysqli->prepare($query);
$stmt->bind_param("i", $id_gold_theothang);
$success = $stmt->execute();
$stmt->close();

// Đóng kết nối
$mysqli->close();

if ($success) {
    echo json_encode(['success' => true, 'message' => 'Thanh toán thành công.']);
} else {
    echo json_encode(['success' => false, 'message' => 'Đã xảy ra lỗi khi thanh toán.']);
}
?>
