<?php
// Kết nối cơ sở dữ liệu

// Lấy thông tin người dùng từ session
$id_admin_tuong_ung = $_SESSION['id_user']; // Thay thế bằng id_admin tương ứng
$role_id_tuong_ung = $_SESSION['role_id']; // Thay thế bằng role_id tương ứng

// Số truyện hiển thị trên mỗi trang
$truyenPerPage = 10; 
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$offset = ($page - 1) * $truyenPerPage;

// Lấy từ khóa tìm kiếm
$searchTerm = isset($_GET['search']) ? $_GET['search'] : '';

// Lấy tham số filter
$filter = isset($_GET['filter']) ? $_GET['filter'] : '';

// Tạo đường dẫn cho form tìm kiếm
$searchFormAction = "index.php?action=quanlytruyen&query=lietke";

// Đếm tổng số truyện chưa duyệt
$sqlCountPending = "SELECT COUNT(*) AS totalPending FROM tbl_truyen WHERE truyen_status = 0";
$resultCountPending = $mysqli->query($sqlCountPending);
$rowCountPending = $resultCountPending->fetch_assoc();
$totalPending = $rowCountPending['totalPending'];

// Đếm tổng số truyện để phân trang
$sqlCount = "SELECT COUNT(DISTINCT tbl_truyen.id_truyen) as total
             FROM tbl_truyen
             LEFT JOIN tbl_truyen_theloai ON tbl_truyen.id_truyen = tbl_truyen_theloai.id_truyen
             LEFT JOIN tbl_theloai ON tbl_truyen_theloai.id_theloai = tbl_theloai.id_theloai
             LEFT JOIN (
                 SELECT id_truyen, COUNT(id_chuong) AS sochuong
                 FROM tbl_chuong
                 GROUP BY id_truyen
             ) AS chuong_count ON tbl_truyen.id_truyen = chuong_count.id_truyen
             WHERE ($role_id_tuong_ung = 1 OR tbl_truyen.id_admin = $id_admin_tuong_ung)
                 AND (tbl_truyen.tieude LIKE '%$searchTerm%' OR tbl_theloai.tentheloai LIKE '%$searchTerm%' OR chuong_count.sochuong LIKE '%$searchTerm%')";

// Áp dụng filter nếu có
if ($filter === 'pending') {
    $sqlCount .= " AND tbl_truyen.truyen_status = 0";
}

$resultCount = $mysqli->query($sqlCount);
$rowCount = $resultCount->fetch_assoc();
$totalRows = $rowCount['total'];
$totalPages = ceil($totalRows / $truyenPerPage);

// Lấy danh sách truyện từ cơ sở dữ liệu
$sql = "SELECT tbl_truyen.*, tbl_user.email AS nguoidang, GROUP_CONCAT(tbl_theloai.tentheloai ORDER BY tbl_theloai.thutu SEPARATOR ', ') AS theloai, COALESCE(chuong_count.sochuong, 0) AS sochuong
        FROM tbl_truyen
        LEFT JOIN tbl_truyen_theloai ON tbl_truyen.id_truyen = tbl_truyen_theloai.id_truyen
        LEFT JOIN tbl_theloai ON tbl_truyen_theloai.id_theloai = tbl_theloai.id_theloai
        LEFT JOIN tbl_user ON tbl_truyen.id_admin = tbl_user.id_user
        LEFT JOIN (
            SELECT id_truyen, COUNT(id_chuong) AS sochuong
            FROM tbl_chuong
            GROUP BY id_truyen
        ) AS chuong_count ON tbl_truyen.id_truyen = chuong_count.id_truyen
        WHERE ($role_id_tuong_ung = 1 OR tbl_truyen.id_admin = $id_admin_tuong_ung)
            AND (tbl_truyen.tieude LIKE '%$searchTerm%' OR tbl_theloai.tentheloai LIKE '%$searchTerm%' OR chuong_count.sochuong LIKE '%$searchTerm%')";

// Áp dụng filter nếu có
if ($filter === 'pending') {
    $sql .= " AND tbl_truyen.truyen_status = 0";
}

$sql .= " GROUP BY tbl_truyen.id_truyen
         ORDER BY tbl_truyen.id_truyen DESC
         LIMIT $offset, $truyenPerPage";

$result = $mysqli->query($sql);

?>


<div class="container mt-4">
    <h2>Danh Sách Truyện</h2>

    <!-- Nút thông báo truyện chưa duyệt -->

    <?php if ($role_id_tuong_ung == 1 && $totalPending > 0): ?>
    <div class="alert alert-warning" role="alert">
        Bạn có <strong><?php echo $totalPending; ?></strong> truyện chưa duyệt. 
        <a href="index.php?action=quanlytruyen&query=lietke&filter=pending" class="alert-link">Xem ngay</a>.
    </div>
<?php elseif ($role_id_tuong_ung == 2 && $totalPending > 0): ?>
    <?php
    // Kiểm tra xem có truyện của tác giả chưa được duyệt hay không
    $sqlCheckPendingByAuthor = "SELECT COUNT(*) AS totalPendingByAuthor FROM tbl_truyen WHERE truyen_status = 0 AND id_admin = $id_admin_tuong_ung";
    $resultCheckPendingByAuthor = $mysqli->query($sqlCheckPendingByAuthor);
    $rowCountPendingByAuthor = $resultCheckPendingByAuthor->fetch_assoc();
    $totalPendingByAuthor = $rowCountPendingByAuthor['totalPendingByAuthor'];
    ?>
    <?php if ($totalPendingByAuthor > 0): ?>
        <div class="alert alert-warning" role="alert">
            Bạn có <strong><?php echo $totalPendingByAuthor; ?></strong> truyện chờ duyệt. 
            <a href="index.php?action=quanlytruyen&query=lietke&filter=pending" class="alert-link">Xem ngay</a>.
        </div>
    <?php endif; ?>
