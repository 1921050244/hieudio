<?php

// Kết nối đến cơ sở dữ liệu
include('./admin/config/config.php');

// Truy vấn CSDL để lấy danh sách thể loại
$query = "SELECT * FROM tbl_theloai";
$result = $mysqli->query($query);

// Tạo mảng để chứa dữ liệu thể loại
$theloaiArray = [];

// Kiểm tra và xử lý kết quả
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $theloaiArray[] = $row['tentheloai'];
    }
}

// Đóng kết nối
$mysqli->close();

// Bắt đầu session

?>
<header class="site-header">
    <nav class="main-nav">
    <div class="nav-mobile123">
        
    <div class="nav-mobile">


    <button class="button-timkiem search-button" type="button" onclick="toggleSearch()">
                <i class="fa-solid fa-magnifying-glass"></i>
            </button>
            
            <div class="nav-item logo">
                <a href="index.php" class="logo"><img src="./assets/image/logo.png" alt="Logo"></a>
            </div>
            <div class="nav-items">
            <button type="button" class="nav-button" onclick="toggleNav()"><i class="fa-solid fa-bars"></i></button>
        </div>
    </div>
<div class="nav-lists" id="navList" style="display: none;">
    <button type="button" class="close-nav" onclick="closeNav()">&times;</button>
    <ul class="nav-menu">
        <!-- Logo -->
        <li class="nav-item logo">
            <a href="index.php" class="nav-link123">
                <img src="./assets/image/logo.png" alt="Logo" class="logo-img">
            </a>
        </li>

        <!-- Đăng nhập/Dang xuất -->
        <?php if (isset($_SESSION['id_user'])): ?>
            <li class="nav-item234 has-dropdown">
    <a href="index.php?quanly=thongtintaikhoan" class="nav-link123 toggleCollapse">
    <?php
// Kiểm tra xem ảnh có phải là một liên kết không
function checkImageLink($image) {
    return filter_var($image, FILTER_VALIDATE_URL) === FALSE ? "./assets/image/" . $image : $image;
}
?>

<!-- Sử dụng hàm checkImageLink để kiểm tra và điều chỉnh đường dẫn ảnh -->
<img src="<?php echo checkImageLink($_SESSION['avatar']); ?>" alt="Avatar" class="avatar-img">
        <?php echo $_SESSION['tenuser']; ?>
     
    </a>
    <div class="dropdown-menus">
        <a href="index.php?quanly=thongtintaikhoan"><i class="fa-regular fa-user"></i> Hồ sơ</a>
        <a href="index.php?quanly=taisan"><i class="fa-solid fa-piggy-bank"></i> Tài sản</a>
        <a href="index.php?quanly=muagold"><i class="fa-regular fa-thumbs-up"></i> Mua gold</a>
        <a href="index.php?quanly=tutruyen"><i class="fa-solid fa-book-open"></i> Tủ truyện</a>
    </div>
</li>


            <li class="nav-item234">
                <a href="index.php?quanly=dangxuat" class="nav-link123"><i class="fa-solid fa-arrow-right-from-bracket"></i> Đăng Xuất</a>
            </li>
        <?php else: ?>
            <li class="nav-item234">
                <a href="index.php?quanly=dangnhap" class="nav-link123">Đăng Nhập</a>
            </li>
            <li class="nav-item234">
                <a href="index.php?quanly=dangki" class="nav-link123">Đăng Kí</a>
            </li>
        <?php endif; ?>

        <!-- Thể loại -->
        <li class="nav-item234 has-dropdown">
        <a href="#" class="nav-link123 toggleCollapse">Thể Loại <i class="fa-solid fa-chevron-down"></i></a>
                    <?php if (!empty($theloaiArray)): ?>
                <ul class="dropdown-menu">
                    <?php foreach ($theloaiArray as $theloai) { ?>
                        <li><a href="index.php?quanly=truyen&category[]=<?php echo $theloai; ?>"><?php echo $theloai; ?></a></li>
                    <?php } ?>
                </ul>
            <?php else: ?>
                <p>Không có dữ liệu thể loại</p>
            <?php endif; ?>
        </li>

        <!-- Thịnh hành -->
        <li class="nav-item234 has-dropdown">
        <a href="#" class="nav-link123 toggleCollapse">Thịnh Hành <i class="fa-solid fa-chevron-down"></i></a>
                  <ul class="dropdown-menu">
                <li><a href="index.php?quanly=truyen&moiCapNhat=truyenMoi">Mới cập nhật</a></li>
                <li><a href="index.php?quanly=truyen&luotDoc=all">Lượt đọc</a></li>
                <li><a href="index.php?quanly=truyen&danhgia=all">Đánh giá</a></li>
                <li><a href="index.php?quanly=truyen&gold=all">Gold</a></li>
                <li><a href="index.php?quanly=truyen&like=yeuthich">Yêu thích</a></li>
                <li><a href="index.php?quanly=truyen&binhluan=comment">Bình luận</a></li>
            </ul>
        </li>

        <!-- Đăng truyện (admin) -->
        <li class="nav-item234">
            <a href="admin/index.php?action=trangchu&query=home" class="nav-link123"><i class="fa-regular fa-circle-up"></i> Đăng Truyện</a>
        </li>
    </ul>
