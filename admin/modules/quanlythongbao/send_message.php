<?php
// Kết nối CSDL
include '../../config/config.php';
session_start();

// Kiểm tra người dùng đã đăng nhập chưa
if (!isset($_SESSION['id_user'])) {
    // Nếu chưa đăng nhập, trả về lỗi và kết thúc script
    http_response_code(401);
    echo json_encode(array("error" => "Bạn chưa đăng nhập."));
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['receiverId']) && isset($_POST['message'])) {
    // Lấy id của người gửi tin nhắn từ session
    $id_nguoigui = $_SESSION['id_user'];

    // Lấy id của người nhận tin nhắn từ form hoặc yêu cầu của bạn
    $id_nguoinhan = $_POST['receiverId'];

    // Lấy nội dung tin nhắn từ form hoặc yêu cầu của bạn
    $noidung = $_POST['message'];

    try {
        // Chuẩn bị câu truy vấn để chèn tin nhắn vào CSDL
        $sql_insert_message = "INSERT INTO tbl_thongbao (id_nguoigui, id_tacgia, tieude, noidung, da_doc) VALUES (?, ?, 'Thông báo', ?, 0)";
        $stmt_insert_message = $mysqli->prepare($sql_insert_message);
        $stmt_insert_message->bind_param("iis", $id_nguoigui, $id_nguoinhan, $noidung);
        $stmt_insert_message->execute();

        // Kiểm tra xem có tin nhắn được chèn thành công không
        if ($stmt_insert_message->affected_rows > 0) {
            // Cập nhật trạng thái da_doc của tin nhắn cũ của người nhận thành đã đọc (1)
            $sql_update_receiver = "UPDATE tbl_thongbao SET da_doc = 1 WHERE id_nguoigui = ? AND id_tacgia = ? AND da_doc = 0";
            $stmt_update_receiver = $mysqli->prepare($sql_update_receiver);
            $stmt_update_receiver->bind_param("ii", $id_nguoinhan, $id_nguoigui);
            $stmt_update_receiver->execute();

            // Trả về thông báo tin nhắn đã được gửi thành công
            echo json_encode(array("success" => "Tin nhắn đã được gửi thành công."));
        } else {
            // Trả về thông báo lỗi nếu không thể chèn tin nhắn vào CSDL
            http_response_code(500);
            echo json_encode(array("error" => "Không thể gửi tin nhắn. Có lỗi xảy ra khi thực hiện truy vấn."));
        }

        // Đóng câu truy vấn và kết nối CSDL
        $stmt_insert_message->close();
        $stmt_update_receiver->close();
        $mysqli->close();

    } catch (Exception $e) {
        // Xử lý ngoại lệ
        http_response_code(500);
        echo json_encode(array("error" => $e->getMessage()));
    }
} else {
    // Trả về thông báo lỗi nếu thiếu dữ liệu gửi từ client
    http_response_code(400); // Bad Request
    echo json_encode(array("error" => "Thiếu dữ liệu gửi từ client."));
}
?>
