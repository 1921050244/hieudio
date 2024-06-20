<?php
// Kết nối CSDL
include '../../config/config.php';
session_start();

// Lấy userId từ yêu cầu AJAX

$id_admin = $_SESSION['id_user'];
$id_nguoinhan = $_GET['userId'];
// Truy vấn tin nhắn giữa người dùng hiện tại và người dùng có userId tương ứng
$sql = "SELECT id_nguoigui, id_tacgia, noidung FROM tbl_thongbao WHERE (id_nguoigui = ? AND id_tacgia = ?) OR (id_nguoigui = ? AND id_tacgia = ?)";
$stmt = $mysqli->prepare($sql);
$stmt->bind_param("iiii", $id_nguoinhan, $id_admin, $id_admin, $id_nguoinhan);
$stmt->execute();
$result = $stmt->get_result();

// Tạo một mảng chứa tin nhắn
$messages = array();
while ($row = $result->fetch_assoc()) {
    $messages[] = $row;
}

// Trả về dữ liệu tin nhắn dưới dạng JSON
header('Content-Type: application/json');
echo json_encode($messages);

// Đóng kết nối CSDL
$stmt->close();
$mysqli->close();
?>
