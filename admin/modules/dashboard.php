<?php
include('./config/config.php');

// Kiểm tra kết nối
if ($mysqli->connect_error) {
    die('Kết nối không thành công: ' . $mysqli->connect_error);
}

if (!isset($_SESSION['role_id']) || $_SESSION['role_id'] == 4) {
    echo "<script>alert('Bạn không có quyền truy cập.'); window.location.href='index.php';</script>";
    exit();
}

$role_id = $_SESSION['role_id'];
$id_user = $_SESSION['id_user'];

// Lấy tháng và năm hiện tại
$currentMonth = date('m');
$currentYear = date('Y');

// Tổng số truyện
$queryTotalTruyen = $role_id == 1 
    ? "SELECT COUNT(*) AS total_truyen FROM tbl_truyen"
    : "SELECT COUNT(*) AS total_truyen FROM tbl_truyen WHERE id_admin = ?";
$stmtTotalTruyen = $mysqli->prepare($queryTotalTruyen);
if ($role_id != 1) {
    $stmtTotalTruyen->bind_param("i", $id_user);
}
$stmtTotalTruyen->execute();
$resultTotalTruyen = $stmtTotalTruyen->get_result();
$rowTotalTruyen = $resultTotalTruyen->fetch_assoc();
$totalTruyen = isset($rowTotalTruyen['total_truyen']) ? $rowTotalTruyen['total_truyen'] : 0;

// Tổng số tài khoản
$queryTotalTaiKhoan = "SELECT COUNT(*) AS total_taikhoan FROM tbl_user";
$resultTotalTaiKhoan = $mysqli->query($queryTotalTaiKhoan);
$rowTotalTaiKhoan = $resultTotalTaiKhoan->fetch_assoc();
$totalTaiKhoan = isset($rowTotalTaiKhoan['total_taikhoan']) ? $rowTotalTaiKhoan['total_taikhoan'] : 0;

// Tổng số gold
$queryTotalGold = $role_id == 1 
    ? "SELECT SUM(gold) AS total_gold FROM tbl_truyen"
    : "SELECT SUM(gold) AS total_gold FROM tbl_truyen WHERE id_admin = ?";
$stmtTotalGold = $mysqli->prepare($queryTotalGold);
if ($role_id != 1) {
    $stmtTotalGold->bind_param("i", $id_user);
}
$stmtTotalGold->execute();
$resultTotalGold = $stmtTotalGold->get_result();
$rowTotalGold = $resultTotalGold->fetch_assoc();
$totalGold = isset($rowTotalGold['total_gold']) ? $rowTotalGold['total_gold'] : 0;

// Tổng lượt đọc
$queryTotalLuotDoc = $role_id == 1 
    ? "SELECT SUM(luotdoc) AS total_luotdoc FROM tbl_truyen"
    : "SELECT SUM(luotdoc) AS total_luotdoc FROM tbl_truyen WHERE id_admin = ?";
$stmtTotalLuotDoc = $mysqli->prepare($queryTotalLuotDoc);
if ($role_id != 1) {
    $stmtTotalLuotDoc->bind_param("i", $id_user);
}
$stmtTotalLuotDoc->execute();
$resultTotalLuotDoc = $stmtTotalLuotDoc->get_result();
$rowTotalLuotDoc = $resultTotalLuotDoc->fetch_assoc();
$totalLuotDoc = isset($rowTotalLuotDoc['total_luotdoc']) ? $rowTotalLuotDoc['total_luotdoc'] : 0;

// Danh sách truyện đọc nhiều (lấy top 10)
$queryTopTruyenDocNhieu = $role_id == 1 
    ? "SELECT id_truyen, tieude, luotdoc FROM tbl_truyen ORDER BY luotdoc DESC LIMIT 10"
    : "SELECT id_truyen, tieude, luotdoc FROM tbl_truyen WHERE id_admin = ? ORDER BY luotdoc DESC LIMIT 10";
$stmtTopTruyenDocNhieu = $mysqli->prepare($queryTopTruyenDocNhieu);
if ($role_id != 1) {
    $stmtTopTruyenDocNhieu->bind_param("i", $id_user);
}
$stmtTopTruyenDocNhieu->execute();
$resultTopTruyenDocNhieu = $stmtTopTruyenDocNhieu->get_result();

