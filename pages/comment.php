<?php
include('../admin/config/config.php');

// Kiểm tra xem có phải là yêu cầu POST và có tham số id_truyen không
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_truyen'])) {
    // Lấy dữ liệu từ POST
    $id_truyen = $_POST['id_truyen'];
    $offset = isset($_POST['offset']) ? (int)$_POST['offset'] : 0;
    $limit = isset($_POST['limit']) ? (int)$_POST['limit'] : 5;

    // Mảng để chứa bình luận
    $comments = [];

    // Câu truy vấn SQL để lấy bình luận với các tham số
    $queryComments = "SELECT 
        binhluan.id_binhluan,
        binhluan.noidung AS binhluan_noidung,
        binhluan.ngaybinhluan AS binhluan_ngay,
        user.tenuser AS user_tenuser,
        user.avatar AS user_avatar,
        binhluan.id_chuong
    FROM
        tbl_binhluan binhluan
    LEFT JOIN
        tbl_user user ON binhluan.id_user = user.id_user
    WHERE
        binhluan.id_truyen = ?
    ORDER BY
        binhluan.ngaybinhluan DESC
    LIMIT ?
    OFFSET ?;";

    // Chuẩn bị truy vấn
    if ($stmt = $mysqli->prepare($queryComments)) {
        // Gán giá trị vào các tham số của câu truy vấn
        $stmt->bind_param('iii', $id_truyen, $limit, $offset);

        // Thực thi truy vấn
        if ($stmt->execute()) {
            $result = $stmt->get_result();

            // Lấy dữ liệu từ kết quả truy vấn và đưa vào mảng comments
            while ($row = $result->fetch_assoc()) {
                $comments[] = $row;
            }
            $result->close();
        } else {
            // Gửi lỗi khi không thể thực thi truy vấn
            echo json_encode(['error' => 'Không thể thực thi truy vấn.']);
            exit();
        }
        $stmt->close();
    } else {
        // Gửi lỗi khi không thể chuẩn bị truy vấn
        echo json_encode(['error' => 'Không thể chuẩn bị truy vấn.']);
        exit();
    }

    // Trả về dữ liệu bình luận dưới dạng JSON
    header('Content-Type: application/json');
    echo json_encode($comments);
} else {
    // Gửi lỗi khi phương thức không phải POST hoặc không có id_truyen
    echo json_encode(['error' => 'Yêu cầu không hợp lệ.']);
    exit();
}
?>
