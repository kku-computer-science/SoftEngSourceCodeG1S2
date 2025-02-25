<?php

$inputPath = "C:/SE/SoftEngSourceCodeG1S2/storage/app/Orcid/";
$outputPath = "C:/SE/SoftEngSourceCodeG1S2/storage/app/Orcid_to_DB/all_papers.json";

// ตรวจสอบว่าโฟลเดอร์มีไฟล์ JSON หรือไม่
$jsonFiles = glob($inputPath . "*.json");
if (empty($jsonFiles)) {
    echo "❌ ไม่พบไฟล์ JSON ในโฟลเดอร์: $inputPath\n";
    exit;
}

echo "🔍 พบไฟล์ JSON จำนวน: " . count($jsonFiles) . " ไฟล์\n";

$researchers = [];

foreach ($jsonFiles as $file) {
    $jsonContent = file_get_contents($file);
    if ($jsonContent === false) {
        echo "❌ ไม่สามารถอ่านไฟล์: " . basename($file) . "\n";
        continue;
    }

    $data = json_decode($jsonContent, true);
    if (empty($data)) {
        echo "⚠️ ไฟล์ว่างเปล่าหรือข้อมูลผิดพลาด: " . basename($file) . "\n";
        continue;
    }

    // ดึงข้อมูลนักวิจัย
    $researcher = [
        "name" => $data['name'] ?? "N/A",
        "affiliation" => $data['affiliation'] ?? "N/A",
        "h_index" => $data['h_index'] ?? "N/A",
        "i10_index" => $data['i10_index'] ?? "N/A",
        "papers" => []
    ];

    // ดึงข้อมูลงานวิจัย
    if (!empty($data['papers'])) {
        foreach ($data['papers'] as $paper) {
            $authors = [];
            if (!empty($paper['contributors'])) {
                foreach ($paper['contributors'] as $contributor) {
                    if (!empty($contributor['name'])) {
                        $authors[] = trim($contributor['name']);
                    }
                }
            }

            if (empty($authors)) {
                $authors[] = $researcher["name"];
            }

            $authorsString = implode(", ", array_unique($authors));

            $researcher["papers"][] = [
                "title" => $paper['title'] ?? "N/A",
                "year" => $paper['year'] ?? "N/A",
                "journal" => $paper['journal'] ?? "N/A",
                "doi" => $paper['doi'] ?? "N/A",
                "url" => $paper['url'] ?? "N/A",
                "citation" => $paper['citation'] ?? "N/A",
                "authors" => $authorsString
            ];
        }
    }

    $researchers[] = $researcher;
}

// บันทึกไฟล์ JSON
if (file_put_contents($outputPath, json_encode($researchers, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)) !== false) {
    echo "✅ บันทึกผลลัพธ์ที่: $outputPath\n";
} else {
    echo "❌ ไม่สามารถบันทึกไฟล์ JSON ที่: $outputPath\n";
}

?>
