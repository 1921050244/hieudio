
<?php
require 'vendor/autoload.php';
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

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Nếu là bước "Đăng truyện"
    if (isset($_POST["dangtruyen"])) {
        $tieude = mysqli_real_escape_string($mysqli, $_POST["tieude"]);
        $id_admin = $_SESSION['id_user']; // Lấy id_admin tạm thời, bạn có thể thay thế bằng phương thức xác thực người dùng
        $tomtat = $_POST["tomtat"];
        $tacgia = mysqli_real_escape_string($mysqli, $_POST["tacgia"]);
        $status_tt = isset($_POST["trangthai"]) ? $_POST["trangthai"] : 0; // Lấy giá trị trạng thái từ form

        // Xử lý upload hình ảnh
        $hinhanh = '';
        if ($_POST['imageType'] === 'url') {
            // Nếu người dùng chọn nhập URL ảnh
            if (isset($_POST['urlHinhanh'])) {
                $hinhanh = $_POST['urlHinhanh']; // Lấy URL ảnh từ form
            }
        } elseif ($_POST['imageType'] === 'upload') {
            // Nếu người dùng chọn tải ảnh lên từ máy tính
            if (isset($_FILES['hinhanhFile']) && $_FILES['hinhanhFile']['error'] == 0) {
                $image = $_FILES['hinhanhFile']['tmp_name'];
                try {
                    $uploadResult = (new UploadApi())->upload($image);
                    $hinhanh = $uploadResult['secure_url'];
                } catch (Exception $e) {
                    echo "Tải lên thất bại: " . $e->getMessage();
                    exit();
                }
            }
        }

        // Thêm truyện mới vào bảng tbl_truyen
        $sql_truyen = "INSERT INTO tbl_truyen (tieude, hinhanh, tomtat, tacgia, ngaydang, status_tt, id_admin,truyen_status) 
                       VALUES ('$tieude', '$hinhanh', '$tomtat', '$tacgia', NOW(), $status_tt, $id_admin,0)";

        $result_truyen = mysqli_query($mysqli, $sql_truyen);

        if ($result_truyen) {
            $id_truyen_moi = mysqli_insert_id($mysqli);

            // Lấy danh sách thể loại được chọn từ form
            if (isset($_POST['theloai']) && is_array($_POST['theloai'])) {
                foreach ($_POST['theloai'] as $id_theloai) {
                    // Thêm vào bảng liên kết nhiều nhiều
                    $sql_lienkethai = "INSERT INTO tbl_truyen_theloai (id_truyen, id_theloai) 
                                       VALUES ($id_truyen_moi, $id_theloai)";
                    mysqli_query($mysqli, $sql_lienkethai);
                }
            }

            // Chuyển hướng đến trang themchuong.php với thông tin truyền đi
            header("Location: index.php?action=quanlytruyen&query=themchuong&id_truyen=$id_truyen_moi");
            exit();
        } else {
            echo "Lỗi khi thêm truyện: " . mysqli_error($mysqli);
        }
    }



    // Nếu là bước "Thêm chương"
    if (isset($_POST["themchuong"])) {
        $id_truyen = mysqli_real_escape_string($mysqli, $_POST["id_truyen"]);
        $tenchuong = mysqli_real_escape_string($mysqli, $_POST["tenchuong"]);
        $sochuong = mysqli_real_escape_string($mysqli, $_POST["sochuong"]);
        $is_locked = isset($_POST["khoachuong"]) ? mysqli_real_escape_string($mysqli, $_POST["khoachuong"]) : 0;
        $gold = 0; // Giá trị mặc định cho Gold
    
        // Kiểm tra nếu chương bị khóa và có giá trị Gold
        if ($is_locked == "1" && isset($_POST["gold"])) {
            $gold = intval($_POST["gold"]);
        }
    
        // Lựa chọn giữa link truyện và nội dung
        $chooseOption = isset($_POST["chooseOption"]) ? $_POST["chooseOption"] : "";
    
        if ($chooseOption == "link") {
            $linkTruyen = mysqli_real_escape_string($mysqli, $_POST["linkTruyen"]);
    
            // Sử dụng cURL để crawl nội dung từ link truyện
            $curl = curl_init($linkTruyen);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            $html = curl_exec($curl);
            curl_close($curl);
    
            // Sử dụng DOMDocument để parse HTML
            $dom = new DOMDocument();
            @$dom->loadHTML($html);
    
            // Lấy nội dung từ thẻ div có id là "chapter-c" bao gồm cả thẻ HTML
            $chapterDiv = $dom->getElementById("chapter-c");
    
            // Kiểm tra xem có thẻ div hay không và hiển thị nội dung
            if ($chapterDiv) {
                // Sử dụng innerHTML để bao gồm cả thẻ HTML
                $noidung = mysqli_real_escape_string($mysqli, $dom->saveHTML($chapterDiv));
    
                date_default_timezone_set('Asia/Ho_Chi_Minh');
                $thoigian = date('Y-m-d H:i:s');
    
                $sql_chuong = "INSERT INTO tbl_chuong (tenchuong, noidung, sochuong, thoigian, id_truyen, is_locked, chuong_gold) 
                VALUES ('$tenchuong', '$noidung', '$sochuong', '$thoigian', $id_truyen, $is_locked, $gold)";
    
                $result_chuong = mysqli_query($mysqli, $sql_chuong);
    
                if ($result_chuong) {
                    // Chuyển hướng đến trang themchuong.php với thông tin truyền đi
                    header("Location: index.php?action=quanlytruyen&query=themchuong&id_truyen=$id_truyen");
                    exit();
                } else {
                    echo "Lỗi khi thêm chương: " . mysqli_error($mysqli);
                }
            } else {
                echo "Không thể lấy nội dung từ link truyện.";
            }
        } else {
            // Nếu lựa chọn là viết nội dung
            $noidung = mysqli_real_escape_string($mysqli, $_POST["noidung"]);
    
            // Thêm nội dung chương vào bảng tbl_chuong
            date_default_timezone_set('Asia/Ho_Chi_Minh');
            $thoigian = date('Y-m-d H:i:s');
    
            $sql_chuong = "INSERT INTO tbl_chuong (tenchuong, noidung, sochuong, thoigian, id_truyen, is_locked, chuong_gold) 
            VALUES ('$tenchuong', '$noidung', '$sochuong', '$thoigian', $id_truyen, $is_locked, $gold)";
    
            $result_chuong = mysqli_query($mysqli, $sql_chuong);
    
            if ($result_chuong) {
                // Chuyển hướng đến trang themchuong.php với thông tin truyền đi
                header("Location: index.php?action=quanlytruyen&query=themchuong&id_truyen=$id_truyen");
                exit();
            } else {
                echo "Lỗi khi thêm chương: " . mysqli_error($mysqli);
            }
        }
    }
    
    
}    
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["capnhat"])) {
        $id_truyen = $_POST["id_truyen"];
        $tieude = mysqli_real_escape_string($mysqli, $_POST["tieude"]);
        $tomtat = $_POST["tomtat"];
        $tacgia = mysqli_real_escape_string($mysqli, $_POST["tacgia"]);
        $status_tt = isset($_POST["trangthai"]) ? $_POST["trangthai"] : 0; // Lấy giá trị trạng thái từ form

        // Xử lý upload hình ảnh mới nếu có
        $hinhanh = ''; // Biến lưu trữ đường dẫn hình ảnh

        // Kiểm tra xem người dùng đã gửi dữ liệu POST hay chưa
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Xử lý khi người dùng chọn nhập URL ảnh
            if (isset($_POST['imageType']) && $_POST['imageType'] === 'url') {
                // Nếu người dùng chọn nhập URL ảnh và đã nhập URL
                if (isset($_POST['urlHinhanh']) && !empty($_POST['urlHinhanh'])) {
                    $hinhanh = $_POST['urlHinhanh']; // Lấy URL ảnh từ form
                }
            } 
            // Xử lý khi người dùng chọn tải ảnh lên từ máy tính
            elseif ($_POST['imageType'] === 'upload') {
                if (isset($_FILES['hinhanhFile']) && $_FILES['hinhanhFile']['error'] == 0) {
                    $image = $_FILES['hinhanhFile']['tmp_name'];
                    try {
                        $uploadResult = (new UploadApi())->upload($image);
                        $hinhanh = $uploadResult['secure_url'];
                    } catch (Exception $e) {
                        echo "Tải lên thất bại: " . $e->getMessage();
                        exit();
                    }
                }
            }
        }

        // Cập nhật thông tin truyện với ảnh mới (nếu có)
        if (!empty($hinhanh)) {
            $sql_capnhat = "UPDATE tbl_truyen SET tieude='$tieude', tomtat='$tomtat', tacgia='$tacgia', status_tt=$status_tt, hinhanh='$hinhanh' WHERE id_truyen=$id_truyen";
        } else {
            // Cập nhật thông tin truyện không có ảnh mới
            $sql_capnhat = "UPDATE tbl_truyen SET tieude='$tieude', tomtat='$tomtat', tacgia='$tacgia', status_tt=$status_tt WHERE id_truyen=$id_truyen";
        }

        // Thực hiện truy vấn cập nhật
        $result_capnhat = mysqli_query($mysqli, $sql_capnhat);

        if ($result_capnhat) {
            // Xóa hết thể loại của truyện
            $sql_xoa_theloai = "DELETE FROM tbl_truyen_theloai WHERE id_truyen=$id_truyen";
            mysqli_query($mysqli, $sql_xoa_theloai);

            // Thêm lại thể loại mới vào bảng tbl_truyen_theloai
            if (isset($_POST['theloai']) && is_array($_POST['theloai'])) {
                foreach ($_POST['theloai'] as $id_theloai) {
                    $sql_them_theloai = "INSERT INTO tbl_truyen_theloai (id_truyen, id_theloai) VALUES ($id_truyen, $id_theloai)";
                    mysqli_query($mysqli, $sql_them_theloai);
                }
            }
            header("Location:index.php?action=quanlytruyen&query=sua&id_truyen=$id_truyen");

            exit();
        } else {
            echo "Lỗi khi cập nhật thông tin truyện: " . mysqli_error($mysqli);
        }

        }

}

