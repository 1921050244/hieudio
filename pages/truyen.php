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
    $imagePath = '../admin/modules/quanlyslide/uploads/bia1.webp';
}
?>

<!-- Thẻ img cho hiển thị ảnh -->
<img class="top-bg-op-box" src="<?php echo $imagePath; ?>" alt="Background Image">
<main>
<script>
    document.addEventListener('DOMContentLoaded', function () {
    var filterToggle = document.getElementById('mobileFilterToggle');
    var filterModal = document.getElementById('filterModal');
    var closeButton = document.getElementsByClassName("close")[0];

    // Khi người dùng nhấn vào icon, mở modal
    filterToggle.addEventListener('click', function () {
        filterModal.style.display = "block";
    });

    // Khi người dùng nhấn vào nút đóng (x), đóng modal
    closeButton.onclick = function() {
        filterModal.style.display = "none";
    }

    // Khi người dùng nhấn vào bất cứ đâu ngoài modal, đóng modal
    window.onclick = function(event) {
        if (event.target == filterModal) {
            filterModal.style.display = "none";
        }
    }
});
</script>
<!-- Icon để mở form -->

    <!-- Phần còn lại của nội dung chính đặt tại đây -->

  <div class="container">
        <div class="main-content">


            <div class="filter-container-eds">


                <?php
                // Giả sử bạn đã có kết nối cơ sở dữ liệu $mysqli
                
                $sql = "SELECT id_theloai, tentheloai FROM tbl_theloai";
                $result = $mysqli->query($sql);

                // Kiểm tra và hiển thị dữ liệu
                if ($result->num_rows > 0) {
                    echo '<h4>Thể Loại</h4>';
                    echo '<ul class="filter-list-eds">';
                    $selectedKeywords = array();

                    while ($row = $result->fetch_assoc()) {
                        $categoryId = $row['id_theloai'];
                        $categoryName = $row['tentheloai'];

                        // Kiểm tra xem thể loại này đã được chọn không
                        $activeClass = (isset($_GET['category']) && in_array($categoryName, (array) $_GET['category'])) ? 'selected-category-eds' : '';

                        // Thêm từ khóa vào mảng nếu nó được chọn
                        if ($activeClass) {
                            $selectedKeywords[] = $categoryName;
                        }

                        echo "<li class='filter-item-eds'><a href='index.php?quanly=truyen&category[]=$categoryName' class='$activeClass'>$categoryName</a></li>";
                    }

                    echo '</ul>';
                    // Sau vòng lặp while
                    ;
                } else {
                    echo "Không có dữ liệu thể loại";
                }
                ?>


                <h4>Tình Trạng</h4>
                <!-- Thêm các tình trạng vào đây -->
                <ul class="filter-list-eds">
                    <li class="filter-item-eds"><a href="index.php?quanly=truyen&truyen=dangra"
                            onclick="toggleCategory('Đang Ra')">Đang Ra</a></li>
                    <li class="filter-item-eds"><a href="index.php?quanly=truyen&truyen=hoanthanh"
                            onclick="toggleCategory('Hoàn Thành')">Hoàn Thành</a></li>
                    <!-- Thêm tình trạng khác tùy thuộc vào nhu cầu -->
                </ul>


                <!-- Thêm mã HTML cho Số chương -->
                <h4>Đã chọn</h4>
                <?php
                echo '<div class="selected-keywords-eds">';
                echo implode(', ', $selectedKeywords);
                echo '</div>'
                    ?>
                <div class="selected-keywords-eds" id="selectedKeywords"><span id="selectedKeywordsList"></span></div>
            </div>

            <div class="list-container-eds">
                <div class="additional-info-container-eds">
                    <div class="jfsdjfj">
                <div class="mobile-filter-menu">
    <i class="fa-solid fa-arrow-down-wide-short" id="mobileFilterToggle"></i>
</div>

