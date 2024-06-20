<?php
// Kết nối CSDL

try {
    // Kiểm tra xem id_chinhsach được truyền vào hay không
    if (!isset($_GET['id_chinhsach'])) {
        throw new Exception("ID chính sách không được cung cấp.");
    }

    // Lấy id_chinhsach từ tham số truy vấn
    $id_chinhsach = $_GET['id_chinhsach'];

    // Truy vấn chi tiết của chính sách từ bảng tbl_chinhsach
    $sql = "SELECT * FROM tbl_chinhsach WHERE id_chinhsach = ?";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param("i", $id_chinhsach);
    $stmt->execute();
    $result = $stmt->get_result();

    if (!$result) {
        throw new Exception("Query failed: " . mysqli_error($mysqli));
    }

    // Lấy thông tin chi tiết của chính sách
    $policy = $result->fetch_assoc();

    // Kiểm tra xem chính sách có tồn tại hay không
    if (!$policy) {
        throw new Exception("Không tìm thấy chính sách có ID: $id_chinhsach");
    }

    // Hiển thị thông tin chi tiết của chính sách
    echo "<div class='policy-details'>";
    echo "<h2 class='policy-title'>{$policy['tenchinhsach']}</h2>";

    echo '<div class="content-text">' . nl2br($policy['noidung']) . '</div>';

    echo "</div>";

    // Đóng kết nối CSDL

} catch (Exception $e) {
    // Xử lý ngoại lệ
    echo "<div class='error-message'>Error: " . $e->getMessage() . "</div>";
}
?>
