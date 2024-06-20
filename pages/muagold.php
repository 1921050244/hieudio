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
    $imagePath = '../acssee/modules/quanlyslide/uploads/bia1.webp';
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
        ?>
<div class="donate-section">
  <h3>Để ủng hộ tác giả & mở các chương bị khóa, hãy donate cho tác nhé:</h3>
  <h4>Quy đổi 1000đ = 1000 gold !</h4>

  <p>Quét mã QR dưới đây trên app ngân hàng của bạn để chuyển tiền:</p>
  
  <!-- Giả sử bạn sẽ đặt mã QR thực tế ở đây -->
 <div class="qr-code"><img src="./assets/image/nganhang.jpg" alt="QR Chuyển Khoản"></div>
  
  <div class="bank-details">
    <p>Ngân hàng SHB</p>
    <p>Số tài khoản: <span class="account-number">1010774859</span></p>
    <p>Chủ tài khoản: <span class="account-name">DANG THI MAI</span></p>
  </div>
  <p>Lưu ý : Sau khi chuyển khoản chụp bill và nhắn vào fb Phiêu Vũ để được duyệt nhanh.        <li><a href="https://www.facebook.com/profile.php?id=100090053724105" target="_blank"><i class="fab fa-facebook"></i> Facebook</a></li>
</p>

  <?php
// Kết nối với cơ sở dữ liệu

// Kiểm tra kết nối
if ($mysqli->connect_error) {
    die("Kết nối cơ sở dữ liệu lỗi: " . $mysqli->connect_error);
}

// Giả sử bạn đã xác thực người dùng và có $userId là id của người dùng đó
$userId = $_SESSION['id_user'];

// Lấy ma_tk từ bảng tbl_user
$query = "SELECT ma_tk FROM tbl_user WHERE id_user = ?";
if ($stmt = $mysqli->prepare($query)) {
    $stmt->bind_param("i", $userId); // 'i' là kiểu dữ liệu của $userId, ở đây là INTEGER
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $userInfo = $result->fetch_assoc();

    } else {
        echo "Không tìm thấy người dùng với ID cung cấp.";
    }
    
    $stmt->close();
}
?>

<div class="transfer-details">
    <label for="transferContent">Nội dung chuyển tiền:</label>
    <input type="text" value="<?php echo htmlspecialchars($userInfo['ma_tk']); ?>" id="transferContent" readonly>
    <button type="button" onclick="copyToClipboard()">Sao chép</button>
</div>
<script>
function copyToClipboard() {
  // Sử dụng navigator.clipboard.writeText() để sao chép nội dung mới
  const copyText = document.getElementById("transferContent");
  navigator.clipboard.writeText(copyText.value).then(() => {
    alert("Đã sao chép nội dung: " + copyText.value);
  }).catch((error) => {
    alert("Có lỗi xảy ra khi sao chép: ", error);
  });
}
</script>
</div>
<script>
function copyToClipboard() {
  var copyText = document.getElementById("transferContent");
  copyText.select();
  copyText.setSelectionRange(0, 99999); // For mobile devices
  document.execCommand("copy");
  alert("Đã sao chép nội dung: " + copyText.value);
}
</script>

<style>
.donate-section {
  padding: 20px;
  background-color: #f9f9f9;
  border-radius: 8px;
  text-align: center;
}

.qr-code img {
  max-width: 200px;
  margin-bottom: 10px;
}





.transfer-details input {
  margin-right: 10px;
  padding: 5px;
  border: 1px solid #ddd;
  border-radius: 4px;
}

.transfer-details button {
  padding: 10px 20px;
  background-color: #4caf50;
  color: white;
  border: none;
  border-radius: 4px;
  cursor: pointer;
}
.transfer-details {
    display: flex;
    justify-content: center;
    align-items: center;
    flex-wrap: wrap;
    gap: 10px;
}
</style>
        <?php
        // ... (Phần code sau liên quan đến việc đóng các thẻ và cấu trúc HTML)
    } else {
        echo 'Không tìm thấy thông tin người dùng.';
    }
}
?>
        </div>
    </div>
</main>