<!-- Modal Form -->
<div id="filterModal" class="modal">
    <!-- Nội dung của modal -->
    <div class="modal-content">
        
        <span class="close">&times;</span>
        <div class="iljdjld">
        <h2>Bộ lọc</h2>
        <div class="fkdjf432a">
                    <ul class="additional-info-list-eds">

                        <li class="dropdown-item-eds" onclick="toggleDropdown('moiCapNhatDropdown')">
                            Mới cập nhật <i class="fa-solid fa-arrow-down"></i>
                            <ul id="moiCapNhatDropdown" class="dropdown-123">
                                <li><a href="index.php?quanly=truyen&moiCapNhat=truyenMoi">Truyện mới</a></li>
                            </ul>
                        </li>

                        <li class="dropdown-item-eds" onclick="toggleDropdown('luotdoc-dropdown')">
                            Lượt đọc <i class="fa-solid fa-arrow-down"></i>
                            <ul id="luotdoc-dropdown" class="dropdown-123">
                                <li><a href="index.php?quanly=truyen&luotDoc=duoi1000">Dưới 1000</a></li>
                                <li><a href="index.php?quanly=truyen&luotDoc=1000-100000">1000 - 100000</a></li>
                                <li><a href="index.php?quanly=truyen&luotDoc=tren100000">Trên 100000</a></li>
                            </ul>
                        </li>

                        <li class="dropdown-item-eds" onclick="toggleDropdown('diemdanhgia-dropdown')">
                            Điểm đánh giá <i class="fa-solid fa-arrow-down"></i>
                            <ul id="diemdanhgia-dropdown" class="dropdown-123">
                                <li><a href="index.php?quanly=truyen&danhgia=duoi3">Dưới 3.0</a></li>
                                <li><a href="index.php?quanly=truyen&danhgia=3-4">3.0 - 4.0</a></li>
                                <li><a href="index.php?quanly=truyen&danhgia=4-5">4.0 - 5</a></li>
                            </ul>
                        </li>
                        <li class="dropdown-item-eds" onclick="toggleDropdown('decu-dropdown')">
                            Đề cử <i class="fa-solid fa-arrow-down"></i>
                            <ul id="decu-dropdown" class="dropdown-123">
                                <li><a href="index.php?quanly=truyen&decu=duoi100">Dưới 100</a></li>
                                <li><a href="index.php?quanly=truyen&decu=100-1000">100 - 1000</a></li>
                                <li><a href="index.php?quanly=truyen&decu=1000-5000">1000 - 5000</a></li>
                                <li><a href="index.php?quanly=truyen&decu=tren5000">Trên 5000</a></li>
                            </ul>
                        </li>

                        <!-- Dropdown Gold -->
<li class="dropdown-item-eds" onclick="toggleDropdown('gold-dropdown')">
    Gold <i class="fa-solid fa-arrow-down"></i>
    <ul id="gold-dropdown" class="dropdown-123" style="display: none;">
        <li><a href="index.php?quanly=truyen&gold=duoi10">Dưới 10000</a></li>
        <li><a href="index.php?quanly=truyen&gold=10-100">10000 - 100000</a></li>
        <li><a href="index.php?quanly=truyen&gold=tren100">Trên 100000</a></li>
        <!-- Thêm các mức gold khác tùy thuộc vào nhu cầu -->
    </ul>
