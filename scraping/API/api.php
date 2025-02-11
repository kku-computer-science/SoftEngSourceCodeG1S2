<?php

header("Content-Type: application/json");

$json_file = __DIR__ . "/../googleScholar/scholar_data.json"; // ปรับเส้นทางไฟล์ JSON

// ✅ โหลดไฟล์ JSON เดิม ถ้ามี
if (file_exists($json_file)) {
    $json_data = file_get_contents($json_file);
    $researchers = json_decode($json_data, true);
} else {
    $researchers = [];
}

// ✅ รับค่าพารามิเตอร์จาก URL
$id = $_GET['id'] ?? null;

// ✅ กรณีไม่มีพารามิเตอร์ ส่งข้อมูลนักวิจัยทั้งหมดกลับไป
if (!$id) {
    echo json_encode($researchers, JSON_PRETTY_PRINT);
    exit;
}

// ✅ ค้นหาข้อมูลนักวิจัยจาก scholar_data.json ตาม Google Scholar ID
$found_researcher = null;
foreach ($researchers as $researcher) {
    if (strpos($researcher["scholar_profile_url"], $id) !== false) {
        $found_researcher = $researcher;
        break;
    }
}

// ✅ ถ้าพบข้อมูล ส่ง JSON กลับไป
if ($found_researcher) {
    echo json_encode($found_researcher, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
} else {
    echo json_encode(["error" => "Researcher not found"], JSON_PRETTY_PRINT);
}

?>
