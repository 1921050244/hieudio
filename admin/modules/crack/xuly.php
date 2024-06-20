<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <title>Crack</title>
</head>
<body>
    <div class="container mt-5">
        <h2>Crack từ TRUYENFULL</h2>
        <form action="" method="POST">
            <div class="form-group">
                <label for="duong_dan">Đường dẫn:</label>
                <input type="text" class="form-control" id="duong_dan" name="duong_dan" required>
            </div>
            <button type="submit" name="themtruyen" class="btn btn-primary">Thêm truyện</button>
        </form>
    </div>
</body>
</html>
<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

function getHTMLContent($url) {
    $htmlContent = '';
    $maxAttempts = 10;
    $attempt = 0;

    while ($attempt < $maxAttempts) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        $htmlContent = curl_exec($ch);
        curl_close($ch);

        if ($htmlContent !== false) {
            break;
        }
        $attempt++;
        sleep(2); // Delay between attempts
    }

    return $htmlContent;
}

function extractStoryInfo($htmlContent) {
    if (empty($htmlContent)) {
        return null;
    }

    $dom = new DOMDocument();
    libxml_use_internal_errors(true);
    if (!$dom->loadHTML(mb_convert_encoding($htmlContent, 'HTML-ENTITIES', 'UTF-8'))) {
        libxml_clear_errors();
        return null;
    }
    libxml_use_internal_errors(false);

    $xpath = new DOMXPath($dom);

    $info = [
        'title' => '',
        'description' => '',
        'author' => '',
        'genres' => '',
        'image' => '',
        'chapters' => []
    ];

    $titleNode = $xpath->query("//h3[@itemprop='name']");
    if ($titleNode->length > 0) {
        $info['title'] = trim($titleNode->item(0)->nodeValue);
    }

    $descriptionNode = $xpath->query("//div[@itemprop='description']");
    if ($descriptionNode->length > 0) {
        $info['description'] = trim($descriptionNode->item(0)->nodeValue);
    }

    $authorNode = $xpath->query("//a[@itemprop='author']");
    if ($authorNode->length > 0) {
        $info['author'] = trim($authorNode->item(0)->nodeValue);
    }

    $genres = [];
    $genreNodes = $xpath->query("//a[@itemprop='genre']");
    foreach ($genreNodes as $node) {
        $genres[] = trim($node->nodeValue);
    }
    $info['genres'] = $genres;

    $imageNode = $xpath->query("//div[@class='book']/img/@src");
    if ($imageNode->length > 0) {
        $info['image'] = $imageNode->item(0)->nodeValue;
    }

    $chapterNodes = $xpath->query("//ul[@class='l-chapters']/li/a");
    foreach ($chapterNodes as $node) {
        $chapterUrl = $node->getAttribute('href');
        $chapterTitle = $node->nodeValue;

        if (preg_match('/Chương (\d+):/', $chapterTitle, $matches)) {
            $chapterNumber = intval($matches[1]);
            $info['chapters'][] = [
                'title' => $chapterTitle,
                'url' => $chapterUrl,
                'number' => $chapterNumber
            ];
        }
    }

    return $info;
}

function checkStoryExistence($title) {
    global $mysqli;
    $escapedTitle = $mysqli->real_escape_string($title);
    $query = "SELECT id_truyen FROM tbl_truyen WHERE tieude = '$escapedTitle'";
    $result = $mysqli->query($query);
    return ($result && $result->num_rows > 0) ? $result->fetch_assoc()['id_truyen'] : false;
}

function fetchChapterContent($url) {
    $htmlContent = getHTMLContent($url);

    if (empty($htmlContent)) {
        return "Sẽ cập nhật chương này sau!";
    }

    $domChapter = new DOMDocument();
    libxml_use_internal_errors(true);
    if (!$domChapter->loadHTML(mb_convert_encoding($htmlContent, 'HTML-ENTITIES', 'UTF-8'))) {
        libxml_clear_errors();
        return "Sẽ cập nhật chương này sau!";
    }
    libxml_use_internal_errors(false);

    $xpathChapter = new DOMXPath($domChapter);
    $contentNode = $xpathChapter->query("//div[@id='chapter-c']");
    return $contentNode->length > 0 ? $domChapter->saveHTML($contentNode->item(0)) : "Sẽ cập nhật chương này sau!";
}

function getGenreId($genre) {
    global $mysqli;
    $escapedGenre = $mysqli->real_escape_string($genre);
    $query = "SELECT id_theloai FROM tbl_theloai WHERE tentheloai = '$escapedGenre'";
    $result = $mysqli->query($query);

    if ($result && $result->num_rows > 0) {
        return $result->fetch_assoc()['id_theloai'];
    } else {
        $query = "INSERT INTO tbl_theloai (tentheloai) VALUES ('$escapedGenre')";
        if ($mysqli->query($query) === TRUE) {
            return $mysqli->insert_id;
        } else {
            return false;
        }
    }
}

function insertStoryGenres($storyId, $genres) {
    global $mysqli;
    foreach ($genres as $genre) {
        $genreId = getGenreId($genre);
        if ($genreId !== false) {
            $query = "INSERT INTO tbl_truyen_theloai (id_truyen, id_theloai) VALUES ($storyId, $genreId)";
            $mysqli->query($query);
        }
    }
}

