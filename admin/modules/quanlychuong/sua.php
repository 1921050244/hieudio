<?php

// Kiểm tra và lấy thông tin chi tiết của chương từ URL
if (isset($_GET["id_chuong"])) {
    $id_chuong = $_GET["id_chuong"];
    $id_truyen = $_GET["id_truyen"];

    // Truy vấn SQL để lấy thông tin chương
    $sql_lay_chuong = "SELECT * FROM tbl_chuong WHERE id_chuong = $id_chuong";
    $result_lay_chuong = $mysqli->query($sql_lay_chuong);

    if ($result_lay_chuong->num_rows > 0) {
        $row_chuong = $result_lay_chuong->fetch_assoc();
        $tenchuong = $row_chuong["tenchuong"];
        $noidung = $row_chuong["noidung"];
        $sochuong = $row_chuong["sochuong"];
        $khoaChuongValue = $row_chuong["is_locked"];
        $chuong_gold = $row_chuong["chuong_gold"];
    } else {
        echo "Không tìm thấy chương.";
        exit();
    }

    // Truy vấn SQL để lấy thông tin truyện
    $sql_lay_truyen = "SELECT tieude FROM tbl_truyen WHERE id_truyen = $id_truyen";
    $result_lay_truyen = $mysqli->query($sql_lay_truyen);

    if ($result_lay_truyen->num_rows > 0) {
        $row_truyen = $result_lay_truyen->fetch_assoc();
        $tieude_truyen = $row_truyen["tieude"];
    } else {
        echo "Không tìm thấy truyện.";
        exit();
    }
}

// Xử lý khi người dùng gửi form "Lưu Sửa"
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["luuchuong"])) {
    $tenchuong_moi = $_POST["tenchuong"];
    $noidung_moi = $_POST["noidung"];
    $is_locked = isset($_POST["khoachuong"]) ? $_POST["khoachuong"] : '0'; // Mặc định là '0' nếu không tồn tại
    $chuong_gold = isset($_POST["gold"]) ? $_POST["gold"] : 0; // Mặc định là '0' nếu không tồn tại

    // Truy vấn SQL để cập nhật thông tin chương
    $sql_cap_nhat = "UPDATE tbl_chuong SET tenchuong = '$tenchuong_moi', noidung = '$noidung_moi', is_locked = '$is_locked', chuong_gold = '$chuong_gold' WHERE id_chuong = $id_chuong";

    if ($mysqli->query($sql_cap_nhat) === TRUE) {
        echo "<script>alert('Thành công !'); window.location = '" . $_SERVER["HTTP_REFERER"] . "';</script>";
    } else {
        echo "Có lỗi xảy ra: " . $mysqli->error;
    }
}

// Đóng kết nối database
$mysqli->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sửa Chương</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-4">
        <h2 class="mb-4">Sửa Nội Dung Chương</h2>
        <p>Tên Truyện: <?php echo $tieude_truyen; ?></p>
        <p>Chương: <?php echo $sochuong; ?></p>

        <form method="post" action="">
            <div class="form-group">
                <label for="tenchuong">Tiêu đề:</label>
                <input type="text" class="form-control" name="tenchuong" value="<?php echo $tenchuong; ?>" required>
            </div>

            <div class="form-group">
                <label for="noidung">Nội dung chương:</label>
                <textarea class="form-control" name="noidung" rows="5" required><?php echo $noidung; ?></textarea>
            </div>

            <div class="form-group">
                <label for="khoachuong">Trạng thái chương:</label>
                <select class="form-control" name="khoachuong" id="khoachuong" onchange="toggleGoldInput()">
                    <option value="0" <?php echo $khoaChuongValue == '0' ? 'selected' : ''; ?>>Không khóa</option>
                    <option value="1" <?php echo $khoaChuongValue == '1' ? 'selected' : ''; ?>>Đã khóa</option>
                </select>
            </div>
            <div class="form-group" id="goldInput" style="display: <?php echo $khoaChuongValue == '1' ? 'block' : 'none'; ?>;">
                <label for="gold">Số Gold:</label>
                <input type="number" class="form-control" name="gold" id="gold" min="0" value="<?php echo $chuong_gold; ?>" placeholder="Nhập số Gold">
            </div>
            <button type="submit" class="btn btn-primary" name="luuchuong">Lưu Sửa</button>
        </form>
    </div>
    <script>
    function toggleGoldInput() {
        var khoachuong = document.getElementById("khoachuong").value;
        var goldInput = document.getElementById("goldInput");
        if (khoachuong == "1") {
            goldInput.style.display = "block";
        } else {
            goldInput.style.display = "none";
        }
    }
    </script>
    <!-- jQuery và Bootstrap JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
</body>
</html>
