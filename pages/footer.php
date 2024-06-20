<!-- Trong file HTML của bạn -->

    </div>
    </div>
<footer>
    <div class="footer-container">
        <div class="footer-section">
            <h3>Liên Hệ</h3>
            <p>Email:  phieuvu69@gmail.com</p>
      
        </div>
        <div class="footer-section">
            <h3>Thông Tin</h3>
<?php
// Kết nối CSDL

try {
    // Truy vấn danh sách các mục từ bảng tbl_chinhsach
    $sqls = "SELECT * FROM tbl_chinhsach WHERE status = 1"; // Chỉ lấy các mục có status = 1 (đã được kích hoạt)
    $results = $mysqli->query($sqls);

    if (!$results) {
        throw new Exception("Query failed: " . mysqli_error($mysqli));
    }

    // Tạo danh sách các mục
    $policyList = '<ul>';
    while ($row = $results->fetch_assoc()) {
        $policyList .= '<li><a href="index.php?quanly=chinhsach&id_chinhsach=' . $row['id_chinhsach'] . '">' . $row['tenchinhsach'] . '</a></li>';
    }
    $policyList .= '</ul>';

    // Hiển thị danh sách
    echo $policyList;

    // Đóng kết nối CSDL
    $mysqli->close();
} catch (Exception $e) {
    // Xử lý ngoại lệ
    echo "Error: " . $e->getMessage();
}
?>

        </div>
        <div class="footer-section">
    <h3>Theo Dõi Chúng Tôi</h3>
    <ul class="social-icons">

        <li><a href="https://www.facebook.com/profile.php?id=100090053724105" target="_blank"><i class="fab fa-facebook"></i> Facebook</a></li>
    </ul>
</div>

    </div>
    <div class="copyright">
        <p>&copy;  Trang Chủ. All rights reserved.</p>
        <p>Phiêu Vũ là trang website cá nhân, cho phép đăng tải truyện chữ do tác giả sáng tác và truyện dịch.</p><p> Những nội dung đăng tải lên website được sự đồng ý và cam kết của tác giả. Vui lòng không sao chép, đạo văn, chuyển thể dưới mọi hình thức để tránh khiếu nại về bản quyền. </p>
    </div>
</footer>
