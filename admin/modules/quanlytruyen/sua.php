<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <title>Sửa Thông Tin Truyện</title>
</head>
<body>

<div class="container">
    <?php

    // Kiểm tra nếu id_truyen được truyền vào từ URL
    if (isset($_GET["id_truyen"])) {
        $id_truyen = $_GET["id_truyen"];

        // Lấy thông tin truyện từ bảng tbl_truyen
        $sql_lay_truyen = "SELECT * FROM tbl_truyen WHERE id_truyen = $id_truyen";
        $result_lay_truyen = $mysqli->query($sql_lay_truyen);

        if ($result_lay_truyen->num_rows > 0) {
            $row_truyen = $result_lay_truyen->fetch_assoc();
            $img_path = $row_truyen['hinhanh'];

            // Kiểm tra xem đường dẫn hình ảnh có phải là URL không
            if (filter_var($img_path, FILTER_VALIDATE_URL)) {
                // Nếu là URL, không cần thay đổi gì
            } else {
                // Nếu không phải URL, thêm '../' vào đầu
                $img_path = '../' . $img_path;
            }   
            echo "<a href='index.php?action=quanlychuong&query=lietke&id_truyen={$row_truyen['id_truyen']}' class='btn btn-warning btn-sm'>Sửa nội dung truyện</a>";

            // Hiển thị form sửa thông tin truyện
            echo "<h2>Sửa thông tin truyện</h2>";

            echo "<form method='post' action='index.php?action=quanlytruyen&query=xuly' enctype='multipart/form-data'>";
            echo "<input type='hidden' name='id_truyen' value='$id_truyen'>";
            echo "<div class='form-group'>";
            echo "Tiêu đề: <input type='text' class='form-control' name='tieude' value='{$row_truyen['tieude']}'><br>";
            echo "</div>";
            echo "<div class='form-group'>";
            echo "Tóm tắt: <textarea class='form-control' name='tomtat'>{$row_truyen['tomtat']}</textarea><br>";
            echo "</div>";
            echo "<div class='form-group'>";
            echo "Tác giả: <input type='text' class='form-control' name='tacgia' value='{$row_truyen['tacgia']}'><br>";
            echo "</div>";
            echo "<div class='form-group'>";
            echo "Ảnh hiện tại: <img src='" . $img_path . "' alt='Ảnh truyện' width='50'><br>";
            echo "</div>";
            echo '                <div class="form-group">
            <label for="hinhanh">Hình ảnh:</label>
            <select class="form-control" id="imageType" onchange="toggleImageInput()" name="imageType">
                <option value="upload">Tải lên ảnh</option>
                <option value="url">Nhập URL ảnh</option>
            </select>
        </div>
        
        <div id="uploadImage" style="display: block;">
            <label for="hinhanhFile">Chọn tệp ảnh để tải lên:</label>
            <input type="file" class="form-control-file" name="hinhanhFile" id="hinhanhFile" accept="image/*">
        </div>
        
        <div id="urlImage" style="display: none;">
            <label for="urlHinhanh">Nhập URL ảnh:</label>
            <input type="text" class="form-control" id="urlHinhanh" name="urlHinhanh" placeholder="Nhập URL ảnh...">
        </div>';
            echo "<div class='form-group'>";
            echo "Trạng thái:
                <select class='form-control' name='trangthai'>
                    <option value='0' " . ($row_truyen['status_tt'] == 0 ? 'selected' : '') . ">Đang ra</option>
                    <option value='1' " . ($row_truyen['status_tt'] == 1 ? 'selected' : '') . ">Hoàn thành</option>
                </select><br>";
            echo "</div>";
            echo "<h3>Danh sách thể loại</h3>";

            // Lấy danh sách thể loại từ bảng tbl_theloai
            $sql_lay_theloai = "SELECT * FROM tbl_theloai";
            $result_lay_theloai = $mysqli->query($sql_lay_theloai);

            if ($result_lay_theloai->num_rows > 0) {
                while ($row_theloai = $result_lay_theloai->fetch_assoc()) {
                    $checked = '';
                    $id_theloai = $row_theloai['id_theloai'];

                    // Kiểm tra xem thể loại này có được chọn không
                    $sql_check_theloai = "SELECT * FROM tbl_truyen_theloai WHERE id_truyen = $id_truyen AND id_theloai = $id_theloai";
                    $result_check_theloai = $mysqli->query($sql_check_theloai);

                    if ($result_check_theloai->num_rows > 0) {
                        $checked = 'checked';
                    }

                    echo "<div class='form-check'>";
                    echo "<input type='checkbox' class='form-check-input' name='theloai[]' value='$id_theloai' $checked>";
                    echo "<label class='form-check-label'>{$row_theloai['tentheloai']}</label>";
                    echo "</div>";
                }
            }

            echo "<button type='submit' class='btn btn-primary' name='capnhat'>Cập nhật</button>";
            echo "</form>";
        } else {
            echo "<p class='alert alert-warning'>Không tìm thấy truyện.</p>";
        }
    } else {
        echo "<p class='alert alert-danger'>Không có truyện nào được chọn.</p>";
    }

    $mysqli->close();
    ?>
</div>
<script>
function toggleImageInput() {
    var imageType = document.getElementById("imageType").value;
    var uploadImage = document.getElementById("uploadImage");
    var urlImage = document.getElementById("urlImage");

    if (imageType === "upload") {
        uploadImage.style.display = "block";
        urlImage.style.display = "none";
    } else if (imageType === "url") {
        uploadImage.style.display = "none";
        urlImage.style.display = "block";
    }
}
</script>



</body>
</html>
