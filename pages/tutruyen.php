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
  <div class="container_vc123">
    
  <?php
    include('thongtinsidebar.php')
    ?>
        <div class="right-column_vc123">

        <div class="column-123">
        <div class="button-group">
          <!-- Buttons for switching between Currently Reading and Bookshelf -->
          <div class="button-group">
          <!-- Buttons for switching between Currently Reading and Bookshelf -->
          <button id="currentlyReadingBtn" class="active">Đang đọc</button>
          <button id="bookshelfBtn">Tủ truyện</button>
        </div
        </div>
        <div id="currentlyReadingContent" style="display: block;">
                <h3 class="thehh">Đang đọc</h3>
        <div class="story-row">
        <?php

// Kết nối đến CSDL (Giả sử bạn đã kết nối rồi)
// ...

if (isset($_SESSION['id_user'])) {
    $id_user = $_SESSION['id_user'];

    // Lấy thông tin từ CSDL về truyện đã đọc của người dùng
    $query_read_stories = "SELECT 
        truyen.id_truyen, 
        truyen.tieude, 
        truyen.hinhanh, 
        MAX(chuong.sochuong) AS tong_so_chuong,
        chuong_doc.id_chuong AS id_chuong_doc,
        chuong_doc.sochuong AS so_chuong_doc
    FROM 
        tbl_truyen AS truyen
    LEFT JOIN 
        tbl_reading_status AS reading ON reading.id_truyen = truyen.id_truyen
    LEFT JOIN 
        tbl_chuong AS chuong ON truyen.id_truyen = chuong.id_truyen
    LEFT JOIN 
        tbl_chuong AS chuong_doc ON reading.id_chuong = chuong_doc.id_chuong
    WHERE 
        reading.id_user = $id_user
    GROUP BY 
        truyen.id_truyen, truyen.tieude, truyen.hinhanh, id_chuong_doc, so_chuong_doc limit 5
    ";
    $result_read_stories = mysqli_query($mysqli, $query_read_stories);

    // Kiểm tra và hiển thị danh sách truyện đã đọc
    if ($result_read_stories && mysqli_num_rows($result_read_stories) > 0) :
        while ($row_story = mysqli_fetch_assoc($result_read_stories)) :
?>
        <div class="story-items">
            <div class="story-thumbnailss">
                <!-- Ảnh truyện -->
                <img style="width:35px;" src="<?php echo $row_story['hinhanh']; ?>" alt="<?php echo $row_story['tieude']; ?>">
            </div>
            <div class="story-details">
                <!-- Thông tin truyện -->
                <a class="tieude" href="index.php?quanly=thongtintruyen&id_truyen=<?php echo $row_story['id_truyen']; ?>">
                    <?php echo $row_story['tieude']; ?>
                    <a href="index.php?quanly=doc&id_truyen=<?php echo $row_story['id_truyen']; ?>&id_chuong=<?php echo $row_story['id_chuong_doc']; ?>" class="read-more">Đọc tiếp</a>
                </a>
                <p>Đã Đọc <?php echo $row_story['so_chuong_doc']; ?>/<?php echo $row_story['tong_so_chuong']; ?> <a href="index.php?quanly=xuly&id_truyen=<?php echo $row_story['id_truyen']; ?>&id_chuong=<?php echo $row_story['id_chuong_doc']; ?>" ><i class="fa-regular fa-trash-can"></i></a></p>
            </div>
        </div>


<?php
        endwhile;
    endif;
} elseif (isset($_COOKIE['user_id'])) {
    $id_user = $_COOKIE['user_id'];

    // Lấy thông tin từ CSDL về truyện đã đọc của người dùng (sử dụng $id_user)
    $query_read_stories_cookie = "SELECT 
                                        truyen.id_truyen, 
                                        truyen.tieude, 
                                        truyen.hinhanh, 
                                        COUNT(DISTINCT reading.id_chuong) AS so_chuong_doc, 
                                        MAX(chuong.sochuong) AS tong_so_chuong
                                   FROM tbl_reading_status AS reading
                                   INNER JOIN tbl_truyen AS truyen ON reading.id_truyen = truyen.id_truyen
                                   LEFT JOIN tbl_chuong AS chuong ON reading.id_chuong = chuong.id_chuong AND truyen.id_truyen = chuong.id_truyen
                                   WHERE reading.id_user = $id_user
                                   GROUP BY truyen.id_truyen, truyen.tieude, truyen.hinhanh";
    $result_read_stories_cookie = mysqli_query($mysqli, $query_read_stories_cookie);

    // Kiểm tra và hiển thị danh sách truyện đã đọc từ cookie
    if ($result_read_stories_cookie && mysqli_num_rows($result_read_stories_cookie) > 0) :
        while ($row_story_cookie = mysqli_fetch_assoc($result_read_stories_cookie)) :
?>
            <div class="story-items">
                <div class="story-thumbnailss">
                    <!-- Ảnh truyện -->
                    <img style="width:35px;" src="<?php echo $row_story_cookie['hinhanh']; ?>" alt="<?php echo $row_story_cookie['tieude']; ?>">
                </div>
                <div class="story-details">
                    <!-- Thông tin truyện -->
                    <a class="tieude" href="index.php?quanly=thongtintruyen&id_truyen=<?php echo $row_story_cookie['id_truyen']; ?>"><?php echo $row_story_cookie['tieude']; ?></a>
                    <p>Đã Đọc <?php echo $row_story_cookie['so_chuong_doc']; ?>/<?php echo $row_story_cookie['tong_so_chuong']; ?></p>
                </div>
            </div>
            </div>

<?php
        endwhile;
    endif;
}

