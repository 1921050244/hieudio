<?php
// Kết nối cơ sở dữ liệu

// Xử lý các thao tác CRUD
$action = isset($_GET['trangthai']) ? $_GET['trangthai'] : '';
if ($action == 'delete') {
    $id_chinhsach = intval($_GET['id_chinhsach']);
    $mysqli->query("DELETE FROM tbl_chinhsach WHERE id_chinhsach = $id_chinhsach");

    header("Location: index.php?action=quanlychinhsach&query=lietke");
    exit();
}

// Lấy danh sách chính sách từ cơ sở dữ liệu
$result = $mysqli->query("SELECT * FROM tbl_chinhsach");

$mysqli->close();
?>