</li>
                        <li class="dropdown-item-eds">
                            <a href="index.php?quanly=truyen&like=yeuthich">Yêu thích</a>
                            <!-- Thêm nội dung cho Yêu thích tại đây -->
                        </li>

                        <li class="dropdown-item-eds">
                            <a href="index.php?quanly=truyen&binhluan=comment">Bình luận</a>
                            <!-- Thêm nội dung cho Bình luận tại đây -->
                        </li>
                        <li class="dropdown-item-eds">
                            <a href="index.php?quanly=truyen&chuong=tongchuong">Số chương</a>
                            <!-- Thêm nội dung cho Số chương tại đây -->
                        </li>
                        <!-- Các mục khác ở đây -->
                    </ul>
                </div>

        
        <?php
                // Giả sử bạn đã có kết nối cơ sở dữ liệu $mysqli
                
                $sql = "SELECT id_theloai, tentheloai FROM tbl_theloai";
                $result = $mysqli->query($sql);

                // Kiểm tra và hiển thị dữ liệu
                if ($result->num_rows > 0) {
                    echo '<h4>Thể Loại</h4>';
                    echo '<ul class="filter-list-eds">';
                    $selectedKeywords = array();

                    while ($row = $result->fetch_assoc()) {
                        $categoryId = $row['id_theloai'];
                        $categoryName = $row['tentheloai'];

                        // Kiểm tra xem thể loại này đã được chọn không
                        $activeClass = (isset($_GET['category']) && in_array($categoryName, (array) $_GET['category'])) ? 'selected-category-eds' : '';

                        // Thêm từ khóa vào mảng nếu nó được chọn
                        if ($activeClass) {
                            $selectedKeywords[] = $categoryName;
                        }

                        echo "<li class='filter-item-eds'><a href='index.php?quanly=truyen&category[]=$categoryName' class='$activeClass'>$categoryName</a></li>";
                    }

                    echo '</ul>';
                    // Sau vòng lặp while
                    ;
                } else {
                    echo "Không có dữ liệu thể loại";
                }
                ?>


                <h4>Tình Trạng</h4>
                <!-- Thêm các tình trạng vào đây -->
                <ul class="filter-list-eds">
                    <li class="filter-item-eds"><a href="index.php?quanly=truyen&truyen=dangra"
                            onclick="toggleCategory('Đang Ra')">Đang Ra</a></li>
                    <li class="filter-item-eds"><a href="index.php?quanly=truyen&truyen=hoanthanh"
                            onclick="toggleCategory('Hoàn Thành')">Hoàn Thành</a></li>
                    <!-- Thêm tình trạng khác tùy thuộc vào nhu cầu -->
                </ul>


                <!-- Thêm mã HTML cho Số chương -->
                <h4>Đã chọn</h4>
                <?php
                echo '<div class="selected-keywords-eds">';
                echo implode(', ', $selectedKeywords);
                echo '</div>'
                    ?>
        <div class="filter-options">
        <?php
                // Giả sử bạn đã có kết nối cơ sở dữ liệu $mysqli
                
                $sql = "SELECT id_theloai, tentheloai FROM tbl_theloai";
                $result = $mysqli->query($sql);

                // Kiểm tra và hiển thị dữ liệu
                if ($result->num_rows > 0) {
                    echo '<h4>Thể Loại</h4>';
                    echo '<ul class="filter-list-eds">';
                    $selectedKeywords = array();

                    while ($row = $result->fetch_assoc()) {
                        $categoryId = $row['id_theloai'];
                        $categoryName = $row['tentheloai'];

                        // Kiểm tra xem thể loại này đã được chọn không
                        $activeClass = (isset($_GET['category']) && in_array($categoryName, (array) $_GET['category'])) ? 'selected-category-eds' : '';

                        // Thêm từ khóa vào mảng nếu nó được chọn
                        if ($activeClass) {
                            $selectedKeywords[] = $categoryName;
                        }

                        echo "<li class='filter-item-eds'><a href='index.php?quanly=truyen&category[]=$categoryName' class='$activeClass'>$categoryName</a></li>";
                    }

                    echo '</ul>';
                    // Sau vòng lặp while
                    ;
                } else {
                    echo "Không có dữ liệu thể loại";
                }
                ?>


                <h4>Tình Trạng</h4>
                <!-- Thêm các tình trạng vào đây -->
                <ul class="filter-list-eds">
                    <li class="filter-item-eds"><a href="index.php?quanly=truyen&truyen=dangra"
                            onclick="toggleCategory('Đang Ra')">Đang Ra</a></li>
                    <li class="filter-item-eds"><a href="index.php?quanly=truyen&truyen=hoanthanh"
                            onclick="toggleCategory('Hoàn Thành')">Hoàn Thành</a></li>
                    <!-- Thêm tình trạng khác tùy thuộc vào nhu cầu -->
                </ul>


                <!-- Thêm mã HTML cho Số chương -->
                <h4>Đã chọn</h4>
                <?php
                echo '<div class="selected-keywords-eds">';
                echo implode(', ', $selectedKeywords);
                echo '</div>'
                    ?>
        </div>
    </div>
</div>
</div>
<div class="fkdjf432">
                    <ul class="additional-info-list-eds">

                        <li class="dropdown-item-eds" onclick="toggleDropdown('moiCapNhatDropdown')">
                            Mới cập nhật <i class="fa-solid fa-arrow-down"></i>
                            <ul id="moiCapNhatDropdown" class="dropdown-123">
                                <li><a href="index.php?quanly=truyen&moiCapNhat=truyenMoi">Truyện mới</a></li>
                            </ul>
                        </li>

                        <li class="dropdown-item-eds" onclick="toggleDropdown('luotdoc-dropdown')">
                            Lượt đọc <i class="fa-solid fa-arrow-down"></i>
                            <ul id="luotdoc-dropdown" class="dropdown-123">
                                <li><a href="index.php?quanly=truyen&luotDoc=duoi1000">Dưới 1000</a></li>
                                <li><a href="index.php?quanly=truyen&luotDoc=1000-100000">1000 - 100000</a></li>
                                <li><a href="index.php?quanly=truyen&luotDoc=tren100000">Trên 100000</a></li>
                            </ul>
                        </li>

                        <li class="dropdown-item-eds" onclick="toggleDropdown('diemdanhgia-dropdown')">
                            Điểm đánh giá <i class="fa-solid fa-arrow-down"></i>
                            <ul id="diemdanhgia-dropdown" class="dropdown-123">
                                <li><a href="index.php?quanly=truyen&danhgia=duoi3">Dưới 3.0</a></li>
                                <li><a href="index.php?quanly=truyen&danhgia=3-4">3.0 - 4.0</a></li>
                                <li><a href="index.php?quanly=truyen&danhgia=4-5">4.0 - 5</a></li>
                            </ul>
                        </li>
                        <li class="dropdown-item-eds" onclick="toggleDropdown('decu-dropdown')">
                            Đề cử <i class="fa-solid fa-arrow-down"></i>
                            <ul id="decu-dropdown" class="dropdown-123">
                                <li><a href="index.php?quanly=truyen&decu=duoi100">Dưới 100</a></li>
                                <li><a href="index.php?quanly=truyen&decu=100-1000">100 - 1000</a></li>
                                <li><a href="index.php?quanly=truyen&decu=1000-5000">1000 - 5000</a></li>
                                <li><a href="index.php?quanly=truyen&decu=tren5000">Trên 5000</a></li>
                            </ul>
                        </li>
