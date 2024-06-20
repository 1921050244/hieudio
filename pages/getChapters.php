<?php
include('../admin/config/config.php');

$id_truyen = intval($_POST['id_truyen']);
$order = $_POST['order'] === 'desc' ? 'DESC' : 'ASC';
$current_chuong = intval($_POST['current_chuong']);

// Truy vấn lấy danh sách chương
$queryChapters = "
    SELECT id_chuong, tenchuong, sochuong
    FROM tbl_chuong
    WHERE id_truyen = $id_truyen
    ORDER BY sochuong $order;
";

$resultChapters = $mysqli->query($queryChapters);

if ($resultChapters && $resultChapters->num_rows > 0) {
    echo '<ul class="chapter-list">';
    while ($chapter = $resultChapters->fetch_assoc()) {
        $isCurrentChapter = ($chapter['id_chuong'] == $current_chuong);
        $chapterClass = $isCurrentChapter ? 'current-chapter' : 'chapter-item';
        echo '<li class="' . $chapterClass . '">';
        echo '<a href="index.php?quanly=doc&id_truyen=' . $id_truyen . '&id_chuong=' . $chapter['id_chuong'] . '">Chương ' . $chapter['sochuong'] . ': ' . $chapter['tenchuong'] . '</a>';
  
    }
    echo '</ul>';
} else {
    echo '<p>Chưa có chương nào.</p>';
}
?>
