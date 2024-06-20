<?php

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
$currentMonth = date('n'); // Lấy tháng hiện tại (1-12)
$currentYear = date('Y');  // Lấy năm hiện tại

// Truy vấn các truyện từ bảng tbl_gold_theothang và kiểm tra trạng thái thanh toán
$queryTruyen = $role_id == 1 
    ? "SELECT t.id_truyen, t.tieude, g.thang, g.nam, g.status
       FROM tbl_gold_theothang g
       JOIN tbl_truyen t ON g.id_truyen = t.id_truyen
       WHERE NOT (g.thang = ? AND g.nam = ?)
       ORDER BY t.id_truyen, g.nam, g.thang"
    : "SELECT t.id_truyen, t.tieude, g.thang, g.nam, g.status
       FROM tbl_gold_theothang g
       JOIN tbl_truyen t ON g.id_truyen = t.id_truyen
       WHERE t.id_admin = ? AND NOT (g.thang = ? AND g.nam = ?)
       ORDER BY t.id_truyen, g.nam, g.thang";

$stmtTruyen = $mysqli->prepare($queryTruyen);
if ($role_id == 1) {
    $stmtTruyen->bind_param("ii", $currentMonth, $currentYear);
} else {
    $stmtTruyen->bind_param("iii", $id_user, $currentMonth, $currentYear);
}
$stmtTruyen->execute();
$resultTruyen = $stmtTruyen->get_result();

// Đóng kết nối
$mysqli->close();

// Process the result to group months by story
$unpaidStories = [];
$paidStories = [];
while ($row = $resultTruyen->fetch_assoc()) {
    $id_truyen = $row['id_truyen'];
    if (!isset($unpaidStories[$id_truyen]) && !isset($paidStories[$id_truyen])) {
        $story = [
            'tieude' => $row['tieude'],
            'unpaid_months' => []
        ];
    }
    if ($row['status'] == 0) {
        if (!isset($unpaidStories[$id_truyen])) {
            $unpaidStories[$id_truyen] = $story;
        }
        $unpaidStories[$id_truyen]['unpaid_months'][] = $row['thang'] . '-' . $row['nam'];
    } else {
        if (!isset($paidStories[$id_truyen])) {
            $paidStories[$id_truyen] = $story;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Truyện cần thanh toán</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-4">
        <h2>Truyện cần thanh toán</h2>
        <ul class="nav nav-tabs" id="myTab" role="tablist">
            <li class="nav-item">
                <a class="nav-link active" id="unpaid-tab" data-toggle="tab" href="#unpaid" role="tab" aria-controls="unpaid" aria-selected="true">Chưa thanh toán</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="paid-tab" data-toggle="tab" href="#paid" role="tab" aria-controls="paid" aria-selected="false">Đã thanh toán</a>
            </li>
        </ul>
        <div class="tab-content" id="myTabContent">
            <div class="tab-pane fade show active" id="unpaid" role="tabpanel" aria-labelledby="unpaid-tab">
                <table class="table table-bordered mt-3">
                    <thead>
                        <tr>
                            <th>STT</th>
                            <th>Tiêu đề</th>
                            <th>Trạng thái</th>
                            <th>Xem chi tiết</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $stt = 1;
                        foreach ($unpaidStories as $id_truyen => $story) {
                            $status = '<span class="badge badge-danger">Chưa thanh toán: ' . implode(', ', $story['unpaid_months']) . '</span>';
                            echo "<tr>
                                    <td>$stt</td>
                                    <td>{$story['tieude']}</td>
                                    <td>{$status}</td>
                                    <td><a href='index.php?action=quanlythanhtoan&query=xemchitiet&id={$id_truyen}' class='btn btn-info'>Xem chi tiết</a></td>
                                </tr>";
                            $stt++;
                        }
                        ?>
                    </tbody>
                </table>
            </div>
            <div class="tab-pane fade" id="paid" role="tabpanel" aria-labelledby="paid-tab">
                <table class="table table-bordered mt-3">
                    <thead>
                        <tr>
                            <th>STT</th>
                            <th>Tiêu đề</th>
                            <th>Trạng thái</th>
                            <th>Xem chi tiết</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $stt = 1;
                        foreach ($paidStories as $id_truyen => $story) {
                            $status = '<span class="badge badge-success">Đã thanh toán</span>';
                            echo "<tr>
                                    <td>$stt</td>
                                    <td>{$story['tieude']}</td>
                                    <td>{$status}</td>
                                    <td><a href='index.php?action=quanlythanhtoan&query=xemchitiet&id={$id_truyen}' class='btn btn-info'>Xem chi tiết</a></td>
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