if (isset($_GET['trangthai']) && $_GET['trangthai'] === 'xoa') {
if (isset($_GET["id_truyen"])) {
    $id_truyen = $_GET["id_truyen"];

    // Xóa đánh giá của truyện
    $sql_xoa_danhgia = "DELETE FROM tbl_danhgia WHERE id_truyen = $id_truyen";
    $result_xoa_danhgia = mysqli_query($mysqli, $sql_xoa_danhgia);
    if (!$result_xoa_danhgia) {
        echo "Lỗi khi xóa đánh giá: " . mysqli_error($mysqli);
        exit(); // Dừng việc xóa nếu không xóa được đánh giá
    }

    // Xóa thể loại của truyện
    $sql_xoa_theloai = "DELETE FROM tbl_truyen_theloai WHERE id_truyen = $id_truyen";
    $result_xoa_theloai = mysqli_query($mysqli, $sql_xoa_theloai);
    if (!$result_xoa_theloai) {
        echo "Lỗi khi xóa thể loại: " . mysqli_error($mysqli);
        exit(); // Dừng việc xóa nếu không xóa được thể loại
    }

    // Xóa các bình luận của truyện
    $sql_xoa_binhluan = "DELETE FROM tbl_gold_theothang WHERE id_truyen = $id_truyen";
    $result_xoa_binhluan = mysqli_query($mysqli, $sql_xoa_binhluan);
    if (!$result_xoa_binhluan) {
        echo "Lỗi khi xóa bình luận: " . mysqli_error($mysqli);
        exit(); // Dừng việc xóa nếu không xóa được bình luận
    }

    $sql_xoa_gold = "DELETE FROM tbl_binhluan WHERE id_truyen = $id_truyen";
    $result_xoa_gold = mysqli_query($mysqli, $sql_xoa_gold);
    if (!$result_xoa_gold) {
        echo "Lỗi khi xóa bình luận: " . mysqli_error($mysqli);
        exit(); // Dừng việc xóa nếu không xóa được bình luận
    }

    // Xóa các chương của truyện
    $sql_xoa_chuong = "DELETE FROM tbl_chuong WHERE id_truyen = $id_truyen";
    $result_xoa_chuong = mysqli_query($mysqli, $sql_xoa_chuong);
    if (!$result_xoa_chuong) {
        echo "Lỗi khi xóa chương: " . mysqli_error($mysqli);
        exit(); // Dừng việc xóa nếu không xóa được các chương
    }

    // Cuối cùng, xóa bản ghi từ tbl_truyen có id_truyen tương ứng
    $sql_xoa_truyen = "DELETE FROM tbl_truyen WHERE id_truyen = $id_truyen";
    $result_xoa_truyen = mysqli_query($mysqli, $sql_xoa_truyen);
    if ($result_xoa_truyen) {
        // Nếu xóa thành công, chuyển hướng về trang lietke.php
        header("Location: index.php?action=quanlytruyen&query=lietke");
        exit();
    } else {
        echo "Lỗi khi xóa truyện: " . mysqli_error($mysqli);
    }
} else {
    echo "Không có truyện nào được chọn.";
}
}
?>

