<?php
// Kiểm tra xem người dùng đã nhấn nút "Đăng ký" chưa
if (isset($_POST['dk'])) {
    // Lấy id_user từ SESSION
    $id_user = $_SESSION['id_user'];

    // Kiểm tra xem role_id của người dùng có phải là 4 không
    $sql = "SELECT * FROM tbl_user WHERE id_user = $id_user AND role_id = 4";
    $result = mysqli_query($mysqli, $sql);

    if (mysqli_num_rows($result) > 0) {
        // Cập nhật role_id từ 4 về 3 trong bảng tbl_user
        $update_sql = "UPDATE tbl_user SET role_id = 3 WHERE id_user = $id_user";
        $update_result = mysqli_query($mysqli, $update_sql);

        if ($update_result) {
            // Cập nhật role_id trong SESSION
            $_SESSION['role_id'] = 3;
            
            // Chuyển hướng đến trang chính
            header("Location: index.php?action=trangchu&query=home");
            exit();
        } else {
            // Hiển thị thông báo lỗi nếu có vấn đề xảy ra khi cập nhật
            echo "Lỗi khi cập nhật role_id: " . mysqli_error($mysqli);
        }
    } else {
        // Nếu role_id không phải là 4, hiển thị thông báo hoặc xử lý theo logic của bạn
        echo "Không thể thực hiện cập nhật role_id.";
    }
} 
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trang Home</title>
    <style>
        #main-content {
            background: url(css/img/index.jpg) no-repeat center center/cover!important;
            height: 100vh; /* 100% của viewport height */
            display: flex;
            justify-content: center;
            align-items: center;
            color: #fff;
            text-align: center;
            width: 100%;
        }

        .greeting {
            font-size: 24px;
            margin-bottom: 20px;
        }

        .waiting-message {
            font-size: 18px;
        }
        /* CSS */
.btn-submit {
    background-color: #007bff;
    color: #fff;
    border: none;
    padding: 10px 20px;
    border-radius: 5px;
    cursor: pointer;
    transition: background-color 0.3s ease;
}

.btn-submit:hover {
    background-color: #0056b3;
}

/* Animation */
@keyframes blink {
    0% { opacity: 1; }
    50% { opacity: 0.5; }
    100% { opacity: 1; }
}

.btn-submit:focus {
    animation: blink 1s infinite;
}

    </style>
</head>
<body>

<div id="main-content">
    <?php
    // Kiểm tra xem có tồn tại SESSION không
    if (isset($_SESSION['role_id'])) {
        $role_id = $_SESSION['role_id'];

        if ($role_id == 1 || $role_id == 2) {
            // Hiển thị nội dung cho Admin và Tác giả
            echo "<p class='greeting'>Chào mừng bạn đến với trang quản lý truyện.</p>";
        } elseif ($role_id == 3) {
            // Hiển thị thông báo chờ xét duyệt cho Người dùng
            echo "<p class='waiting-message'>Xin vui lòng chờ xét duyệt.</p>";
        } elseif ($role_id == 4) {
            // Hiển thị nút "Đăng ký để đăng truyện" cho Người dùng có role_id là 4
            ?>
<form action="" method="post">
    <button type="submit" name="dk" class="btn-submit">Đăng ký để đăng truyện</button>
</form>

            <?php
        }
    } else {
        // Nếu không có SESSION, chuyển hướng đến trang đăng nhập
        header("Location: index.php?action=dangnhap&query=dangnhap");
        exit();
    }
    ?>
</div>

</body>
</html>
