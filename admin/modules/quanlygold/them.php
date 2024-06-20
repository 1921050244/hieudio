<?php
// process_add_gold.php
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['ma_tk'])) {
    $ma_tk = $_POST['ma_tk'];  // Lấy mã tài khoản từ form

    // Kết nối tới database
    
    // Kiểm tra kết nối
    if ($mysqli->connect_error) {
        die("Kết nối thất bại: " . $mysqli->connect_error);
    }
    
    // Tạo và thực hiện truy vấn
    $sql = "SELECT tenuser, email, sodienthoai , gold , ma_tk FROM tbl_user WHERE ma_tk = '$ma_tk'";
    $result = $mysqli->query($sql);
    
    // Kiểm tra kết quả truy vấn
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        
        // Hiển thị thông tin
        echo "<div class='container mt-3'><h4>Thông Tin Người Dùng</h4>";
        echo "<p>Tên: " . $row["tenuser"]. "</p>";
        echo "<p>Email: " . $row["email"]. "</p>";
        echo "<p>Số điện thoại: " . $row["sodienthoai"]. "</p>";
        echo "<p>Mã tài khoản: " . $row["ma_tk"]. "</p>";

        echo "<p>Gold đang có: " . number_format($row["gold"], 0, '', ',') . "</p>";
        
        // Form nhập số lượng gold
        echo '<form action="index.php?action=quanlygold&query=xuly" method="POST">
                <input type="hidden" name="ma_tk" value="'.$ma_tk.'">
                <div class="form-group">
                    <label for="gold">Số lượng gold thêm:</label>
                    <input type="number" class="form-control" id="gold" name="gold" required>
                </div>
                <button type="submit" class="btn btn-success">Thêm Gold</button>
              </form></div>';
    } else {
        echo "<p>Không tìm thấy người dùng!</p>";
    }
}
?>