</div>


    </div>
    </div>
    
    <input id="searchContainer" class="timkiem search-input" type="text" placeholder="Tìm kiếm..." style="display: none;"
                        onkeypress="handleKeyPress(event) " >  
                        
        <ul class="nav-list">
            <ul class="nav-list">
                <li class="nav-item logo">
                    <a href="index.php" class="logo"><img src="./assets/image/logo.png" alt="Logo"></a>
                </li>
                <li class="nav-item">
                    <a href="index.php?quanly=truyen" class="nav-link"><i class="fa-solid fa-bars"></i> Thể Loại</a>
                    <!-- Thể loại dropdown -->
                    <?php if (!empty($theloaiArray)): ?>
                        <ul class="dropdown">
                            <?php foreach ($theloaiArray as $theloai) { ?>
                                <li><a href="index.php?quanly=truyen&category[]=<?php echo $theloai; ?>">
                                        <?php echo $theloai; ?>
                                    </a></li>
                            <?php } ?>

                        </ul>
                    <?php else: ?>
                        <p>Không có dữ liệu thể loại</p>
                    <?php endif; ?>
                </li>
                <li class="nav-item">
                    <a href="#" class="nav-link">Thịnh Hành</a>
                    <ul class="dropdowns">
                        <li><a href="index.php?quanly=truyen&moiCapNhat=truyenMoi">Thịnh hành</a></li>
                        <li><a href="index.php?quanly=truyen&luotDoc=all">Đọc nhiều</a></li>
                        <li><a href="index.php?quanly=truyen&danhgia=all">Đánh giá</a></li>
                        <li><a href="index.php?quanly=truyen&gold=all">Gold</a></li>
                        <li><a href="index.php?quanly=truyen&like=yeuthich">Yêu thích</a></li>
                        <li><a href="index.php?quanly=truyen&binhluan=comment">Thảo luận</a></li>
                    </ul>
                </li>
                <li class="nav-item-search-box">
                    <input id="searchInput" class="timkiem search-input" type="text" placeholder="Tìm kiếm..."
                        onkeypress="handleKeyPress(event)">
                    <button class="button-timkiem search-button" type="button" onclick="search()"><i
                            class="fa-solid fa-magnifying-glass"></i></button>
                </li>

                <li class="nav-item">
                    <a href="admin/index.php?action=trangchu&query=home" class="nav-link"><i
                            class="fa-regular fa-circle-up"></i> Đăng Truyện</a>
                </li>
                <!-- Thanh điều hướng -->
                <!-- Các mục khác ... -->

                <!-- Trong header.php -->
                <?php if (isset($_SESSION['id_user'])): ?>
                    <li class="nav-item">
                        <a href="index.php?quanly=thongtintaikhoan" class="nav-link">
                        <?php
$avatar = $_SESSION['avatar'];
// Kiểm tra xem ảnh có phải là một đường dẫn đầy đủ không
if (filter_var($avatar, FILTER_VALIDATE_URL)) {
    // Nếu là một đường dẫn đầy đủ, loại bỏ phần "./assets/image/" nếu có
    $avatar = str_replace('./assets/image/', '', $avatar);
} else {
    // Nếu không phải là một đường dẫn đầy đủ, thêm phần "./assets/image/" vào trước
    $avatar = './assets/image/' . $avatar;
}
?>
<img src="<?php echo $avatar; ?>" alt="Avatar" class="avatar-img">
                            <?php echo $_SESSION['tenuser']; ?>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="index.php?quanly=dangxuat" class="nav-link">Đăng Xuất</a>
                    </li>
                <?php else: ?>
                    <li class="nav-item">
                        <a href="index.php?quanly=dangnhap" class="nav-link">Đăng Nhập</a>
                    </li>
                    <li class="nav-item">
                        <a href="index.php?quanly=dangki" class="nav-link">Đăng Kí</a>
                    </li>
                <?php endif;

                ?>

            </ul>
  
    </nav>