<!-- Bộ lọc Gold -->
<!-- Dropdown Gold -->
<li class="dropdown-item-eds" onclick="toggleDropdown('gold-dropdown')">
    Gold <i class="fa-solid fa-arrow-down"></i>
    <ul id="gold-dropdown" class="dropdown-123">
        <li><a href="index.php?quanly=truyen&gold=duoi10">Dưới 10000</a></li>
        <li><a href="index.php?quanly=truyen&gold=10-100">10000 - 100000</a></li>
        <li><a href="index.php?quanly=truyen&gold=tren100">Trên 100000</a></li>
        <!-- Thêm các mức gold khác tùy thuộc vào nhu cầu -->
    </ul>
</li>
                        <li class="dropdown-item-eds">
                            <a href="index.php?quanly=truyen&like=yeuthich">Yêu thích</a>
                            <!-- Thêm nội dung cho Yêu thích tại đây -->
                        </li>

                        <li class="dropdown-item-eds">
                            <a href="index.php?quanly=truyen&binhluan=comment">Bình luận</a>
                            <!-- Thêm nội dung cho Bình luận tại đây -->
                        </li>
                        <li class="dropdown-item-eds">
                            <a href="index.php?quanly=truyen&chuong=tongchuong">Số chương</a>
                            <!-- Thêm nội dung cho Số chương tại đây -->
                        </li>
                        <!-- Các mục khác ở đây -->
                    </ul>
                </div>
                
                </div>
                </div>
                <div class="search-container">

                    <?php
                    // Kiểm tra xem có từ khóa tìm kiếm không
                    if (isset($_GET['search']) || isset($_GET['category'])) {
                        echo ' <div class="search-container">';
                        echo '<span>Tìm kiếm cho:</span>';
                        // Hiển thị từ khóa tìm kiếm
                        if (isset($_GET['search'])) {

                            echo '<span class="search-keyword">' . htmlspecialchars($_GET['search']) . '</span>';
                        }

                        // Hiển thị từ khóa thể loại
                        if (isset($_GET['category'])) {
                            $selectedKeywords = array_map('htmlspecialchars', $_GET['category']);
                            echo implode(', ', $selectedKeywords);
                        }

                        echo '</div>';
                    }
                    ?>

                </div>

                <div class="story-row">

                    <?php
                    // Thêm điều kiện WHERE để lọc theo thể loại nếu có