?>
        </div>
        </div>

        <?php
// Kiểm tra xem người dùng đã đăng nhập hay chưa
if (isset($_SESSION['id_user'])) {
    $id_user = $_SESSION['id_user'];

    // Truy vấn CSDL để lấy danh sách các truyện đã đánh dấu bởi người dùng hiện tại
    $query_bookmarks = "SELECT d.id_danhdau, d.id_truyen, d.id_chuong, d.thoigian, 
                        t.tieude, t.hinhanh, c.sochuong AS so_chuong_doc, 
                        (SELECT MAX(sochuong) FROM tbl_chuong WHERE id_truyen = t.id_truyen) AS tong_so_chuong
                        FROM tbl_danhdau AS d
                        INNER JOIN tbl_truyen AS t ON d.id_truyen = t.id_truyen
                        LEFT JOIN tbl_chuong AS c ON d.id_chuong = c.id_chuong
                        WHERE d.id_user = $id_user
                        ORDER BY d.thoigian DESC";

    $result_bookmarks = mysqli_query($mysqli, $query_bookmarks);

    // Kiểm tra và hiển thị danh sách trang đã lưu
    if ($result_bookmarks && mysqli_num_rows($result_bookmarks) > 0) {
?>
<div id="bookshelfContent" style="display: none;" class="column-123">
    <h3 class="thehh">Tủ truyện</h3>
    <div class="story-row">
        <?php while ($row_bookmark = mysqli_fetch_assoc($result_bookmarks)): ?>
            <div class="story-items">
                <div class="story-thumbnailss">
                    <img style="width: 35px;" src="<?php echo htmlspecialchars($row_bookmark['hinhanh']); ?>" alt="<?php echo htmlspecialchars($row_bookmark['tieude']); ?>">
                </div>
                <div class="story-details">
                    <a class="tieude" href="index.php?quanly=thongtintruyen&id_truyen=<?php echo $row_bookmark['id_truyen']; ?>">
                        <?php echo htmlspecialchars($row_bookmark['tieude']); ?>
                    </a>
                    <p>
                        Đã Đọc <?php echo $row_bookmark['so_chuong_doc']; ?>/<?php echo $row_bookmark['tong_so_chuong']; ?>
                        <a href="index.php?quanly=doc&id_truyen=<?php echo $row_bookmark['id_truyen']; ?>&id_chuong=<?php echo $row_bookmark['id_chuong']; ?>" class="read-more">Đọc tiếp</a>
                        <a href="index.php?quanly=xuly&id_truyen=<?php echo $row_bookmark['id_truyen']; ?>&id_chuong=<?php echo $row_bookmark['id_chuong']; ?>&action=delete" class="delete">Xóa</a>
                    </p>
                </div>
            </div>
        <?php endwhile; ?>
    </div>
</div>
<?php
    } else {
?>
<div class="container_vc123">
    <div class="right-column_vc123">
        <div class="column-123">
            <h3 class="thehh">Truyện đã lưu</h3>
            <p>Chưa có truyện đánh dấu.</p>
        </div>
    </div>
</div>
<?php
    }
} else {
?>
<div class="container_vc123">
    <div class="right-column_vc123">
        <div class="column-123">
            <h3 class="thehh">Trang đã lưu</h3>
            <p>Vui lòng đăng nhập để xem trang đã lưu.</p>
        </div>
    </div>
</div>
<?php
}
?>

</div>
    </div>
        </div>
    </div>
    </div>

</main>
<script>
document.addEventListener("DOMContentLoaded", function() {
    const currentlyReadingBtn = document.getElementById('currentlyReadingBtn');
    const bookshelfBtn = document.getElementById('bookshelfBtn');
    
    const currentlyReadingContent = document.getElementById('currentlyReadingContent');
    const bookshelfContent = document.getElementById('bookshelfContent');
    
    currentlyReadingBtn.addEventListener('click', function() {
        currentlyReadingBtn.classList.add('active');
        bookshelfBtn.classList.remove('active');
        currentlyReadingContent.style.display = 'block';
        bookshelfContent.style.display = 'none';
    });
    
    bookshelfBtn.addEventListener('click', function() {
        bookshelfBtn.classList.add('active');
        currentlyReadingBtn.classList.remove('active');
        currentlyReadingContent.style.display = 'none';
        bookshelfContent.style.display = 'block';
    });
});
document.addEventListener("DOMContentLoaded", function() {
    const currentlyReadingBtn = document.getElementById('currentlyReadingBtn');
    const bookshelfBtn = document.getElementById('bookshelfBtn');
    
    currentlyReadingBtn.addEventListener('click', function() {
        currentlyReadingBtn.classList.add('selected');
        bookshelfBtn.classList.remove('selected');
        // Cập nhật hiển thị nội dung phía dưới nếu cần
    });
    
    bookshelfBtn.addEventListener('click', function() {
        bookshelfBtn.classList.add('selected');
        currentlyReadingBtn.classList.remove('selected');
        // Cập nhật hiển thị nội dung phía dưới nếu cần
    });
});
</script>