</header>
<!-- Đoạn JavaScript để thực hiện tìm kiếm -->
<script>
    
document.addEventListener('DOMContentLoaded', function() {
    // Get all elements that should toggle the dropdowns
    var dropdownToggles = document.querySelectorAll('.toggleCollapse');

    // Add click event listener to each toggle
    dropdownToggles.forEach(function(toggle) {
        toggle.addEventListener('click', function(event) {
            // Prevent default anchor click behavior
            event.preventDefault();

            // Toggle the display of the dropdown menu which is a sibling of the toggle
            var dropdownMenu = this.nextElementSibling;

            // Check if the clicked element is the anchor inside the list item
            if (!dropdownMenu) {
                // If not, find the dropdown menu within the parent element
                dropdownMenu = this.parentNode.querySelector('.dropdown-menu');
            }

            var arrowIcon = this.querySelector('i.fa-chevron-down') || this.parentNode.querySelector('i.fa-chevron-down');

            if (dropdownMenu && arrowIcon) {
                if (dropdownMenu.style.display === 'flex') {
                    dropdownMenu.style.display = 'none';
                    arrowIcon.style.transform = ''; // Reset rotation of the icon
                } else {
                    dropdownMenu.style.display = 'flex';
                    arrowIcon.style.transform = 'rotate(180deg)'; // Rotate icon 180 degrees
                }
            }
        });
    });
});
    function handleKeyPress(event) {
        // Kiểm tra xem phím Enter (keyCode 13) đã được nhấn không
        if (event.keyCode === 13) {
            // Gọi hàm search() khi Enter được nhấn
            search();
        }
    }

    function search() {
    // Lấy giá trị từ ô nhập liệu tìm kiếm
    var searchInputValue = document.getElementById('searchInput').value.trim().toLowerCase();
    var searchContainerValue = document.getElementById('searchContainer').value.trim().toLowerCase();

    // Kết hợp cả hai giá trị nhập liệu tìm kiếm
    var searchTerm = searchInputValue || searchContainerValue;

    if (searchTerm !== '') {
        // Chuyển hướng đến trang kết quả tìm kiếm với tham số truyền vào
        window.location.href = 'index.php?quanly=truyen&search=' + encodeURIComponent(searchTerm);
    }
}

    function toggleSearch() {
    var searchContainer = document.getElementById('searchContainer');
    var searchInput = document.getElementById('searchInput');
    var siteHeader = document.querySelector('.site-header'); // Lấy phần tử .site-header

    if (searchContainer.style.display === 'none' || searchContainer.style.display === '') {
        // Nếu searchContainer đang ẩn hoặc không có thuộc tính display
        searchContainer.style.display = 'block'; // Hiển thị searchContainer
        searchInput.style.display = 'block'; // Hiển thị searchInput
        siteHeader.classList.add('expanded'); // Thêm class .expanded để thay đổi giao diện .site-header
    } else {
        // Nếu searchContainer đang hiển thị
        searchContainer.style.display = 'none'; // Ẩn searchContainer
        searchInput.style.display = 'none'; // Ẩn searchInput
        siteHeader.classList.remove('expanded'); // Xóa class .expanded để trở về giao diện .site-header bình thường
    }
}

// Chỉ giữ lại một hàm toggleNav()
function toggleNav() {
    var navList = document.getElementById('navList');

    // Kiểm tra xem lớp 'show' có được thêm vào không
    var isNavVisible = navList.classList.toggle('show');

    // Nếu 'show' được thêm vào, hiển thị navList
    if (isNavVisible) {
        navList.style.display = 'block';
    } else {
        // Ngược lại, ẩn navList
        navList.style.display = 'none';
    }
}

function closeNav() {
    var navList = document.getElementById('navList');

    // Loại bỏ lớp 'show' và ẩn navList
    navList.classList.remove('show');
    navList.style.display = 'none';
}

</script>