// Thêm điều kiện WHERE nếu có lựa chọn thể loại
                    $whereClause = "";
                    $count = "";
                    $orderClause = "";
                    $interClause = "";
                    if (isset($_GET['category'])) {
                        $selectedCategories = array_map([$mysqli, 'real_escape_string'], $_GET['category']);
                        $categoryString = implode("','", $selectedCategories);
                        $whereClause = "WHERE theloai.tentheloai IN ('$categoryString')";
                    }

                    // Thêm điều kiện WHERE cho tìm kiếm theo tên tác giả hoặc tiêu đề
                    $searchTerm = isset($_GET['search']) ? $mysqli->real_escape_string($_GET['search']) : '';
                    $whereClause .= " AND (truyen.tacgia LIKE '%$searchTerm%' OR truyen.tieude LIKE '%$searchTerm%')";

                    // Thêm điều kiện ORDER BY nếu có lựa chọn "Truyện mới" hoặc "Mới đăng"
                    $orderClause = '';
                    if (isset($_GET['moiCapNhat']) && $_GET['moiCapNhat'] == 'truyenMoi') {
                        // Nếu chọn "Truyện mới", sắp xếp theo ngày đăng của truyện
                        $orderClause = 'ORDER BY truyen.ngaydang DESC';
                    } elseif (isset($_GET['moiCapNhat']) && $_GET['moiCapNhat'] == 'moiDang') {
                        // Nếu chọn "Mới đăng", sắp xếp theo thời gian của chương
                        $interClause = 'INNER JOIN
                                        tbl_chuong chuong ON truyen.id_truyen = chuong.id_truyen';
                        $orderClause = 'ORDER BY MAX(tbl_chuong.thoigian) DESC';
                    }
                    $luotDocWhereClause = "";
                    if (isset($_GET['like']) && $_GET['like'] == 'yeuthich') {
                        // Nếu chọn "Truyện mới", sắp xếp theo ngày đăng của truyện
                        $orderClause = 'ORDER BY truyen.yeuthich DESC';
                    }
                    // Kiểm tra xem có lựa chọn lượt đọc không
                    if (isset($_GET['luotDoc'])) {
                        // Lấy giá trị lượt đọc được chọn từ URL
                        $selectedLuotDoc = $_GET['luotDoc'];

                        // Dựa vào giá trị lựa chọn để tạo điều kiện WHERE tương ứng
                        switch ($selectedLuotDoc) {
                            case 'duoi1000':
                                $luotDocWhereClause = ' AND truyen.luotdoc < 1000';
                                $orderClause .= ' ORDER BY truyen.luotdoc DESC'; // Sắp xếp tăng dần (ASC)
                                break;
                            case '1000-100000':
                                $luotDocWhereClause = ' AND truyen.luotdoc >= 1000 AND truyen.luotdoc <= 100000';
                                $orderClause .= ' ORDER BY truyen.luotdoc DESC'; // Sắp xếp tăng dần (ASC)
                                break;
                            case 'tren100000':
                                $luotDocWhereClause = ' AND truyen.luotdoc > 100000';
                                $orderClause .= ' ORDER BY truyen.luotdoc DESC'; // Sắp xếp giảm dần (DESC)
                                break;
                            case 'all':
                                $luotDocWhereClause = ' AND truyen.luotdoc > 0';
                                $orderClause .= ' ORDER BY truyen.luotdoc DESC'; // Sắp xếp giảm dần (DESC)
                                break;
                        }
                        // Thêm các trường hợp khác nếu cần
                    
                        $whereClause .= $luotDocWhereClause;
                    }
                    $decuWhereClause = "";

                    // Kiểm tra xem có lựa chọn đề cử không
                    if (isset($_GET['decu'])) {
                        // Lấy giá trị đề cử được chọn từ URL
                        $selectedDecu = $_GET['decu'];

                        // Dựa vào giá trị lựa chọn để tạo điều kiện WHERE tương ứng
                        switch ($selectedDecu) {
                            case 'duoi100':
                                $decuWhereClause = ' AND truyen.decu < 100';
                                $orderClause .= ' ORDER BY truyen.decu DESC';
                                break;
                            case '100-1000':
                                $decuWhereClause = ' AND truyen.decu >= 100 AND truyen.decu <= 1000';
                                $orderClause .= ' ORDER BY truyen.decu DESC';
                                break;
                            case '1000-5000':
                                $decuWhereClause = ' AND truyen.decu >= 1000 AND truyen.decu <= 5000';
                                $orderClause .= ' ORDER BY truyen.decu DESC';
                                break;
                            case 'tren5000':
                                $decuWhereClause = ' AND truyen.decu > 5000';
                                $orderClause .= ' ORDER BY truyen.decu DESC';
                                break;
                            case 'all':
                                $decuWhereClause = ' AND truyen.decu > 0';
                                // Nếu bạn muốn sắp xếp tất cả, thêm ORDER BY ở đây
                                $orderClause .= ' ORDER BY truyen.decu DESC';
                                break;

                            // Thêm các trường hợp khác nếu cần
                        }
                        $whereClause .= $decuWhereClause;
                    }
                    if (isset($_GET['truyen']) && $_GET['truyen'] == 'dangra') {
                        // Nếu chọn "Truyện mới", sắp xếp theo ngày đăng của truyện
                        $orderClause = ' ORDER BY truyen.ngaydang DESC';
                        $whereClause = ' AND truyen.status_tt = 0';
                    }

                    if (isset($_GET['truyen']) && $_GET['truyen'] == 'hoanthanh') {
                        // Nếu chọn "Truyện đã hoàn thành", sắp xếp theo ngày đăng của truyện
                        $orderClause = ' ORDER BY truyen.ngaydang DESC';
                        $whereClause = ' AND truyen.status_tt = 1';
                    }
                    // Kiểm tra xem có lựa chọn đề cử không
                    

                    if (isset($_GET['binhluan']) && $_GET['binhluan'] == 'comment') {
                        $count = 'COALESCE(binhluan.tong_binhluan, 0) AS tong_binhluan,';
                        $interClause = '
    LEFT JOIN (
        SELECT 
            id_truyen, 
            COUNT(*) AS tong_binhluan
        FROM 
            tbl_binhluan
        GROUP BY 
            id_truyen
    ) binhluan ON truyen.id_truyen = binhluan.id_truyen
';
                        $orderClause = 'ORDER BY tong_binhluan DESC';
                    }

                    if (isset($_GET['chuong']) && $_GET['chuong'] == 'tongchuong') {
                        $count = 'IFNULL(chuong.tong_chuong, 0) AS tong_chuong ,';
                        $interClause = 'LEFT JOIN (
        SELECT
            id_truyen,
            COUNT(*) AS tong_chuong
        FROM
            tbl_chuong
        GROUP BY
            id_truyen
    ) chuong ON truyen.id_truyen = chuong.id_truyen';
                        $orderClause = 'ORDER BY tong_chuong DESC';
                    }

                    // Thêm điều kiện WHERE vào câu truy vấn chính
                    

                    // Thêm điều kiện WHERE vào câu truy vấn chính
                    
