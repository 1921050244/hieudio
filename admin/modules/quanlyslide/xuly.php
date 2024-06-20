<?php
// Include file kết nối database
// include("connect.php");
require '../admin/modules/quanlytruyen/vendor/autoload.php';
use Cloudinary\Cloudinary;
use Cloudinary\Configuration\Configuration;
use Cloudinary\Api\Upload\UploadApi;

// Cấu hình Cloudinary
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

// Kiểm tra phương thức là POST và tồn tại các trường cần thiết
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_GET["id_anhtrangbia"]) && isset($_POST['suaanhtrangbia'])) {
    $id_anhtrangbia = $_GET["id_anhtrangbia"];

    // Lấy thông tin cần thiết từ form
    $thutu = isset($_POST['thutu']) ? $_POST['thutu'] : '';

    // Kiểm tra xem người dùng có upload ảnh mới không
    if (!empty($_FILES['hinhanh']['name'])) {
        // Tải lên Cloudinary
        $uploadResult = (new UploadApi())->upload($_FILES['hinhanh']['tmp_name']);
        $hinhanhUrl = $uploadResult['secure_url'];

        // Xóa ảnh cũ nếu có
        $sql_lay_anhtrangbia = "SELECT * FROM tbl_anhtrangbia WHERE id_anhtrangbia = $id_anhtrangbia";
        $result_lay_anhtrangbia = $mysqli->query($sql_lay_anhtrangbia);

        if ($result_lay_anhtrangbia->num_rows > 0) {
            $row_anhtrangbia = $result_lay_anhtrangbia->fetch_assoc();
            $anhCuUrl = $row_anhtrangbia["hinhanh"];
            // Không cần xóa ảnh cũ từ thư mục vì đã dùng Cloudinary
        }

        // Cập nhật URL ảnh mới vào cơ sở dữ liệu
        $hinhanh = $hinhanhUrl;
    } else {
        // Nếu không có ảnh mới, giữ nguyên ảnh cũ
        $sql_lay_anhtrangbia = "SELECT * FROM tbl_anhtrangbia WHERE id_anhtrangbia = $id_anhtrangbia";
        $result_lay_anhtrangbia = $mysqli->query($sql_lay_anhtrangbia);

        if ($result_lay_anhtrangbia->num_rows > 0) {
            $row_anhtrangbia = $result_lay_anhtrangbia->fetch_assoc();
            $hinhanh = $row_anhtrangbia["hinhanh"];
        }
    }

    $tinhtrang = $_POST['tinhtrang'];

    // Kiểm tra xem có sự thay đổi nào không trước khi cập nhật
    $sql_check_changes = "SELECT * FROM tbl_anhtrangbia WHERE id_anhtrangbia = $id_anhtrangbia";
    $result_check_changes = $mysqli->query($sql_check_changes);

    if ($result_check_changes->num_rows > 0) {
        $row_check_changes = $result_check_changes->fetch_assoc();
        if ($row_check_changes['hinhanh'] != $hinhanh || $row_check_changes['thutu'] != $thutu || $row_check_changes['tinhtrang'] != $tinhtrang) {
            // Có sự thay đổi, thực hiện cập nhật
            $sql_update_anhtrangbia = "UPDATE tbl_anhtrangbia SET hinhanh = '$hinhanh', thutu = '$thutu', tinhtrang = '$tinhtrang' WHERE id_anhtrangbia = $id_anhtrangbia";

            if ($mysqli->query($sql_update_anhtrangbia) === TRUE) {
                header("Location: index.php?action=quanlyslide&query=lietke");
            } else {
                echo "Lỗi: " . $mysqli->error;
            }
        } else {
            // Không có sự thay đổi, không cần cập nhật
            header("Location: index.php?action=quanlyslide&query=lietke");
        }
    } else {
        echo "Không tìm thấy ảnh trang bìa.";
    }

    // Đóng kết nối database
}


if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['themanhtrangbia'])) {
    // Kiểm tra xem trường 'thutu' có tồn tại trong dữ liệu POST không
    $thutu = isset($_POST['thutu']) ? $_POST['thutu'] : '';

    // Kiểm tra xem người dùng có upload ảnh không
    if (!empty($_FILES['hinhanh']['name'])) {
        // Tải lên Cloudinary
        $uploadResult = (new UploadApi())->upload($_FILES['hinhanh']['tmp_name']);
        $hinhanhUrl = $uploadResult['secure_url'];

        // Lấy thông tin cần thiết từ form
        $tinhtrang = $_POST['tinhtrang'];

        // Thực hiện truy vấn SQL để thêm dữ liệu vào cơ sở dữ liệu
        $sql_insert_anhtrangbia = "INSERT INTO tbl_anhtrangbia (hinhanh, thutu, tinhtrang) VALUES ('$hinhanhUrl', '$thutu', '$tinhtrang')";

        if ($mysqli->query($sql_insert_anhtrangbia) === TRUE) {
            header("Location: index.php?action=quanlyslide&query=lietke");
        } else {
            echo "Lỗi: " . $mysqli->error;
        }
    } else {
        echo "Vui lòng chọn ảnh.";
    }

    // Đóng kết nối database
}

if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET["id_anhtrangbia"])) {
    $id_anhtrangbia = $_GET["id_anhtrangbia"];

    // Lấy thông tin ảnh trang bìa từ cơ sở dữ liệu
    $sql_lay_anhtrangbia = "SELECT * FROM tbl_anhtrangbia WHERE id_anhtrangbia = $id_anhtrangbia";
    $result_lay_anhtrangbia = $mysqli->query($sql_lay_anhtrangbia);

    if ($result_lay_anhtrangbia->num_rows > 0) {
        $row_anhtrangbia = $result_lay_anhtrangbia->fetch_assoc();
        $hinhanh = $row_anhtrangbia["hinhanh"];

        // Xóa ảnh từ Cloudinary
        // Lấy public ID từ URL của ảnh
        $publicId = pathinfo($hinhanh, PATHINFO_FILENAME);
        (new UploadApi())->destroy($publicId);

        // Thực hiện truy vấn SQL để xóa dữ liệu khỏi cơ sở dữ liệu
        $sql_xoa_anhtrangbia = "DELETE FROM tbl_anhtrangbia WHERE id_anhtrangbia = $id_anhtrangbia";

        if ($mysqli->query($sql_xoa_anhtrangbia) === TRUE) {
            header("Location: index.php?action=quanlyslide&query=lietke");
        } else {
            echo "Lỗi: " . $mysqli->error;
        }
    } else {
        echo "Không tìm thấy ảnh trang bìa.";
    }

    // Đóng kết nối database
    mysqli_close($mysqli);
}
?>