// Top GOLD tháng hiện tại
$queryGoldTheoThang = $role_id == 1 
    ? "SELECT g.id_truyen, t.tieude, g.thang, g.nam, g.gold AS total_gold_theothang 
       FROM tbl_gold_theothang g
       JOIN tbl_truyen t ON g.id_truyen = t.id_truyen
       WHERE g.thang = ? AND g.nam = ?
       ORDER BY g.gold DESC"
    : "SELECT g.id_truyen, t.tieude, g.thang, g.nam, g.gold AS total_gold_theothang 
       FROM tbl_gold_theothang g
       JOIN tbl_truyen t ON g.id_truyen = t.id_truyen
       WHERE t.id_admin = ? AND g.thang = ? AND g.nam = ?
       ORDER BY g.gold DESC";
$stmtGoldTheoThang = $mysqli->prepare($queryGoldTheoThang);
if ($role_id == 1) {
    $stmtGoldTheoThang->bind_param("ii", $currentMonth, $currentYear);
} else {
    $stmtGoldTheoThang->bind_param("iii", $id_user, $currentMonth, $currentYear);
}
$stmtGoldTheoThang->execute();
$resultGoldTheoThang = $stmtGoldTheoThang->get_result();

// Đóng kết nối
$mysqli->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thống kê</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-4">
        <h2>Thống kê</h2>
        <div class="row">
            <div class="col-md-3">
                <div class="card text-white bg-primary mb-3">
                    <div class="card-header">Tổng số truyện</div>
                    <div class="card-body">
                        <h5 class="card-title"><?php echo $totalTruyen; ?></h5>
                    </div>
                </div>
            </div>
            <?php
            if (isset($_SESSION['role_id']) && $_SESSION['role_id'] == 1) {
                echo '
                <div class="col-md-3">
                    <div class="card text-white bg-success mb-3">
                        <div class="card-header">Tổng số tài khoản</div>
                        <div class="card-body">
                            <h5 class="card-title">' . $totalTaiKhoan . '</h5>
                        </div>
                    </div>
                </div>
                ';
            }
            ?>
            <div class="col-md-3">
                <div class="card text-white bg-warning mb-3">
                    <div class="card-header">Tổng số gold</div>
                    <div class="card-body">
                        <h5 class="card-title"><?php echo $totalGold; ?></h5>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-white bg-info mb-3">
                    <div class="card-header">Tổng lượt đọc</div>
                    <div class="card-body">
                        <h5 class="card-title"><?php echo $totalLuotDoc; ?></h5>
                    </div>
                </div>
            </div>
        </div>

        <ul class="nav nav-tabs">
            <li class="nav-item">
                <a class="nav-link active" id="luotdoc-tab" data-toggle="tab" href="#luotdocSection" role="tab">Lượt đọc</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="goldthang-tab" data-toggle="tab" href="#goldthangSection" role="tab">Top GOLD tháng hiện tại</a>
            </li>
        </ul>
        <div class="tab-content">
            <div id="luotdocSection" class="tab-pane fade show active" role="tabpanel" aria-labelledby="luotdoc-tab">
                <h3>Danh sách truyện đọc nhiều</h3>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>STT</th>
                            <th>Tiêu đề</th>
                            <th>Lượt đọc</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $stt = 1;
                        while ($row = $resultTopTruyenDocNhieu->fetch_assoc()) {
                            echo "<tr>
                                    <td>$stt</td>
                                    <td>{$row['tieude']}</td>
                                    <td>{$row['luotdoc']}</td>
                                </tr>";
                            $stt++;
                        }
                        ?>
                    </tbody>
                </table>
            </div>

            <div id="goldthangSection" class="tab-pane fade" role="tabpanel" aria-labelledby="goldthang-tab">
                <h3> GOLD tháng </h3>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>STT</th>
                            <th>Tiêu đề truyện</th>
                            <th>Tháng</th>
                            <th>Năm</th>
                            <th>Tổng Gold</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $stt = 1;
                        while ($row = $resultGoldTheoThang->fetch_assoc()) {
                            echo "<tr>
                                    <td>$stt</td>
                                    <td>{$row['tieude']}</td>
                                    <td>{$row['thang']}</td>
                                    <td>{$row['nam']}</td>
                                    <td>{$row['total_gold_theothang']}</td>
                                </tr>";
                            $stt++;
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Script Bootstrap -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