// Bộ lọc gold
$goldWhereClause = "";
if (isset($_GET['gold'])) {
    // Lấy giá trị gold được chọn từ URL
    $selectedGold = $_GET['gold'];

    // Dựa vào giá trị lựa chọn để tạo điều kiện WHERE tương ứng
    switch ($selectedGold) {
        case 'duoi10':
            $goldWhereClause = ' AND truyen.gold < 10000';
            $orderClause .= ' ORDER BY truyen.gold DESC';

            break;
        case '10-100':
            $goldWhereClause = ' AND truyen.gold >= 10000 AND truyen.gold <= 100000';
            $orderClause .= ' ORDER BY truyen.gold DESC';

            break;
        case 'tren100':
            $goldWhereClause = ' AND truyen.gold > 100000';
            $orderClause .= ' ORDER BY truyen.gold DESC';

            break;
            case 'all':
                $goldWhereClause = ' AND truyen.gold >=0';
                $orderClause .= ' ORDER BY truyen.gold DESC';
    
                break;
            
        // Thêm các trường hợp khác nếu cần
    }
    $whereClause .= $goldWhereClause;
}
                    if (isset($_GET['danhgia'])) {
                        // Lấy giá trị đánh giá được chọn từ URL
                        $selectedDanhGia = $_GET['danhgia'];

                        // Dựa vào giá trị lựa chọn để tạo điều kiện HAVING tương ứng
                        switch ($selectedDanhGia) {
                            case 'duoi3':
                                $orderClause .= ' HAVING AVG(danhgia.diemtrungbinh) < 3 ORDER BY AVG(danhgia.diemtrungbinh) DESC';
                                break;
                            case '3-4':
                                $orderClause .= ' HAVING 
            AVG(danhgia.diemtrungbinh) >= 3 AND AVG(danhgia.diemtrungbinh) <= 4 ORDER BY AVG(danhgia.diemtrungbinh) DESC';
                                break;
                            case '4-5':
                                $orderClause .= ' HAVING 
            AVG(danhgia.diemtrungbinh) >= 4 AND AVG(danhgia.diemtrungbinh) <= 5 ORDER BY AVG(danhgia.diemtrungbinh) DESC';
                                break;
                            case 'all':
                                $orderClause .= ' HAVING AVG(danhgia.diemtrungbinh) >= 0 AND AVG(danhgia.diemtrungbinh) <= 5 ORDER BY AVG(danhgia.diemtrungbinh) DESC';
                                break;

                            // Thêm các trường hợp khác nếu cần
                        }

                        // Đảm bảo chỉ lấy những truyện có đánh giá
                        $whereClause .= ' WHERE 
    danhgia.diemtrungbinh IS NOT NULL ';

                        $count = 'AVG(danhgia.diemtrungbinh) AS diemtrungbinh,';
                        $interClause = '
    LEFT JOIN 
    (
        SELECT id_truyen, AVG(tongdiem) AS diemtrungbinh
        FROM tbl_danhgia
        GROUP BY id_truyen
    ) danhgia ON truyen.id_truyen = danhgia.id_truyen';
                    }
                    // Thực hiện truy vấn SQL
                    $truyenPerPage = 18;

// Trang hiện tại, mặc định là trang 1 nếu không có giá trị
$current_page = isset($_GET['page']) ? intval($_GET['page']) : 1;