if (isset($_POST['themtruyen']) && isset($_POST['duong_dan'])) {
    $url = $_POST['duong_dan'];
    $htmlContent = getHTMLContent($url);

    if (!empty($htmlContent)) {
        $storyInfo = extractStoryInfo($htmlContent);

        if ($storyInfo) {
            $title = $mysqli->real_escape_string($storyInfo['title']);
            $description = $mysqli->real_escape_string($storyInfo['description']);
            $author = $mysqli->real_escape_string($storyInfo['author']);
            $imageSrc = $mysqli->real_escape_string($storyInfo['image']);
            $id_admin = $_SESSION['id_user']; // Assuming you have user sessions enabled

            $existingStoryId = checkStoryExistence($title);

            if ($existingStoryId !== false) {
                $idTruyen = $existingStoryId;
                $sqlUpdateTruyen = "UPDATE tbl_truyen SET tomtat = '$description', tacgia = '$author' WHERE id_truyen = $idTruyen";

                if ($mysqli->query($sqlUpdateTruyen) === TRUE) {
                    echo "Cập nhật thông tin truyện thành công.\n";

                    $queryMaxChapter = "SELECT MAX(sochuong) AS max_chapter FROM tbl_chuong WHERE id_truyen = $idTruyen";
                    $resultMaxChapter = $mysqli->query($queryMaxChapter);
                    $maxChapterInDB = $resultMaxChapter->fetch_assoc()['max_chapter'];

                    $maxChapterNumber = max($maxChapterInDB, 100000);

                    $emptyChapterCount = 0; // Initialize the counter for empty chapters

                    for ($i = $maxChapterInDB + 1; $i <= $maxChapterNumber; $i++) {
                        $chapterUrl = "{$url}chuong-$i/";
                        $content = fetchChapterContent($chapterUrl);

                        $content = $mysqli->real_escape_string($content);

                        $sqlChuong = "INSERT INTO tbl_chuong (tenchuong, noidung, sochuong, thoigian, id_truyen, is_locked) VALUES ('Chương $i', '$content', $i, NOW(), $idTruyen, 0) ON DUPLICATE KEY UPDATE noidung = VALUES(noidung)";

                        if ($mysqli->query($sqlChuong) === TRUE) {
                            echo "Thêm hoặc cập nhật chương $i thành công.\n";
                        } else {
                            echo "Lỗi khi thêm hoặc cập nhật chương $i: " . $mysqli->error . "\n";
                        }

                        if (stripos($content, "Sẽ cập nhật chương này sau!") !== false) {
                            $emptyChapterCount++;
                        } else {
                            $emptyChapterCount = 0;
                        }

                        if ($emptyChapterCount > 5) {
                            break;
                        }

                        sleep(1); // Small delay between requests
                    }

                    // Update genres
                    insertStoryGenres($idTruyen, $storyInfo['genres']);
                } else {
                    echo "Lỗi khi cập nhật thông tin truyện: " . $mysqli->error . "\n";
                }
            } else {
                $sqlTruyen = "INSERT INTO tbl_truyen (tieude, hinhanh, tomtat, tacgia, ngaydang, id_admin, status_tt, luotdoc, decu, yeuthich, gold, truyen_status) VALUES ('$title', '$imageSrc', '$description', '$author', NOW(), $id_admin, 0, 0, 0, 0, 0, 0)";

                if ($mysqli->query($sqlTruyen) === TRUE) {
                    $idTruyen = $mysqli->insert_id;

                    for ($i = 1; $i <= 100000; $i++) {
                        $chapterUrl = "{$url}chuong-$i/";
                        $content = fetchChapterContent($chapterUrl);

                        $content = $mysqli->real_escape_string($content);

                        $sqlChuong = "INSERT INTO tbl_chuong (tenchuong, noidung, sochuong, thoigian, id_truyen, is_locked) VALUES ('Chương $i', '$content', $i, NOW(), $idTruyen, 0) ON DUPLICATE KEY UPDATE noidung = VALUES(noidung)";

                        if ($mysqli->query($sqlChuong) === TRUE) {
                            echo "Thêm hoặc cập nhật chương $i thành công.\n";
                        } else {
                            echo "Lỗi khi thêm hoặc cập nhật chương $i: " . $mysqli->error . "\n";
                        }

                        if (stripos($content, "Sẽ cập nhật chương này sau!") !== false) {
                            $emptyChapterCount++;
                        } else {
                            $emptyChapterCount = 0;
                        }

                        if ($emptyChapterCount > 5) {
                            break;
                        }

                        sleep(1); // Small delay between requests
                    }

                    // Insert genres
                    insertStoryGenres($idTruyen, $storyInfo['genres']);
                } else {
                    echo "Lỗi khi thêm truyện mới: " . $mysqli->error . "\n";
                }
            }
        } else {
            echo "Không thể trích xuất thông tin truyện từ nội dung HTML.\n";
        }
    } else {
        echo "Không thể lấy nội dung HTML từ URL: $url\n";
    }
}

$mysqli->close();
?>
