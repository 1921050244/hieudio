<?php 
ob_start(); // Start output buffering
session_start();
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Connect to the database
include('admin/config/config.php');

// Check the connection
if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

// Initialize default variables
$tieude = "Phiêu Vũ"; // Default title for the homepage
$hinhanh = "assets/image/logo.ico"; // Default image for the homepage
$tomtat = "Phiêu vũ nền tảng đọc truyện miễn phí"; // Default description for the homepage

// Get story information based on id_truyen
if (isset($_GET['id_truyen']) && !empty($_GET['id_truyen'])) {
    $id_truyen = $_GET['id_truyen'];
    $id_chuong = isset($_GET['id_chuong']) ? $_GET['id_chuong'] : null;
    $sql = "SELECT tieude, hinhanh, tomtat FROM tbl_truyen WHERE id_truyen = ?";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param("i", $id_truyen);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $tieude = $row['tieude'];
        $hinhanh = $row['hinhanh'];
        $tomtat = $row['tomtat'];
    } else {
        // Handle when the story is not found
        $tieude = "Không tìm thấy truyện";
        $tomtat = "";
    }
    $stmt->close();

    // Construct the share URL
    $share_url = "https://phieuvu.com/index.php?quanly=thongtintruyen&id_truyen=" . urlencode($id_truyen);
    if ($id_chuong) {
        $share_url .= "&id_chuong=" . urlencode($id_chuong);
    }
} else {
    // Default share URL for the homepage
    $share_url = "https://phieuvu.com/index.php";
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($tieude); ?></title>
    <?php if (isset($_GET['id_truyen']) && !empty($_GET['id_truyen'])) : ?>
        <meta property="og:title" content="<?php echo htmlspecialchars($tieude); ?>" />
        <meta property="og:description" content="<?php echo htmlspecialchars($tomtat); ?>" />
        <meta property="og:image" content="<?php echo htmlspecialchars($hinhanh); ?>" />
        <meta property="og:url" content="<?php echo htmlspecialchars($share_url); ?>" />
    <?php else : ?>
        <meta property="og:title" content="Phiêu Vũ" />
        <meta property="og:description" content="Phiêu Vũ nền tảng đọc truyện miễn phí" />
        <meta property="og:image" content="assets/image/logo.ico" />
        <meta property="og:url" content="https://phieuvu.com/index.php" />
    <?php endif; ?>

    <link rel="stylesheet" href="./assets/css/styles.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Agbalumo&family=Inter:wght@300;400;500;600&family=Oswald:wght@300;400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" integrity="sha512-..." crossorigin="anonymous" />
    <link rel="shortcut icon" type="image/png" href="assets/image/logo.ico">
    <meta name="google-signin-client_id" content="903289929360-7umpc2inp7iov7sbsmnrmpiai16onig9.apps.googleusercontent.com">
</head>



<body>
<?php 
include("pages/header.php");
include("pages/main.php");
include("pages/footer.php");


?>
<button id="btnTop" title="Go to top"><i class="fa-solid fa-chevron-up"></i></button>


<script>
    // Khi người dùng cuộn trang, hiển thị nút
window.onscroll = function() {scrollFunction()};

function scrollFunction() {
    if (document.body.scrollTop > 20 || document.documentElement.scrollTop > 20) {
        document.getElementById("btnTop").style.display = "block";
    } else {
        document.getElementById("btnTop").style.display = "none";
    }
}

// Khi người dùng nhấn nút, chuyển lên đầu trang
document.getElementById('btnTop').addEventListener('click', function() {
    document.body.scrollTop = 0; // Cho Safari
    document.documentElement.scrollTop = 0; // Cho Chrome, Firefox, IE và Opera
});
</script>
<!-- <script>
  document.addEventListener('contextmenu', function(e) {
    e.preventDefault(); // Ngăn chặn menu chuột phải
  });
  

document.addEventListener('keydown', function(event) {
    if (event.key === 'F12') {
        // Hủy sự kiện mặc định để không mở công cụ phát triển
        event.preventDefault();
        // Tải lại trang
        window.location.reload(true);
    }
});
</script> -->



<script>
    document.addEventListener('DOMContentLoaded', function () {
        const cookieBanner = document.getElementById('cookie-consent-banner');
        const acceptButton = document.getElementById('accept-cookie');

        if (!getCookie('cookie_consent')) {
            cookieBanner.style.display = 'block';
        }

        acceptButton.addEventListener('click', function () {
            setCookie('cookie_consent', 'accepted', 365);
            cookieBanner.style.display = 'none';
        });

        function setCookie(name, value, days) {
            const d = new Date();
            d.setTime(d.getTime() + (days * 24 * 60 * 60 * 1000));
            const expires = "expires=" + d.toUTCString();
            document.cookie = name + "=" + value + ";" + expires + ";path=/";
        }

        function getCookie(name) {
            const nameEQ = name + "=";
            const ca = document.cookie.split(';');
            for (let i = 0; i < ca.length; i++) {
                let c = ca[i];
                while (c.charAt(0) === ' ') c = c.substring(1, c.length);
                if (c.indexOf(nameEQ) === 0) return c.substring(nameEQ.length, c.length);
            }
            return null;
        }
    });
</script>

</body>
</html>