// Tính OFFSET dựa trên trang hiện tại và số truyện trên mỗi trang
$offset = ($current_page - 1) * $truyenPerPage;
$sql = "SELECT
    truyen.id_truyen,
    truyen.tieude,
    truyen.tomtat,
    truyen.hinhanh,
    truyen.luotdoc,
    truyen.decu,
    truyen.gold,
    truyen.yeuthich,
    truyen.tacgia AS tacgia,
    $count
    MAX(theloai.tentheloai) AS theloai
FROM
    tbl_truyen truyen
INNER JOIN
    tbl_user user ON truyen.id_admin = user.id_user
INNER JOIN
    tbl_truyen_theloai tt ON truyen.id_truyen = tt.id_truyen
INNER JOIN
    tbl_theloai theloai ON tt.id_theloai = theloai.id_theloai
$interClause
$whereClause
AND truyen.truyen_status = 1
GROUP BY
    truyen.id_truyen
$orderClause
LIMIT $offset, $truyenPerPage;";

                    $result = $mysqli->query($sql);
                    ?>


                    <?php while ($row = $result->fetch_assoc()): ?>
                        <div class="story-item">
                            <div class="story-thumbnail">
                                <!-- Ảnh truyện (thay thế bằng đường dẫn đến hình ảnh từ cơ sở dữ liệu) -->
                                <img src="<?php echo $row['hinhanh']; ?>"
                                    alt="Truyện <?php echo $row['tieude'];
                                    ; ?>">
                            </div>
                            <div class="story-details">
                                <!-- Thông tin truyện -->
                                <a class="tieude"
                                    href="index.php?quanly=thongtintruyen&id_truyen=<?php echo $row['id_truyen']; ?>">
                                    <?php echo $row['tieude']; ?>
                                </a>
                                <p class="tomtat_v1">
                                    <?php echo $row['tomtat']; ?>
                                </p>
                                <p class="tacgia321"><i class="fa-solid fa-user-pen"></i>
                                    <?php echo $row['tacgia']; ?>
                                </p></br>
                                <a href="index.php?quanly=truyen&category[]=<?php echo  $row['theloai']; ?>" class="theloai321"> <?php echo $row['theloai']; ?></a></br>

                                <?php
                                if (isset($_GET['chuong']) && $_GET['chuong'] == 'tongchuong') {
                                    echo '<p class="trangthai321"><i class="fa-solid fa-bars"></i> ' . $row['tong_chuong'] . ' chương</p>';
                                }
                                elseif (isset($_GET['luotDoc'])) {
                                 echo '<p class="trangthai321"><i class="fa-solid fa-glasses"></i> ' . $row['luotdoc'] . ' lượt đọc</p>';
                                
                                } elseif (isset($_GET['decu'])) {
                                    echo '<p class="trangthai321"><i class="fa-regular fa-face-smile"></i> ' . $row['decu'] . ' đề cử</p>';
                                } elseif (isset($_GET['danhgia'])) {
                                    echo '<p class="trangthai321"><i class="fa-regular fa-star"></i> ' . number_format($row['diemtrungbinh'], 2) . ' điểm</p>';
                                }elseif(isset($_GET['like']) && $_GET['like'] == 'yeuthich') {
                                    echo '<p class="trangthai321"><i class="fa-solid fa-heart"></i> ' . $row['yeuthich'] . ' thích</p>';
                                }elseif (isset($_GET['binhluan']) && $_GET['binhluan'] == 'comment') {
                                        echo '<p class="trangthai321"><i class="fa-regular fa-comment"></i> ' . $row['tong_binhluan'] . ' comment</p>';
                                } elseif (isset($_GET['truyen']) && $_GET['truyen'] == 'hoanthanh') {
                                    echo '<p class="trangthai321"><i class="fa-solid fa-pen"></i> Trạng thái: Hoàn thành </p>';
                                } elseif (isset($_GET['truyen']) && $_GET['truyen'] == 'dangra') {
                                    echo '<p class="trangthai321"><i class="fa-solid fa-pen"></i> Trạng thái: Đang ra </p>';

                                }elseif (isset($_GET['gold'])) {
                                    echo '<p class="trangthai321"><i class="fa-solid fa-piggy-bank"></i> ' . $row['gold'] . ' gold</p>';
                                }
                                ?>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
                <div class="pagination">
    <?php
    // Tính tổng số truyện dựa trên điều kiện lọc
    $count_sql = "SELECT COUNT(DISTINCT truyen.id_truyen) AS total FROM tbl_truyen truyen INNER JOIN tbl_truyen_theloai tt ON truyen.id_truyen = tt.id_truyen INNER JOIN tbl_theloai theloai ON tt.id_theloai = theloai.id_theloai $interClause $whereClause";
    $count_result = $mysqli->query($count_sql);
    $row = $count_result->fetch_assoc();
    $total_truyen = $row['total'];

    // Tính tổng số trang
    $total_pages = ceil($total_truyen / $truyenPerPage);

    // Hiển thị nút phân trang
    for ($i = 1; $i <= $total_pages; $i++) {
        $activeClass = ($i == $current_page) ? 'active' : '';
        $filterParams = $_GET; // Lấy các tham số lọc từ URL
        $filterParams['page'] = $i; // Thêm tham số page vào mảng
        $pageLink = "index.php?" . http_build_query($filterParams); // Tạo URL từ mảng tham số
        echo "<a class='page-link $activeClass' href='$pageLink'>$i</a>";
    }
    ?>
