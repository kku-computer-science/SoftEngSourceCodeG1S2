<?php

header("Content-Type: application/json");

// ✅ โหลดไฟล์ JSON
$json_file = __DIR__ . "/../googleScholar/scholar_data.json";
$researchers = file_exists($json_file) ? json_decode(file_get_contents($json_file), true) : [];

// ✅ รับค่าพารามิเตอร์จาก URL
$endpoint = $_GET['endpoint'] ?? null;
$user_id = $_GET['id'] ?? null;

// ✅ ตรวจสอบเส้นทาง API
if ($endpoint === "researchers") {
    // ✅ 1. ดึงข้อมูลนักวิจัยทั้งหมด
    if (!$user_id) {
        echo json_encode($researchers, JSON_PRETTY_PRINT);
        exit;
    }

    // ✅ 2. ดึงข้อมูลนักวิจัยตาม Google Scholar ID
    $researcher = array_filter($researchers, fn($r) => strpos($r["scholar_profile_url"], $user_id) !== false);

    if ($researcher) {
        echo json_encode(array_values($researcher)[0], JSON_PRETTY_PRINT);
    } else {
        echo json_encode(["error" => "Not Found"], JSON_PRETTY_PRINT);
    }
    exit;
} elseif ($endpoint === "scrape" && $user_id) {
    // ✅ 3. ดึงข้อมูลนักวิจัยใหม่และอัปเดต JSON
    require_once __DIR__ . "/../googleScholar/scrape_scholar.php";
    $new_data = scrape_scholar_profile($user_id);

    if ($new_data) {
        $researchers[] = $new_data;
        file_put_contents($json_file, json_encode($researchers, JSON_PRETTY_PRINT));
        echo json_encode(["message" => "Scraped and updated", "data" => $new_data], JSON_PRETTY_PRINT);
    } else {
        echo json_encode(["error" => "Failed to scrape data"], JSON_PRETTY_PRINT);
    }
    exit;
}

// ✅ ถ้าไม่มีเส้นทางที่ตรงกัน → แสดง Error
http_response_code(404);
echo json_encode(["error" => "Invalid API Endpoint"], JSON_PRETTY_PRINT);
