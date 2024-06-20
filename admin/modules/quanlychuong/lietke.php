<?php

// Kiểm tra nếu id_truyen được truyền vào từ URL
if (isset($_GET["id_truyen"])) {

    $id_truyen = $_GET["id_truyen"];
    // Lấy danh sách chương của truyện từ bảng tbl_chuong
    $sql_lay_chuong = "SELECT * FROM tbl_chuong WHERE id_truyen = $id_truyen";
    $result_lay_chuong = $mysqli->query($sql_lay_chuong);

    if ($result_lay_chuong->num_rows > 0) {

        // Số chương hiển thị trên mỗi trang
        $soChuongTrenMotTrang = 20;

        // Lấy số lượng chương
        $soChuong = $result_lay_chuong->num_rows;

        // Tính tổng số trang
        $soTrang = ceil($soChuong / $soChuongTrenMotTrang);

        // Lấy trang hiện tại từ tham số URL
        $trangHienTai = isset($_GET['page']) ? intval($_GET['page']) : 1;

        // Tính offset cho truy vấn SQL
        $offset = ($trangHienTai - 1) * $soChuongTrenMotTrang;

        // Thực hiện truy vấn SQL với phân trang
        $sql_lay_chuong_phan_trang = "SELECT * FROM tbl_chuong WHERE id_truyen = $id_truyen LIMIT $offset, $soChuongTrenMotTrang";
        $result_lay_chuong_phan_trang = $mysqli->query($sql_lay_chuong_phan_trang);
        echo "<div class='container'>";

        // Chia kết quả thành 2 mảng cho 2 cột
        $chuongList = [];
        while ($row_chuong = $result_lay_chuong_phan_trang->fetch_assoc()) {
            $chuongList[] = $row_chuong;
        }

        $column1 = array_slice($chuongList, 0, 10);
        $column2 = array_slice($chuongList, 10, 10);

        // Hiển thị 2 cột
        echo "<div class='row'>";
        echo "<div class='col-md-6'>";
        foreach ($column1 as $row_chuong) {
            $sochuong = $row_chuong["sochuong"];
            $tenchuong = $row_chuong["tenchuong"];
            echo "<div class='card mb-4'>";
            echo "<div class='card-body'>";
            echo "<h4 class='card-title'>Chương $sochuong: $tenchuong</h4>";
            echo "<a href='index.php?action=quanlychuong&query=sua&id_chuong={$row_chuong['id_chuong']}&id_truyen=$id_truyen' class='btn btn-primary'>Sửa chương</a>";
            echo "</div>";
            echo "</div>";
        }
        echo "</div>";

        echo "<div class='col-md-6'>";
        foreach ($column2 as $row_chuong) {
            $sochuong = $row_chuong["sochuong"];
            $tenchuong = $row_chuong["tenchuong"];
            echo "<div class='card mb-4'>";
            echo "<div class='card-body'>";
            echo "<h4 class='card-title'>Chương $sochuong: $tenchuong</h4>";
            echo "<a href='index.php?action=quanlychuong&query=sua&id_chuong={$row_chuong['id_chuong']}&id_truyen=$id_truyen' class='btn btn-primary'>Sửa chương</a>";
            echo "</div>";
            echo "</div>";
        }
        echo "</div>";
        echo "</div>";

        // Hiển thị phân trang
        if ($soTrang > 1) {
            echo '<div class="container">';
            echo '<div class="row mt-4">';
            echo '<div class="col-md-8 offset-md-2">';
            echo '<nav aria-label="Page navigation example">';
            echo '<ul class="pagination justify-content-center">';
            
            // Liên kết "Trang trước"
            if ($trangHienTai > 1) {
                echo '<li class="page-item"><a class="page-link" href="?action=quanlychuong&query=lietke&id_truyen=' . $id_truyen . '&page=' . ($trangHienTai - 1) . '">Trang trước</a></li>';
            }

            // Số trang hiển thị giới hạn
            $soTrangHienThi = 5; // Số trang tối đa hiển thị cùng lúc
            $batDau = max(1, $trangHienTai - floor($soTrangHienThi / 2));
            $ketThuc = min($soTrang, $trangHienTai + floor($soTrangHienThi / 2));

            if ($batDau > 1) {
                echo '<li class="page-item"><a class="page-link" href="?action=quanlychuong&query=lietke&id_truyen=' . $id_truyen . '&page=1">1</a></li>';
                if ($batDau > 2) {
                    echo '<li class="page-item"><span class="page-link">...</span></li>';
                }
            }

            for ($i = $batDau; $i <= $ketThuc; $i++) {
                $activeClass = ($i == $trangHienTai) ? 'active' : '';
                echo '<li class="page-item ' . $activeClass . '"><a class="page-link" href="?action=quanlychuong&query=lietke&id_truyen=' . $id_truyen . '&page=' . $i . '">' . $i . '</a></li>';
            }

            if ($ketThuc < $soTrang) {
                if ($ketThuc < $soTrang - 1) {
                    echo '<li class="page-item"><span class="page-link">...</span></li>';
                }
                echo '<li class="page-item"><a class="page-link" href="?action=quanlychuong&query=lietke&id_truyen=' . $id_truyen . '&page=' . $soTrang . '">' . $soTrang . '</a></li>';
            }

            // Liên kết "Trang sau"
            if ($trangHienTai < $soTrang) {
                echo '<li class="page-item"><a class="page-link" href="?action=quanlychuong&query=lietke&id_truyen=' . $id_truyen . '&page=' . ($trangHienTai + 1) . '">Trang sau</a></li>';
            }

            echo '</ul>';
            echo '</nav>';
            echo '</div>';
            echo '</div>';
            echo '</div>';
        }
    } else {
        echo "<p class='text-center'>Không có chương nào cho truyện này.</p>";
    }
} else {
    echo "<p class='text-center'>Không có truyện nào được chọn.</p>";
}

$mysqli->close();
?>
