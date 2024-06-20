<?php

// Kiểm tra xem có tham số id được truyền từ URL không
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id_user = $_GET['id'];

    // Truy vấn để lấy dữ liệu người dùng cần sửa
    $sql = "SELECT * FROM tbl_user WHERE id_user = $id_user";
    $result = mysqli_query($mysqli, $sql);

    if (mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);

        // Kiểm tra xem có sự thay đổi về role_id hay không
        if (isset($_POST["role_id"]) && $_POST["role_id"] != $row["role_id"]) {
            // Nếu có sự thay đổi về role_id, thực hiện cập nhật thông tin người dùng
            $update_sql = "UPDATE tbl_user SET tenuser='{$_POST['ten']}', email='{$_POST['email']}', role_id={$_POST['role_id']} WHERE id_user = $id_user";
            $update_result = mysqli_query($mysqli, $update_sql);

            if ($update_result) {
                header("Location: index.php?action=quanlytaikhoan&query=lietke");
            } else {
                echo "Lỗi khi cập nhật người dùng: " . mysqli_error($mysqli);
            }
        } else {
            // Nếu không có sự thay đổi về role_id, thực hiện cập nhật thông tin người dùng khác (nếu có)
            if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["submit"])) {
                $ten_moi = mysqli_real_escape_string($mysqli, $_POST['ten']);
                $email_moi = mysqli_real_escape_string($mysqli, $_POST['email']);

                $update_sql = "UPDATE tbl_user SET tenuser='$ten_moi', email='$email_moi' WHERE id_user = $id_user";
                $update_result = mysqli_query($mysqli, $update_sql);

                if ($update_result) {
                    header("Location: index.php?action=quanlytaikhoan&query=lietke");
                } else {
                    echo "Lỗi khi cập nhật người dùng: " . mysqli_error($mysqli);
                }
            }

            // Kiểm tra và xử lý duyệt/ngừng duyệt
            if (isset($_POST["duyet"])) {
                $update_sql = "UPDATE tbl_user SET role_id = 2 WHERE id_user = $id_user";
                $update_result = mysqli_query($mysqli, $update_sql);

                if ($update_result) {
                    header("Location: " . $_SERVER["HTTP_REFERER"]);
                } else {
                    echo "Lỗi khi duyệt người dùng: " . mysqli_error($mysqli);
                }
            }

            // Kiểm tra và xử lý xóa
            if (isset($_POST["xoa"])) {
                // Đầu tiên, kiểm tra xem có bản ghi nào trong tbl_truyen liên quan đến id_user hay không
                $check_sql = "SELECT * FROM tbl_truyen WHERE id_admin = $id_user";
                $check_result = mysqli_query($mysqli, $check_sql);
                $row_count = mysqli_num_rows($check_result);
            
                if ($row_count > 0) {
                    // Nếu có, đưa ra thông báo lỗi hoặc xử lý theo logic của bạn
                    echo "Không thể xóa người dùng này vì vẫn còn truyện liên quan.";
                } else {
                    // Nếu không, tiến hành xóa người dùng
                    $delete_sql = "DELETE FROM tbl_user WHERE id_user = $id_user";
                    $delete_result = mysqli_query($mysqli, $delete_sql);
            
                    if ($delete_result) {
                        // Xóa dữ liệu từ các bảng liên quan
                        $delete_related_sql = "
                            DELETE FROM tbl_reading_status WHERE id_user = $id_user;
                            DELETE FROM tbl_danhdau WHERE id_user = $id_user;
                            DELETE FROM tbl_danhgia WHERE id_user = $id_user;
                            DELETE FROM tbl_binhluan WHERE id_user = $id_user;
                            DELETE FROM tbl_thongbao WHERE id_tacgia = $id_user;
                            DELETE FROM tbl_thongbao WHERE id_nguoigui = $id_user;


                        ";
            
                        // Thực thi các lệnh xóa liên quan
                        $delete_related_result = mysqli_multi_query($mysqli, $delete_related_sql);
            
                        // Kiểm tra kết quả
                        if ($delete_related_result) {
                            header("Location: " . $_SERVER["HTTP_REFERER"]); // Chuyển hướng trở lại trang trước
                            exit();
                        } else {
                            echo "Lỗi khi xóa dữ liệu liên quan: " . mysqli_error($mysqli);
                        }
                    } else {
                        echo "Lỗi khi xóa người dùng: " . mysqli_error($mysqli);
                    }
                }
            }
            
        }
    } else {
        echo "Người dùng không tồn tại.";
    }
} else {
    echo "Tham số id không hợp lệ.";
}

// Đóng kết nối CSDL
mysqli_close($mysqli);
?>
