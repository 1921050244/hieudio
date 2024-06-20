<?php
// Kết nối cơ sở dữ liệu


// Lấy id_chinhsach từ URL
$id_chinhsach = isset($_GET['id_chinhsach']) ? intval($_GET['id_chinhsach']) : 0;

// Kiểm tra nếu id_chinhsach hợp lệ
if ($id_chinhsach <= 0) {
    echo "ID chính sách không hợp lệ.";
    exit();
}

// Xử lý dữ liệu khi form được submit
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $tenchinhsach = $_POST['tenchinhsach'];
    $noidung = $_POST['noidung'];
    $status = isset($_POST['status']) ? 1 : 0;

    $stmt = $mysqli->prepare("UPDATE tbl_chinhsach SET tenchinhsach = ?, noidung = ?, status = ? WHERE id_chinhsach = ?");
    $stmt->bind_param("ssii", $tenchinhsach, $noidung, $status, $id_chinhsach);
    $stmt->execute();
    $stmt->close();

    // Chuyển hướng về trang quản lý chính sách sau khi sửa thành công
    header("Location: index.php?action=quanlychinhsach&query=lietke");
    exit();
}

// Lấy thông tin chính sách hiện tại từ cơ sở dữ liệu
$stmt = $mysqli->prepare("SELECT tenchinhsach, noidung, status FROM tbl_chinhsach WHERE id_chinhsach = ?");
$stmt->bind_param("i", $id_chinhsach);
$stmt->execute();
$stmt->bind_result($tenchinhsach, $noidung, $status);
$stmt->fetch();
$stmt->close();

$mysqli->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sửa Chính sách</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-4">
    <h2>Sửa Chính sách</h2>
    <form action="" method="post">
        <div class="form-group">
            <label for="tenchinhsach">Tên Chính sách</label>
            <input type="text" class="form-control" id="tenchinhsach" name="tenchinhsach" value="<?php echo htmlspecialchars($tenchinhsach); ?>" required>
        </div>
        <div class="form-group">
            <label for="noidung">Nội dung</label>
            <textarea class="form-control" id="noidung" name="noidung" rows="5" required><?php echo htmlspecialchars($noidung); ?></textarea>
        </div>
        <script>
    // Lấy thẻ textarea
    var textarea = document.getElementById("noidung");

    // Thêm sự kiện "copy" vào textarea
    textarea.addEventListener("copy", function(event) {
        // Lấy văn bản đã được chọn
        var selectedText = window.getSelection().toString();

        // Nếu không có văn bản nào được chọn, không cần xử lý
        if (!selectedText) {
            return;
        }

        // Thêm các thẻ class vào văn bản đã được chọn
        var copiedText = addClassToSelectedText(selectedText);

        // Đặt nội dung đã được chỉnh sửa vào clipboard
        event.clipboardData.setData("text/plain", copiedText);

        // Ngăn chặn hành động copy mặc định
        event.preventDefault();
    });

    // Hàm thêm các thẻ class vào văn bản đã được chọn
    function addClassToSelectedText(text) {
        // Thêm các thẻ class vào văn bản đã được chọn (ở đây là ví dụ, bạn có thể thay đổi tuỳ theo yêu cầu của bạn)
        var modifiedText = "<div class='copied-text'>" + text + "</div>";

        return modifiedText;
    }
</script>
        <div class="form-group form-check">
            <input type="checkbox" class="form-check-input" id="status" name="status" <?php echo $status ? 'checked' : ''; ?>>
            <label class="form-check-label" for="status">Kích hoạt</label>
        </div>
        <button type="submit" class="btn btn-success">Lưu</button>
        <a href="index.php?action=quanlychinhsach&query=lietke" class="btn btn-secondary">Hủy</a>
    </form>
</div>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
</body>
</html>
