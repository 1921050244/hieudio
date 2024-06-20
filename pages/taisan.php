<?php

// Truy vấn CSDL để lấy đường dẫn ảnh từ bảng tbl_anhtrangbia
$sql = "SELECT hinhanh FROM tbl_anhtrangbia WHERE tinhtrang = 1 ORDER BY RAND() LIMIT 1";
$result = $mysqli->query($sql);

// Kiểm tra và lấy đường dẫn ảnh
if ($result && $result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $imagePath = './admin/modules/quanlyslide/uploads/' . $row['hinhanh'];
} else {
    // Nếu không có dữ liệu, sử dụng ảnh mặc định hoặc đặt giá trị khác tùy ý
    $imagePath = '../admin/modules/quanlyslide/uploads/bia1.webp';
}
?>

<!-- Thẻ img cho hiển thị ảnh -->
<img class="top-bg-op-box" src="<?php echo $imagePath; ?>" alt="Background Image">
<main>
  <div class="container_vc123">
  <?php
    include('thongtinsidebar.php')
    ?>
        <div class="right-column_vc123">

          
        <?php
// ... (Phần code trước liên quan tới cập nhật thông tin người dùng và ảnh đại diện)

// Lấy id_user từ session hoặc bất kỳ nguồn nào khác
if (isset($_SESSION['id_user'])) {
    // Lấy giá trị của id_user từ session
    $id_user = $_SESSION['id_user'];

    // Truy vấn SQL để lấy thông tin người dùng từ bảng tbl_user, bao gồm cả số gold
    $queryUserInfo = "SELECT id_user, tenuser, email, avatar, ngaysinh, sodienthoai, gold FROM tbl_user WHERE id_user = $id_user";

    // Thực hiện truy vấn và lấy dữ liệu
    $resultUserInfo = $mysqli->query($queryUserInfo);

    // Kiểm tra và hiển thị thông tin người dùng
    if ($resultUserInfo && $resultUserInfo->num_rows > 0) {
        $userInfo = $resultUserInfo->fetch_assoc();
        // ... (Phần hiển thị thông tin còn lại như avatar, tenuser, ngaysinh, sodienthoai, email)

        // Hiển thị số gold đang có
// Trong phần hiển thị số Gold và nút Mua Gold
echo '<div class="gold-info">';
echo '<p class="gold-amount">Gold đang có: <span>' . number_format($userInfo['gold'], 0, '', ',') . '</span></p>';
echo '<button type="button" class="buy-gold-btn" onClick="location.href=\'index.php?quanly=muagold\'">Mua Gold</button>';
echo '</div>';        
        // ... (Phần code sau liên quan đến việc đóng các thẻ và cấu trúc HTML)
    } else {
        echo 'Không tìm thấy thông tin người dùng.';
    }
}
?>
        </div>
    </div>
</main>
<style>
.gold-info {
    margin-top: 20px;
    background-color: #ffd700; /* Màu vàng gold */
    padding: 10px;
    border-radius: 8px;
    text-align: center;
    color: #000; /* Màu chữ */
}

.gold-amount {
    font-size: 24px;
    font-weight: bold;
}

.gold-amount span {
    color: #3c763d; /* Màu xanh lá */
}

.buy-gold-btn {
    display: inline-block;
    margin-top: 20px;
    padding: 10px 20px;
    font-size: 18px;
    color: white;
    background-color: #28a745; /* Màu xanh lá nhạt */
    border: none;
    border-radius: 5px;
    cursor: pointer;
    transition: background-color 0.2s ease-in-out;
}

.buy-gold-btn:hover {
    background-color: #218838; /* Màu xanh lá đậm */
}
</style>