<?php 
if (isset($_GET['trangthai']) && $_GET['trangthai'] === 'duyet') {
    // Kiểm tra xem id_truyen được gửi từ URL hay không
    if (isset($_GET['id_truyen'])) {
        // Lấy id_truyen từ URL
        $id_truyen = intval($_GET['id_truyen']); // Sử dụng intval để đảm bảo giá trị là số nguyên
        
        // Thực hiện cập nhật trạng thái truyện thành đã duyệt (truyen_status = 1)
        $sql_update = "UPDATE tbl_truyen SET truyen_status = 1 WHERE id_truyen = $id_truyen";
        $result_update = mysqli_query($mysqli, $sql_update);
        
        if ($result_update) {
            // Sau khi cập nhật thành công, chuyển hướng đến trang danh sách truyện
            header("Location: index.php?action=quanlytruyen&query=lietke");
            exit();
        } else {
            // Hiển thị thông báo lỗi nếu cập nhật không thành công
            echo "Lỗi: " . mysqli_error($mysqli);
        }
    } else {
        // Nếu không tồn tại id_truyen, hiển thị thông báo lỗi
        echo "Lỗi: Không có dữ liệu id_truyen được gửi từ URL.";
    }
} else {
    // Nếu action hoặc query không phải là "duyet" hoặc "xuly", chuyển hướng đến trang không có quyền truy cập
    echo "Lỗi: Không có quyền truy cập.";
    exit();
}

?>
