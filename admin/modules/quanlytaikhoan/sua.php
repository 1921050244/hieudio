<?php
// Kết nối CSDL

// Kiểm tra xem có tham số id được truyền từ URL không
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id_user = $_GET['id'];

    // Truy vấn để lấy dữ liệu người dùng cần sửa
    $sql = "SELECT * FROM tbl_user WHERE id_user = $id_user";
    $result = mysqli_query($mysqli, $sql);

    if (mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);

        // Xử lý khi form được submit
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            // Lấy dữ liệu mới từ form
            $ten_moi = mysqli_real_escape_string($mysqli, $_POST['ten']);
            $email_moi = mysqli_real_escape_string($mysqli, $_POST['email']);
            $role_id_moi = mysqli_real_escape_string($mysqli, $_POST['role_id']);
            
            // Thực hiện cập nhật thông tin người dùng
            $update_sql = "UPDATE tbl_user SET tenuser='$ten_moi', email='$email_moi', role_id=$role_id_moi WHERE id_user = $id_user";
            $update_result = mysqli_query($mysqli, $update_sql);

            if ($update_result) {
                echo "Cập nhật người dùng thành công!";
            } else {
                echo "Lỗi khi cập nhật người dùng: " . mysqli_error($mysqli);
            }
        }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sửa người dùng</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-4">
        <h2>Sửa người dùng</h2>
        <form method="POST" action="index.php?action=quanlytaikhoan&query=xuly&id=<?php echo $_GET['id']?>">
            <div class="form-group">
                <label for="ten">Tên:</label>
                <input type="text" class="form-control" id="ten" name="ten" value="<?php echo $row['tenuser']; ?>" required>
            </div>
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" class="form-control" id="email" name="email" value="<?php echo $row['email']; ?>" required>
            </div>
            <div class="form-group">
                <label for="role_id">Role ID:</label>
                <select class="form-control" id="role_id" name="role_id" required>
                    <option value="2" <?php echo ($row['role_id'] == 2) ? 'selected' : ''; ?>>Tác giả</option>
                    <option value="3" <?php echo ($row['role_id'] == 3) ? 'selected' : ''; ?>>Chờ duyệt</option>
                    <option value="4" <?php echo ($row['role_id'] == 4) ? 'selected' : ''; ?>>Người dùng</option>

                </select>
            </div>
            <button type="submit" class="btn btn-primary">Lưu thay đổi</button>
        </form>
    </div>

    <!-- Bootstrap JS và jQuery -->
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>

<?php
    } else {
        echo "Người dùng không tồn tại.";
    }
} else {
    echo "Tham số id không hợp lệ.";
}

// Đóng kết nối CSDL
mysqli_close($mysqli);
?>
