<?php
// Kết nối CSDL và kiểm tra kết nối (sử dụng kết nối CSDL giống như bạn đã làm trong các trang khác)

// Kiểm tra xem có tham số truyền vào không
if ( isset($_GET['id_truyen']) && isset($_GET['id_chuong'])) {
    // Lấy giá trị tham số truyền vào
    $id_user = $_SESSION['id_user'];
    $id_truyen = $_GET['id_truyen'];
    $id_chuong = $_GET['id_chuong'];

    // Chuẩn bị câu lệnh SQL để xóa dữ liệu từ bảng
    $sql_delete = "DELETE FROM tbl_reading_status WHERE id_user = $id_user AND id_truyen = $id_truyen AND id_chuong = $id_chuong";

    // Thực hiện truy vấn xóa
    $result_delete = $mysqli->query($sql_delete);

    if ($result_delete) {
        header("Location: " . $_SERVER["HTTP_REFERER"]);
    } else {
        echo "Lỗi khi xóa dữ liệu: " . $mysqli->error;
    }
// ...
} else {
    echo "Thiếu tham số cần thiết: " . $mysqli->error;
}
// ...
if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id_truyen']) && isset($_GET['id_chuong'])) {
    $id_truyen = $_GET['id_truyen'];
    $id_chuong = $_GET['id_chuong']; // Lấy id_chuong từ biến $_GET
    $id_user = $_SESSION['id_user'];

    // Chuẩn bị câu truy vấn SQL để xóa bản ghi, bây giờ bao gồm cả điều kiện id_chuong
    $query = "DELETE FROM tbl_danhdau WHERE id_truyen = ? AND id_chuong = ? AND id_user = ?";

    // Chuẩn bị và thực thi truy vấn
    if ($stmt = $mysqli->prepare($query)) {
        // Bây giờ, bạn cần thêm một "i" khác trong bind_param để biểu thị kiểu dữ liệu integer của id_chuong
        $stmt->bind_param("iii", $id_truyen, $id_chuong, $id_user); // 3 "i" tương ứng với 3 giá trị integer
        $stmt->execute();

        if ($stmt->affected_rows > 0) {
            echo "Xóa thành công.";
        } else {
            echo "Không tìm thấy bản ghi hoặc không có quyền xóa.";
        }

        $stmt->close();
    } else {
        echo "Lỗi: Không thể chuẩn bị câu truy vấn.";
    }
} else {
    // Nếu thiếu tham số hoặc tham số không hợp lệ, hiển thị thông báo lỗi
    echo "Yêu cầu không hợp lệ.";
}
// Đóng kết nối CSDL
$mysqli->close();
?>