</div>



            </div>
        </div>
    </div>
</main>
<script>
    
document.getElementById('show-dropdown').addEventListener('click', function() {
    var content = document.getElementById('dropdown-content').cloneNode(true);
    content.style.display = 'block'; // Đảm bảo nội dung được hiển thị

    var dropdownContainer = document.createElement('div');
    dropdownContainer.id = 'dropdown-container';
    dropdownContainer.style.left = '100px'; // Vị trí tùy chỉnh
    dropdownContainer.style.top = '50px'; // Vị trí tùy chỉnh

    dropdownContainer.appendChild(content);
    document.body.appendChild(dropdownContainer);
});
</script>

<script>
    
    // Sử dụng một mảng để lưu trữ các thể loại được chọn
    var selectedCategories = [];

    function toggleCategory(category) {
        // Kiểm tra xem thể loại đã được chọn chưa
        var index = selectedCategories.indexOf(category);

        if (index !== -1) {
            // Nếu đã chọn, hãy loại bỏ khỏi mảng
            selectedCategories.splice(index, 1);
        } else {
            // Nếu chưa chọn, thêm vào mảng
            selectedCategories.push(category);
        }

        // Cập nhật hiển thị
        updateDisplay();
    }

    function updateDisplay() {
        // Lấy thẻ span để hiển thị từ khóa
        var selectedKeywordsList = document.getElementById('selectedKeywordsList');

        // Xóa các từ khóa hiện tại
        selectedKeywordsList.innerHTML = "";

        // Hiển thị các thể loại được chọn
        selectedCategories.forEach(function (category) {
            var keywordSpan = document.createElement('span');
            keywordSpan.textContent = category;
            selectedKeywordsList.appendChild(keywordSpan);
            // Thêm dấu phẩy nếu không phải là từ khóa cuối cùng
            if (selectedCategories.indexOf(category) !== selectedCategories.length - 1) {
                selectedKeywordsList.appendChild(document.createTextNode(', '));
            }
        });
    }
</script>



<script>
document.addEventListener('DOMContentLoaded', function () {
    // Khi toàn bộ nội dung đã được tải, thiết lập các sự kiện cho dropdowns
    setupDropdowns();
});

function setupDropdowns() {
    // Chọn tất cả các phần tử có class 'dropdown-item-eds' và gắn sự kiện onclick
    var dropdownItems = document.querySelectorAll('.dropdown-item-eds');
    dropdownItems.forEach(function (item) {
        item.onclick = function () {
            toggleDropdown(this);
        };
    });
}

var openDropdown = null;

function toggleDropdown(element) {
    // Tìm dropdown content gần nhất với phần tử được click
    var dropdown = element.querySelector('.dropdown-123');

    if (!dropdown) {
        console.error("No dropdown content found for", element);
        return;
    }

    // Kiểm tra xem có dropdown nào đang mở không và không phải là dropdown hiện tại
    if (openDropdown && openDropdown !== dropdown) {
        openDropdown.style.display = 'none';
    }

    // Bật / Tắt hiển thị cho dropdown hiện tại
    if (dropdown.style.display === 'block') {
        dropdown.style.display = 'none';
        openDropdown = null;
    } else {
        dropdown.style.display = 'block';
        openDropdown = dropdown;
    }

    // Đặt class 'active' cho phần tử hiện tại và loại bỏ khỏi các phần tử khác
    setActive(element);
}

function setActive(element) {
    // Xóa class 'active' khỏi tất cả các sibling
    var siblingItems = element.parentNode.children;
    for (var i = 0; i < siblingItems.length; i++) {
        siblingItems[i].classList.remove('active');
    }

    // Thêm class 'active' cho phần tử được click
    element.classList.add('active');
}

</script>