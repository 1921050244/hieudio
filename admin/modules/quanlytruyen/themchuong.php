<?php
// Kết nối CSDL và kiểm tra kết nối



// Lấy id_truyen từ tham số truyền vào hoặc từ SESSION (tuỳ vào cách bạn truyền id_truyen)
$id_truyen = isset($_GET['id_truyen']) ? $_GET['id_truyen'] : $_SESSION['id_truyen'];

// Truy vấn SQL để lấy tên truyện
$sql_ten_truyen = "SELECT tieude FROM tbl_truyen WHERE id_truyen = $id_truyen";
$result_ten_truyen = $mysqli->query($sql_ten_truyen);

// Kiểm tra và hiển thị tên truyện
if ($result_ten_truyen && $result_ten_truyen->num_rows > 0) {
    $row_ten_truyen = $result_ten_truyen->fetch_assoc();
    $ten_truyen = $row_ten_truyen['tieude'];
} else {
    $ten_truyen = "Không xác định"; // Nếu không có dữ liệu
}
$sql_max_chapter = "SELECT MAX(sochuong) as max_chapter FROM tbl_chuong WHERE id_truyen = $id_truyen";
$result_max_chapter = $mysqli->query($sql_max_chapter);

$max_chapter = 1; // Nếu không có chương nào, mặc định là 1
if ($result_max_chapter && $result_max_chapter->num_rows > 0) {
    $row_max_chapter = $result_max_chapter->fetch_assoc();
    $max_chapter = $row_max_chapter['max_chapter'] + 1;
}

$mysqli->close();
?>

<div class="container">
    <h2><?php echo $ten_truyen; ?></h2>
    <h3 class="mt-4 mb-4">Thêm Chương Mới</h3>
    <form method="post" action="index.php?action=quanlytruyen&query=xuly">
    <div class="form-group">
    <label>Chọn cách nhập nội dung:</label>
    <div class="form-check">
        <input class="form-check-input" type="radio" name="chooseOption" id="writeContent" value="write" checked>
        <label class="form-check-label" for="writeContent">
            Viết nội dung
        </label>
    </div>
    <div class="form-check">
        <input class="form-check-input" type="radio" name="chooseOption" id="useLink" value="link">
        <label class="form-check-label" for="useLink">
            Sử dụng link
        </label>
    </div>
</div>

<!-- Phần nhập nội dung -->
<div class="form-group" id="writeContentGroup">
    <label for="noidung">Nội dung chương:</label>
    <?php
    // Giả sử $noidung là dữ liệu bạn lấy từ CSDL
    $noidung = "";
    echo '<textarea rows="5" name="noidung" class="form-control">' . strip_tags($noidung) . '</textarea>';
    ?>
</div>

<!-- Nếu chọn sử dụng link, thì hiển thị thêm trường input -->
<div class="form-group" id="linkTruyenGroup" style="display:none;">
    <label for="linkTruyen">Link truyện:</label>
    <input type="text" class="form-control" name="linkTruyen" id="linkTruyen">
</div>
        <div class="form-group">
            <label for="tenchuong">Tên chương:</label>
            <input type="text" class="form-control" name="tenchuong">
        </div>
        <div class="form-group">
    <label for="sochuong">Chương số:</label>
    <input type="text" class="form-control" name="sochuong" value="<?php echo $max_chapter; ?>" readonly>
</div>

<div class="form-group">
    <label for="khoachuong">Khóa chương:</label>
    <select class="form-control" name="khoachuong" id="khoachuong" onchange="toggleGoldInput()">
        <option value="0">Mở</option>
        <option value="1">Khóa</option>
    </select>
</div>

<div class="form-group" id="goldInput" style="display: none;">
    <label for="gold">Số Gold:</label>
    <input type="number" class="form-control" name="gold" id="gold" min="0" placeholder="Nhập số Gold">
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

        <!-- Trường ẩn để truyền id_truyen -->
        <input type="hidden" name="id_truyen" value="<?php echo $id_truyen; ?>">

        <button type="submit" class="btn btn-primary" name="themchuong">Tạo Chương</button>
    </form>
</div>

<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<!-- Thư viện TinyMCE từ CDN -->
<script type="text/javascript">
    // Ẩn hoặc hiện các phần dựa vào lựa chọn
    $('input[type=radio][name=chooseOption]').change(function () {
        if (this.value == 'write') {
            $('#writeContentGroup').show();
            $('#linkTruyenGroup').hide();
        } else if (this.value == 'link') {
            $('#writeContentGroup').hide();
            $('#linkTruyenGroup').show();
        }
    });
</script>