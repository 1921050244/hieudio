<?php
// update_gold.php
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['ma_tk']) && isset($_POST['gold'])) {
    $ma_tk = $_POST['ma_tk'];
    $gold = $_POST['gold'];

    // Kết nối tới database
    
    // Kiểm tra kết nối
    if ($mysqli->connect_error) {
        die("Kết nối thất bại: " . $mysqli->connect_error);
    }
    
    // Cập nhật gold cho người dùng
    $sql = "UPDATE tbl_user SET gold = gold + $gold WHERE ma_tk = '$ma_tk'";
    if ($mysqli->query($sql) === TRUE) {
        echo "<script>alert('Thêm gold thành công!'); window.location = 'index.php?action=quanlygold&query=lietke'</script>";

    } else {
        echo "<p>Lỗi: " . $mysqli->error . "</p>";
    }
    
}
?>