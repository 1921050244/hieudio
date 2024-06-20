<?php
if (isset($_SESSION['id_user'])) {
    $ten_nguoidung = $_SESSION['tenuser'];

    echo "<nav class='navbar navbar-expand-lg navbar-light bg-light'>
            <span class='navbar-brand mb-0 h1'>Xin chào: $ten_nguoidung</span>
            <div class='ustify-content-start d-flex flex-row-reverse'>
                <a class='nav-link' href='?action=dangxuat'>Đăng xuất</a>
                <a class='nav-link' href='../index.php'>Thoát</a>
            </div>
          </nav>";

    if (isset($_GET['action']) && $_GET['action'] == 'dangxuat') {
        session_destroy();
        header("Location: index.php?action=dangnhap&query=dangnhap");
        exit();
    }
} else {
    echo "<nav class='navbar navbar-expand-lg navbar-light bg-light'>
            <div class='navbar-collapse d-flex flex-row-reverse'>
                <a class='nav-link' href='index.php?action=dangnhap&query=dangnhap'>Đăng nhập</a>
                <a class='nav-link' href='index.php?action=dangky&query=dangky'>Đăng ký</a>
                <a class='nav-link' href='../index.php'>Thoát</a>
            </div>
          </nav>";
}
?>
