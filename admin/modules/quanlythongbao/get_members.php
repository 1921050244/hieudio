<?php
// Kết nối CSDL
include '../../config/config.php';
session_start();

try {
    // Kiểm tra xem người dùng đã đăng nhập chưa
    if (!isset($_SESSION['id_user']) || !isset($_SESSION['role_id'])) {
        throw new Exception("Bạn chưa đăng nhập hoặc không có quyền truy cập.");
    }

    // Lấy id_user hiện tại và role_id của người dùng từ session
    $id_user = $_SESSION['id_user'];
    $role_id = $_SESSION['role_id'];

    // Tạo một mảng chứa danh sách thành viên và thông tin số thông báo chưa đọc
    $members = array();

    // Truy vấn danh sách thành viên từ bảng tbl_user
    if ($role_id == 1) {
        // Nếu role_id là 1, lấy các user có role_id là 2
        $sql = "SELECT id_user, email FROM tbl_user WHERE role_id = 2";
        $stmt = $mysqli->prepare($sql);
        $stmt->execute();
        $result = $stmt->get_result();

        if (!$result) {
            throw new Exception("Truy vấn thất bại: " . mysqli_error($mysqli));
        }

        while ($row = $result->fetch_assoc()) {
            // Truy vấn số thông báo chưa đọc cho từng thành viên
            $sql_unread = "SELECT COUNT(*) AS total_unread FROM tbl_thongbao WHERE id_nguoigui = ? AND da_doc = 0";
            $stmt_unread = $mysqli->prepare($sql_unread);
            $stmt_unread->bind_param("i", $row['id_user']);
            $stmt_unread->execute();
            $result_unread = $stmt_unread->get_result()->fetch_assoc();

            if (!$result_unread) {
                throw new Exception("Truy vấn thất bại: " . mysqli_error($mysqli));
            }

            // Thêm thông tin số thông báo chưa đọc vào mỗi phần tử trong mảng thành viên
            $row['total_unread'] = $result_unread['total_unread'];
            $members[] = $row;
        }
        
        // Sắp xếp mảng $members theo total_unread giảm dần
        usort($members, function ($a, $b) {
            return $b['total_unread'] - $a['total_unread'];
        });
    } else if ($role_id == 2) {
        // Nếu role_id là 2, lấy các user có role_id là 1
        $sql = "SELECT id_user, email FROM tbl_user WHERE role_id = 1";
        $stmt = $mysqli->prepare($sql);
        $stmt->execute();
        $result = $stmt->get_result();

        if (!$result) {
            throw new Exception("Truy vấn thất bại: " . mysqli_error($mysqli));
        }

        while ($row = $result->fetch_assoc()) {
            // Truy vấn số thông báo chưa đọc cho từng thành viên
            $sql_unread = "SELECT COUNT(*) AS total_unread FROM tbl_thongbao WHERE (id_tacgia = $id_user and id_nguoigui = ?) AND da_doc = 0";       
                 $stmt_unread = $mysqli->prepare($sql_unread);
            $stmt_unread->bind_param("i", $row['id_user']);
            $stmt_unread->execute();
            $result_unread = $stmt_unread->get_result()->fetch_assoc();

            if (!$result_unread) {
                throw new Exception("Truy vấn thất bại: " . mysqli_error($mysqli));
            }

            // Thêm thông tin số thông báo chưa đọc vào mỗi phần tử trong mảng thành viên
            $row['total_unread'] = $result_unread['total_unread'];
            $members[] = $row;
        }
    } else {
        // Nếu không phải 1 hoặc 2, không có quyền truy cập
        throw new Exception("Không có quyền truy cập.");
    }

    // Trả về danh sách thành viên và tổng số thông báo chưa đọc dưới dạng JSON
    echo json_encode(array("members" => $members));

    // Đóng kết nối CSDL
    $stmt->close();
    $mysqli->close();
} catch (Exception $e) {
    // Xử lý ngoại lệ
    http_response_code(500);
    echo json_encode(array("error" => $e->getMessage()));
}
?>
