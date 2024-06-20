<?php
// Bắt đầu session nếu chưa có
// Bắt đầu session nếu chưa có
if (session_status() == PHP_SESSION_NONE) {
}

// Kết nối đến cơ sở dữ liệu

// Kiểm tra kết nối
if ($mysqli->connect_errno) {
    echo "Kết nối đến MySQL bị lỗi: " . $mysqli->connect_error;
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['id_token'])) {
        // Xác thực ID token với Google
        $id_token = $_POST['id_token'];
        $url = "https://oauth2.googleapis.com/tokeninfo?id_token=" . $id_token;
        $response = file_get_contents($url);
        if ($response === FALSE) {
            die('Error fetching URL');
        }
        $json = json_decode($response, true);

        if (isset($json['email'])) {
            $email = $json['email'];
            $name = $json['name'];
            $avatar = $json['picture'];
            $google_id = $json['sub'];
            $token = $id_token;

            // Kiểm tra xem người dùng đã tồn tại trong cơ sở dữ liệu của bạn chưa
            $sql_kiemtra = "SELECT * FROM tbl_user WHERE email='$email'";
            $result_kiemtra = mysqli_query($mysqli, $sql_kiemtra);

            if ($result_kiemtra && mysqli_num_rows($result_kiemtra) > 0) {
                // Người dùng đã tồn tại, cập nhật thông tin người dùng
                $row_user = mysqli_fetch_assoc($result_kiemtra);

                $sql_update = "UPDATE tbl_user SET google_id='$google_id', token='$token', avatar='$avatar' WHERE email='$email'";
                mysqli_query($mysqli, $sql_update);

                // Lưu thông tin người dùng vào session
                $_SESSION['id_user'] = $row_user['id_user'];
                $_SESSION['tenuser'] = $row_user['tenuser'];
                $_SESSION['avatar'] = $row_user['avatar'];
                $_SESSION['role_id'] = $row_user['role_id'];
            } else {
                // Người dùng chưa tồn tại, thêm người dùng vào cơ sở dữ liệu
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

                $ma_tk = generateRandomCode();
                $sql_them = "INSERT INTO tbl_user (tenuser, email, avatar, ma_tk, gold, role_id, google_id, token) VALUES ('$name', '$email', '$avatar', '$ma_tk', 0, 4, '$google_id', '$token')";

                if (mysqli_query($mysqli, $sql_them)) {
                    $id_user = mysqli_insert_id($mysqli);

                    // Lưu thông tin người dùng vào session
                    $_SESSION['id_user'] = $id_user;
                    $_SESSION['tenuser'] = $name;
                    $_SESSION['avatar'] = $avatar;
                    $_SESSION['role_id'] = 4; // Giả sử người dùng mới có role_id là 4
                } else {
                    echo "Lỗi: " . $sql_them . "<br>" . mysqli_error($mysqli);
                    exit();
                }
            }

            // Chuyển hướng về trang chính
            header("Location: index.php?action=trangchu&query=home");
            exit();
        } else {
            echo "ID token không hợp lệ";
        }
    } elseif (isset($_POST['dangnhap'])) {
        // Xử lý đăng nhập thông thường ở đây
        $email = mysqli_real_escape_string($mysqli, $_POST["email"]);
        $matkhau = mysqli_real_escape_string($mysqli, $_POST["matkhau"]);

        // Kiểm tra thông tin đăng nhập từ bảng tbl_user
        $sql_kiemtra = "SELECT * FROM tbl_user WHERE email='$email'";
        $result_kiemtra = mysqli_query($mysqli, $sql_kiemtra);

        if ($result_kiemtra) {
            $row_user = mysqli_fetch_assoc($result_kiemtra);

            // Kiểm tra mật khẩu
            if ($row_user && password_verify($matkhau, $row_user['matkhau'])) {
                // Lưu thông tin người dùng vào session
                $_SESSION['id_user'] = $row_user['id_user'];
                $_SESSION['tenuser'] = $row_user['tenuser'];
                $_SESSION['avatar'] = $row_user['avatar'];
                $_SESSION['role_id'] = $row_user['role_id'];

                // Chuyển hướng về trang chính
                header("Location: index.php?action=trangchu&query=home");
                exit();
            } else {
                $error_message = "Thông tin đăng nhập không đúng.";
            }
        } else {
            echo "Lỗi truy vấn CSDL: " . mysqli_error($mysqli);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng Nhập</title>
    <!-- Bootstrap CSS -->
    <style>


#main-content {
    background: url(css/img/index.jpg) no-repeat center center/cover!important;
    height: 100vh; /* 100% của viewport height */
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
.col-md-6 {
    background-color: #fff;
    color: #454545;
    padding: 20px;
    border-radius: 20px;
    margin-bottom: 100px;
}
</style>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
</head>

<body>
    <div id="main-content">
        <div class="container mt-5">
            <div class="row justify-content-center">
                <div class="col-md-6">
                    <h2>Đăng Nhập</h2>
                    <?php
                    if (isset($error_message)) {
                        echo '<div class="alert alert-danger">' . $error_message . '</div>';
                    }
                    ?>
                    <form method="post" action="">
                        <div class="form-group">
                            <label for="email">Email:</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>
                        <div class="form-group">
                            <label for="matkhau">Mật Khẩu:</label>
                            <input type="password" class="form-control" id="matkhau" name="matkhau" required>
                        </div>
                        <button type="submit" name="dangnhap" class="btn btn-primary">Đăng Nhập</button>
                    </form>
                    <div id="g_id_onload"
                     data-client_id="903289929360-urlenc1j6iqpb032mqmbr8okpm9orpqg.apps.googleusercontent.com"
                     data-callback="onSignIn">
                </div>
                <div class="g_id_signin"
                     data-type="standard"
                     data-size="large"
                     data-theme="dark"
                     data-text="sign_in_with"
                     data-shape="rectangular"
                     data-logo_alignment="left">
                </div>
                <form id="google-signin-form" method="post" action="">
                    <input type="hidden" id="id_token" name="id_token">
                </form>
                </div>
            </div>
        </div>
    </div>
    <script src="https://accounts.google.com/gsi/client" async defer></script>
    <script>
        function onSignIn(response) {
            // Lấy thông tin đăng nhập của người dùng
            var id_token = response.credential;

            // Gửi ID token về máy chủ của bạn qua form
            var form = document.getElementById('google-signin-form');
            document.getElementById('id_token').value = id_token;
            form.submit(); // Gửi form tự động
        }
    </script>

</body>


</html>
