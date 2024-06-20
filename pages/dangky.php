<?php

// Bắt đầu session nếu chưa có
if (session_status() == PHP_SESSION_NONE) {
}

// Kết nối đến cơ sở dữ liệu

// Kiểm tra kết nối
if ($mysqli->connect_errno) {
    echo "Kết nối đến MySQL bị lỗi: " . $mysqli->connect_error;
    exit();
}

// Kiểm tra nếu người dùng đã đăng nhập, chuyển hướng về trang chính
if (isset($_SESSION['id_user'])) {
    header("Location: index.php");
    exit();
}

// Kiểm tra nếu có dữ liệu đăng ký được submit từ form
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $tenuser = mysqli_real_escape_string($mysqli, $_POST["ten"]);
    $email = mysqli_real_escape_string($mysqli, $_POST["email"]);
    $matkhau = mysqli_real_escape_string($mysqli, $_POST["matkhau"]);
    $avatar = "avatar.jpg"; // Giá trị mặc định cho cột avatar

    // Kiểm tra xem email đã tồn tại chưa
    $sql_kiemtra_email = "SELECT * FROM tbl_user WHERE email='$email'";
    $result_kiemtra_email = mysqli_query($mysqli, $sql_kiemtra_email);

    // Tạo mã ngẫu nhiên gồm 2 số và 4 chữ cái thường
    function generateRandomCode() {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyz';
        $codeLength = 6; // Tổng cộng 6 ký tự (2 số + 4 chữ cái)
        $code = '';

        for ($i = 0; $i < $codeLength; $i++) {
            $index = rand(0, strlen($characters) - 1);
            $code .= $characters[$index];
        }

        return $code;
    }

    // Sử dụng hàm để tạo mã ngẫu nhiên
    $ma_tk = generateRandomCode();

    if ($result_kiemtra_email) {
        if (mysqli_num_rows($result_kiemtra_email) > 0) {
            $error_message = "Email đã tồn tại. Vui lòng sử dụng email khác.";
        } else {
            // Mã hóa mật khẩu trước khi lưu vào cơ sở dữ liệu
            $hashed_password = password_hash($matkhau, PASSWORD_BCRYPT);

            // Thêm thông tin đăng ký vào cơ sở dữ liệu
            $sql_dangky = "INSERT INTO tbl_user (tenuser, email, matkhau, avatar, ma_tk, gold, role_id) VALUES ('$tenuser', '$email', '$hashed_password', '$avatar', '$ma_tk', 0, 4)";
            $result_dangky = mysqli_query($mysqli, $sql_dangky);

            if ($result_dangky) {
                // Lưu thông tin người dùng vào session
                $_SESSION['id_user'] = mysqli_insert_id($mysqli);
                $_SESSION['tenuser'] = $tenuser;
                $_SESSION['avatar'] = $avatar;
                $_SESSION['role_id'] = 4; // Giả sử người dùng mới có role_id là 4

                // Chuyển hướng về trang chính
                header("Location: index.php");
                exit();
            } else {
                $error_message = "Lỗi khi đăng ký: " . mysqli_error($mysqli);
            }
        }
    } else {
        echo "Lỗi truy vấn CSDL: " . mysqli_error($mysqli);
    }
}

mysqli_close($mysqli);
?>


    <div class="container-mt-5">
        <div class="row-justify-content-center">
            <div class="col-md-6-123">
                <h2>Đăng Ký</h2>
                <?php
                if (isset($error_message)) {
                    echo '<div class="alert-alert-danger">' . $error_message . '</div>';
                }
                ?>
                <form method="post" action="">
                    <div class="form-group-123">
                        <label for="ten">Tên:</label>
                        <input type="text" class="form-control-123" id="ten" name="ten" required>
                    </div>
                    <div class="form-group-123">
                        <label for="email">Email:</label>
                        <input type="email" class="form-control-123" id="email" name="email" required>
                    </div>
                    <div class="form-group-123">
                        <label for="matkhau">Mật Khẩu:</label>
                        <input type="password" class="form-control-123" id="matkhau" name="matkhau" required>
                    </div>
                    <button type="submit" class="btn-btn-primary123">Đăng Ký</button>
                </form>
                <div class="mt-3">
                <p>Đã có tài khoản? <a href="index.php?quanly=dangnhap">Đăng nhập</a></p>
            </div>
            </div>
        </div>
    </div>
