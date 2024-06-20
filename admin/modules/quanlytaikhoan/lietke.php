

<?php
include('./config/config.php');

// Số lượng người dùng trên mỗi trang
$limit = 10;

// Trang hiện tại, mặc định là trang 1
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? $_GET['page'] : 1;

// Tính offset bắt đầu từ vị trí của người dùng trên trang hiện tại
$offset = ($page - 1) * $limit;

// Xử lý dữ liệu tìm kiếm
$search = isset($_GET['search']) ? $_GET['search'] : '';

// Truy vấn để lấy dữ liệu từ bảng tbl_user, loại bỏ role_id = 1 và áp dụng điều kiện tìm kiếm
$sql = "SELECT * FROM tbl_user WHERE role_id != 1 
        AND (tenuser LIKE '%$search%' OR email LIKE '%$search%' OR ma_tk LIKE '%$search%') 
        LIMIT $limit OFFSET $offset";
$result = mysqli_query($mysqli, $sql);

// Mảng ánh xạ role_id sang tên role
$roleMapping = array(
    1 => 'Admin',
    2 => 'Tác giả',
    3 => 'Chờ duyệt',
    4 => 'Người dùng'
);
?>

<!-- Hiển thị kết quả tìm kiếm -->
<div class="container mt-4">
<form action="index.php" method="get" class="form-inline mt-2 mb-2">
        <input type="hidden" name="action" value="quanlytaikhoan">
        <input type="hidden" name="query" value="lietke">
        <input type="hidden" name="page" value="1"> <!-- Để đảm bảo page không bị trống -->
        <div class="input-group">
            <input type="text" name="search" class="form-control" placeholder="Tìm kiếm">
            <div class="input-group-append">
                <button type="submit" class="btn btn-primary">Tìm kiếm</button>
            </div>
        </div>
    </form>

    <h2>Kết quả tìm kiếm</h2>
    <?php if (mysqli_num_rows($result) > 0) { ?>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID User</th>
                    <th>Tên</th>
                    <th>Email</th>
                    <th>Mã tài khoản</th>
                    <th>Quyền</th>
                    <th>Thao tác</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                    <tr>
                        <td><?php echo $row['id_user']; ?></td>
                        <td><?php echo $row['tenuser']; ?></td>
                        <td><?php echo $row['email']; ?></td>
                        <td><?php echo $row['ma_tk']; ?></td>
                        <td><?php echo $roleMapping[$row['role_id']]; ?></td>
                        <td>
                            <form method="POST" action="index.php?action=quanlytaikhoan&query=xuly&id=<?php echo $row['id_user']; ?>">
                            <?php if ($row['role_id'] == 3) { ?>
        <button type="submit" name="duyet" class="btn btn-success btn-sm"><i class="fas fa-check"></i></button>
    <?php } ?>
    <a href="index.php?action=quanlytaikhoan&query=sua&id=<?php echo $row['id_user']; ?>" class="btn btn-warning btn-sm"><i class="fas fa-edit"></i></a>
    <button type="submit" name="xoa" class="btn btn-danger btn-sm"><i class="fas fa-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    <?php } else { ?>
        <p>Không tìm thấy kết quả nào.</p>
    <?php } ?>
</div>
        <!-- Phân trang -->
        <?php
        $sql_total = "SELECT COUNT(*) AS total FROM tbl_user WHERE role_id != 1";
        $result_total = mysqli_query($mysqli, $sql_total);
        $row_total = mysqli_fetch_assoc($result_total);
        $total_records = $row_total['total'];
        $total_pages = ceil($total_records / $limit);
        ?>
        <nav aria-label="Page navigation example">
            <ul class="pagination">
                <?php for ($i = 1; $i <= $total_pages; $i++) { ?>
                    <li class="page-item <?php if ($i == $page) echo 'active'; ?>"><a class="page-link" href="index.php?page=<?php echo $i; ?>"><?php echo $i; ?></a></li>
                <?php } ?>
            </ul>
        </nav>
    </div>

    <!-- Bootstrap JS và jQuery -->
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
