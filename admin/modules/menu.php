<?php
// Giả sử $role_id lưu trong phiên là role_id của người dùng sau khi đăng nhập
$role_id = isset($_SESSION['role_id']) ? $_SESSION['role_id'] : null;

// Giả sử $selected_menu là biến lưu trữ mục được chọn (ví dụ: 'home', 'quanlytruyen', 'quanlytheloai', 'themuser', 'danhsachuser')
$selected_menu = isset($_GET['action']) ? $_GET['action'] : 'home';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trang của bạn</title>

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <a class="navbar-brand" href="index.php?action=trangchu&query=home">Trang chủ</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ml-auto">
                <?php if ($role_id == 3) { ?>
                    <!-- Nếu role_id là 3, chỉ hiển thị mục Trang chủ -->
                    <li class="nav-item <?php echo ($selected_menu == 'home') ? 'active' : ''; ?>">
                       
                    </li>
                <?php } else if ($role_id == 1 || $role_id == 2) { ?>
                    <li class="nav-item <?php echo ($selected_menu == 'dashboard') ? 'active' : ''; ?>">
                        <a class="nav-link" href="index.php?action=dashboard&query=dashboard">Dashboard</a>
                    </li>
                    <!-- Nếu role_id là 1 hoặc 2, hiển thị mục Quản lý truyện -->
                    <li class="nav-item <?php echo ($selected_menu == 'quanlytruyen') ? 'active' : ''; ?>">
                        <a class="nav-link" href="index.php?action=quanlytruyen&query=them">Thêm truyện mới</a>
                    </li>
                    <li class="nav-item <?php echo ($selected_menu == 'quanlytruyen' && isset($_GET['query']) && $_GET['query'] == 'lietke') ? 'active' : ''; ?>">
                        <a class="nav-link" href="index.php?action=quanlytruyen&query=lietke">Truyện của tôi</a>
                    </li>
                <?php } ?>

                <?php if ($role_id == 1) { ?>
                    <!-- Nếu role_id là 1, hiển thị mục Quản lý thể loại -->
                    <li class="nav-item <?php echo ($selected_menu == 'quanlytheloai') ? 'active' : ''; ?>">
                        <a class="nav-link" href="index.php?action=quanlytheloai&query=lietke">Quản lý thể loại</a>
                    </li>
                <?php } ?>

                <?php if ($role_id == 1) { ?>
                    <!-- Nếu role_id là 1, hiển thị mục Quản lý user -->
                    <li class="nav-item <?php echo ($selected_menu == 'danhsachuser') ? 'active' : ''; ?>">
                        <a class="nav-link" href="index.php?action=quanlytaikhoan&query=lietke">Quản lý user</a>
                    </li>
                <?php } ?>
                <?php if ($role_id == 1) { ?>
                    <!-- Nếu role_id là 1, hiển thị mục Quản lý user -->
                    <li class="nav-item <?php echo ($selected_menu == 'quanlyslde') ? 'active' : ''; ?>">
                        <a class="nav-link" href="index.php?action=quanlyslide&query=lietke">Quản lý Slide</a>
                    </li>
                <?php } ?>
                <?php if ($role_id == 1) { ?>
                    <!-- Nếu role_id là 1, hiển thị mục Quản lý user -->
                    <li class="nav-item <?php echo ($selected_menu == 'quanlygold') ? 'active' : ''; ?>">
                        <a class="nav-link" href="index.php?action=quanlygold&query=lietke">Quản lý Gold</a>
                    </li>
                <?php } ?>
                <?php if ($role_id == 1) { ?>
                    <!-- Nếu role_id là 1, hiển thị mục Quản lý user -->
                    <li class="nav-item <?php echo ($selected_menu == 'crack') ? 'active' : ''; ?>">
                        <a class="nav-link" href="index.php?action=crack&query=them">Crack</a>
                    </li>
                <?php } ?>
                <?php if ($role_id == 1) { ?>
                    <!-- Nếu role_id là 1, hiển thị mục Quản lý user -->
                    <li class="nav-item <?php echo ($selected_menu == 'chinhsach') ? 'active' : ''; ?>">
                        <a class="nav-link" href="index.php?action=quanlychinhsach&query=lietke">Chính sách</a>
                    </li>
                <?php } ?>
                <?php if ($role_id == 1) { ?>
                    <!-- Nếu role_id là 1, hiển thị mục Quản lý user -->
                    <li class="nav-item <?php echo ($selected_menu == 'thanhtoan') ? 'active' : ''; ?>">
                        <a class="nav-link" href="index.php?action=quanlythanhtoan&query=lietke">Thanh toán</a>
                    </li>
                <?php } ?>
                <?php if ($role_id == 1 || $role_id == 2) { ?>
                    <!-- Nếu role_id là 1, hiển thị mục Quản lý user -->
                    <?php
// Kết nối CSDL
include 'config/config.php';

// Lấy id_user hiện tại từ session
$id_user = $_SESSION['id_user'];

try {
    // Truy vấn số thông báo chưa đọc cho người dùng hiện tại
    $sql_unread = "SELECT COUNT(*) AS total_unread FROM tbl_thongbao WHERE id_tacgia = ? AND da_doc = 0";
    $stmt_unread = $mysqli->prepare($sql_unread);
    $stmt_unread->bind_param("i", $id_user);
    $stmt_unread->execute();
    $result_unread = $stmt_unread->get_result();

    if (!$result_unread) {
        throw new Exception("Query failed: " . mysqli_error($mysqli));
    }

    // Lấy số thông báo chưa đọc
    $row_unread = $result_unread->fetch_assoc();
    $total_unread = $row_unread['total_unread'];

    // Tạo chuỗi văn bản cho thông báo, bao gồm số tin nhắn chưa đọc nếu có
    $notificationText = "Thông báo";
    if ($total_unread > 0) {
        $notificationText .= " <span class='badge badge-danger'>$total_unread</span>";
    }
?>

<!-- HTML -->
<li class="nav-item <?php echo ($selected_menu == 'chinhsach') ? 'active' : ''; ?>">
    <a class="nav-link" href="index.php?action=quanlythongbao&query=lietke"><?php echo $notificationText; ?></a>
</li>

<?php
    // Đóng kết nối CSDL
    $stmt_unread->close();
    $mysqli->close();
} catch (Exception $e) {
    // Xử lý ngoại lệ
    http_response_code(500);
    echo json_encode(array("error" => $e->getMessage()));
}
?>


                <?php } ?>
            </ul>
        </div>
    </nav>

    <!-- Bootstrap JS và jQuery (Chú ý: jQuery là bắt buộc để Bootstrap hoạt động) -->
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