<?php endif; ?>


    <!-- Form tìm kiếm -->
    <form action="index.php" method="get" class="form-inline mt-2 mb-2">
        <input type="hidden" name="action" value="quanlytruyen">
        <input type="hidden" name="query" value="lietke">
        <input type="hidden" name="page" value="1"> <!-- Để đảm bảo page không bị trống -->
        <div class="input-group">
            <input type="text" name="search" class="form-control" placeholder="Tìm kiếm" value="<?php echo htmlspecialchars($searchTerm); ?>">
            <div class="input-group-append">
                <button type="submit" class="btn btn-primary">Tìm kiếm</button>
            </div>
        </div>
    </form>

    <?php
    if ($result->num_rows > 0) {
    ?>
    <div class="table-responsive">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>STT</th>
                    <th>Ảnh</th>
                    <th>Tên truyện</th>
                    <th>Số Chương</th>
                    <th>Người đăng</th>

                    <th>Gold</th>
                    <th>Trạng thái</th>
                    <th>Thao tác</th>
                </tr>
            </thead>
            <tbody>
            <?php
$stt = $offset + 1;
while ($row = $result->fetch_assoc()) {
    $img_path = $row['hinhanh'];

    // Kiểm tra xem đường dẫn hình ảnh có phải là URL không
    if (!filter_var($img_path, FILTER_VALIDATE_URL)) {
        $img_path = '../' . $img_path;
    }

    // Kiểm tra trạng thái của truyện
    $trangThai = $row['truyen_status'] == 1 ? 'Đã duyệt' : 'Chưa duyệt';

    // Rút gọn nội dung tiêu đề nếu quá dài
    $tieude = strlen($row['tieude']) > 50 ? substr($row['tieude'], 0, 50) . "..." : $row['tieude'];

    // Thêm nút "Xem thêm" cho nội dung quá dài

    // Kiểm tra quyền của người dùng để hiển thị nút duyệt
    $actionButtons = '';
    if ($_SESSION['role_id'] == 1 && $row['truyen_status'] == 0) {
        $actionButtons = "<a href='index.php?action=quanlytruyen&query=xuly&id_truyen={$row['id_truyen']}&trangthai=duyet' class='btn btn-success btn-sm'><i class='fas fa-check'></i></a>";
    }

    echo "<tr>
            <td>$stt</td>
            <td><img src='$img_path' alt='Ảnh truyện' class='img-thumbnail' style='max-width: 50px;'></td>
            <td>{$tieude}</td>
            <td>{$row['sochuong']}</td>
            <td>{$row['nguoidang']}</td>
            <td>{$row['gold']}</td>
            <td>{$trangThai}</td>
            <td>
                <a href='index.php?action=quanlytruyen&query=themchuong&id_truyen={$row['id_truyen']}' class='btn btn-primary btn-sm'><i class='fas fa-plus'></i></a>
                <a href='index.php?action=quanlytruyen&query=sua&id_truyen={$row['id_truyen']}' class='btn btn-warning btn-sm'><i class='fas fa-edit'></i></a>
                <a href='#' onclick='confirmDelete({$row['id_truyen']})' class='btn btn-danger btn-sm'><i class='fas fa-trash'></i></a>
                $actionButtons
            </td>
        </tr>";

    $stt++;
}
?>

            </tbody>
        </table>
    </div>

        <!-- Pagination -->
        <nav aria-label="Page navigation">
            <ul class="pagination justify-content-center">
                <?php
                // Hiển thị nút Previous
                if ($page > 1) {
                    echo "<li class='page-item'><a class='page-link' href='index.php?action=quanlytruyen&query=lietke&page=" . ($page - 1) . "&search=" . urlencode($searchTerm) . "'>&laquo; Previous</a></li>";
                }

                // Hiển thị các trang số
                for ($i = 1; $i <= $totalPages; $i++) {
                    echo "<li class='page-item " . ($page == $i ? 'active' : '') . "'><a class='page-link' href='index.php?action=quanlytruyen&query=lietke&page=$i&search=" . urlencode($searchTerm) . "'>$i</a></li>";
                }

                // Hiển thị nút Next
                if ($page < $totalPages) {
                    echo "<li class='page-item'><a class='page-link' href='index.php?action=quanlytruyen&query=lietke&page=" . ($page + 1) . "&search=" . urlencode($searchTerm) . "'>Next &raquo;</a></li>";
                }
                ?>
            </ul>
        </nav>
    <?php
    } else {
        echo "<p class='text-center'>Không có truyện nào trong cơ sở dữ liệu.</p>";
    }
    ?>
</div>

<?php
$mysqli->close();
?>
<script>
function confirmDelete(id_truyen) {
    var result = confirm("Bạn có chắc muốn xóa truyện này không?");
    if (result) {
        // Nếu người dùng chọn "OK", chuyển hướng đến trang xử lý xóa
        window.location.href = 'index.php?action=quanlytruyen&query=xuly&trangthai=xoa&id_truyen=' + id_truyen;
    }
}
</script>
<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>

<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
