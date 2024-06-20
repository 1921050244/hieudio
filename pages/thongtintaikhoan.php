<?php
require 'admin/modules/quanlytruyen/vendor/autoload.php';

use Cloudinary\Cloudinary;
use Cloudinary\Configuration\Configuration;
use Cloudinary\Api\Upload\UploadApi;

// Truy vấn CSDL để lấy đường dẫn ảnh từ bảng tbl_anhtrangbia
$sql = "SELECT hinhanh FROM tbl_anhtrangbia WHERE tinhtrang = 1 ORDER BY RAND() LIMIT 1";
$result = $mysqli->query($sql);

// Kiểm tra và lấy đường dẫn ảnh
if ($result && $result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $imagePath =  $row['hinhanh'];
} else {
    // Nếu không có dữ liệu, sử dụng ảnh mặc định hoặc đặt giá trị khác tùy ý
    $imagePath =  $row['hinhanh'];
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
            <ul class="nav_vc123">
                <li class="nav-item_vc123 nav-item-horizontal_vc123">Hồ sơ</li>
                <li class="nav-item_vc123 nav-item-horizontal_vc123">Bảo mật</li>
                <li class="nav-item_vc123 nav-item-horizontal_vc123">Cấu hình</li>
            </ul>
          
            <?php
// Giả sử bạn đã kết nối cơ sở dữ liệu và có biến $mysqli


if (isset($_POST['suathongtin'])) {
    $userId = $_SESSION['id_user']; // Giả sử bạn đã có biến $_SESSION['id_user'] chứa ID của người dùng
    $newDisplayName = $_POST['display-name'];
    $newBirthDate = $_POST['birth-date'];
    $newPhoneNumber = $_POST['phone-number'];
    $newEmail = $_POST['email'];

    // Cập nhật thông tin người dùng trong bảng tbl_user
    $sqlUpdateInfo = "UPDATE tbl_user SET tenuser = '$newDisplayName', ngaysinh = '$newBirthDate', sodienthoai = '$newPhoneNumber', email = '$newEmail' WHERE id_user = $userId";

    if ($mysqli->query($sqlUpdateInfo) === TRUE) {
        echo "Cập nhật thông tin thành công";
    } else {
        echo "Lỗi cập nhật thông tin: " . $mysqli->error;
    }

    // Kiểm tra xem người dùng đã chọn tệp mới để tải lên hay không
    if ($_FILES['avatar']['error'] == 0) {
        // Xử lý tải lên ảnh mới lên Cloudinary
        $config = Configuration::instance([
            'cloud' => [
                'cloud_name' => 'deam5w1nh',
                'api_key'    => '464877953624249',
                'api_secret' => 'u3I9FDuc0_r1xq19XbswsIOj79Q',
            ],
            'url' => [
                'secure' => true // Sử dụng HTTPS
            ]
        ]);
        
        $cloudinary = new Cloudinary($config);

        try {
            // Tải ảnh lên Cloudinary
            $uploadResult = (new UploadApi())->upload($_FILES['avatar']['tmp_name']);

            // Lấy URL của ảnh từ Cloudinary
            $newAvatar = $uploadResult['secure_url'];

            // Cập nhật đường dẫn ảnh mới vào cơ sở dữ liệu
            $sqlUpdateAvatar = "UPDATE tbl_user SET avatar = '$newAvatar' WHERE id_user = $userId";

            if ($mysqli->query($sqlUpdateAvatar) === TRUE) {
                echo "Cập nhật ảnh đại diện thành công";
            } else {
                echo "Lỗi cập nhật ảnh đại diện: " . $mysqli->error;
            }
        } catch (Exception $e) {
            echo "Lỗi khi tải lên ảnh lên Cloudinary: " . $e->getMessage();
        }
    }
}

// Lấy id_user từ session hoặc bất kỳ nguồn nào khác
if (isset($_SESSION['id_user'])) {
    // Lấy giá trị của id_user từ session
    $id_user = $_SESSION['id_user']; // Đây là một giả định, bạn cần thay đổi nó tùy thuộc vào cách bạn lấy id_user

// Truy vấn SQL để lấy thông tin người dùng từ bảng tbl_user
$queryUserInfo = "SELECT id_user, tenuser, email,avatar, ngaysinh, sodienthoai FROM tbl_user WHERE id_user = $id_user";

// Thực hiện truy vấn và lấy dữ liệu
$resultUserInfo = $mysqli->query($queryUserInfo);

// Kiểm tra và hiển thị thông tin người dùng
if ($resultUserInfo && $resultUserInfo->num_rows > 0) {
    $userInfo = $resultUserInfo->fetch_assoc();
    echo '<form id="update-form" action="" method="post" enctype="multipart/form-data">';
    echo '<div class="personal-info_vc123">';
    echo '<label for="avatar" class="label_vc123">Ảnh đại diện:</label>';
    function isExternalUrl($url) {
        // Sử dụng hàm parse_url để phân tích URL
        $parsedUrl = parse_url($url);
    
        // Nếu không có host hoặc host khác với host của trang web hiện tại, đây là URL external
        return isset($parsedUrl['host']) && $parsedUrl['host'] !== $_SERVER['HTTP_HOST'];
    }
    
    // Kiểm tra nếu avatar là link external
    if (isExternalUrl($userInfo['avatar'])) {
        // Loại bỏ phần đường dẫn ./assets/image/ nếu có
        $avatarUrl = str_replace('./assets/image/', '', $userInfo['avatar']);
    } else {
        // Nếu không phải là link external, thêm phần đường dẫn ./assets/image/
        $avatarUrl = './assets/image/' . $userInfo['avatar'];
    }
    
    // Hiển thị ảnh đại diện
    echo '<img src="' . $avatarUrl .'" alt="Ảnh đại diện" class="profile-picture_vc123">';    echo '<input type="file" id="avatar" name="avatar" accept="image/*">';

    echo '<label for="display-name" class="label_vc123">Tên:</label>';
    echo '<input type="text" id="display-name" name="display-name" class="input-field_vc123" value="' . $userInfo['tenuser'] . '">';
    
    echo ' <label for="birth-date" class="label_vc123">Năm sinh:</label>';
    echo '<input type="text" id="birth-date" name="birth-date" class="input-field_vc123" value="' . $userInfo['ngaysinh'] . '">';
    
    echo '<label for="phone-number" class="label_vc123">Số điện thoại:</label>';
    echo '<input type="text" id="phone-number" name="phone-number" class="input-field_vc123" value="' . $userInfo['sodienthoai'] . '">';
    
    echo '<label for="email" class="label_vc123">Email:</label>';
    echo '<input type="text" id="email" name="email" class="input-field_vc123" value="' . $userInfo['email'] . '">';
    
    echo '</div>';
    echo '<button type="submit" name="suathongtin" class="update-button_vc123">Cập nhật</button>';
    echo '</form>';
    
} else {
    echo 'Không tìm thấy thông tin người dùng.';
}
}
?>

        </div>
    </div>
</main>