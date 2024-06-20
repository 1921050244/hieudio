<?php
// Link trang web cần crawl
$url = "https://truyenfull.vn/than-dao-dan-ton-606028/chuong-5343/";

// Sử dụng cURL để lấy nội dung từ trang web
$curl = curl_init($url);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
$html = curl_exec($curl);
curl_close($curl);

// Sử dụng DOMDocument để parse HTML
$dom = new DOMDocument();
@$dom->loadHTML($html);

// Lấy nội dung từ thẻ div có id là "chapter-c"
$chapterDiv = $dom->getElementById("chapter-c");

// Kiểm tra xem có thẻ div hay không và hiển thị nội dung
if ($chapterDiv) {
    $content = $chapterDiv->nodeValue;
    echo $content;
} else {
    echo "Không thể lấy nội dung từ trang web.";
}
?>
