<?php

$json_file = "scholar_data.json";

// ✅ โหลดไฟล์ JSON เดิม ถ้ามี
if (file_exists($json_file)) {
    $json_data = file_get_contents($json_file);
    $researchers = json_decode($json_data, true);
} else {
    $researchers = [];
}

// ✅ ฟังก์ชันดึงข้อมูลนักวิจัยจาก Google Scholar
function scrape_scholar_profile($user_id) {
    $url = "https://scholar.google.com/citations?hl=en&user=" . $user_id . "&view_op=list_works&sortby=pubdate";

    echo "🔍 กำลังดึงข้อมูลนักวิจัย: $user_id ...\n";

    // ✅ ใช้ cURL โหลด HTML
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 10.0; Win64; x64)");
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

    $html = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($http_code != 200 || empty($html)) {
        echo "❌ Error: ไม่สามารถดึงข้อมูลจาก Google Scholar ได้! HTTP Code: $http_code\n";
        return null;
    }

    // ✅ ใช้ DOMDocument วิเคราะห์ HTML
    $dom = new DOMDocument();
    @$dom->loadHTML($html);
    $xpath = new DOMXPath($dom);

    // ✅ ดึงข้อมูลโปรไฟล์นักวิจัย
    $name = $xpath->query('//div[@id="gsc_prf_in"]')->item(0)?->nodeValue ?? "Unknown Name";
    $affiliation = $xpath->query('//div[@class="gsc_prf_il"]')->item(0)?->nodeValue ?? "Unknown Affiliation";
    $h_index = (int) $xpath->query('//td[@class="gsc_rsb_std"]')->item(2)?->nodeValue ?? 0;
    $i10_index = (int) $xpath->query('//td[@class="gsc_rsb_std"]')->item(4)?->nodeValue ?? 0;

    // ✅ ดึงรายการผลงานวิจัย
    $papers = [];
    $paper_nodes = $xpath->query('//tr[@class="gsc_a_tr"]');
    foreach ($paper_nodes as $node) {
        // ✅ ดึงชื่อบทความ และลิงก์
        $titleNode = $xpath->query('.//a[@class="gsc_a_at"]', $node);
        $title = $titleNode->length > 0 ? $titleNode[0]->nodeValue : "No Title";
        $paper_link = $titleNode->length > 0 ? "https://scholar.google.com" . $titleNode[0]->getAttribute('href') : "#";

        // ✅ ดึงจำนวนการอ้างอิง
        $citationNode = $xpath->query('.//td[@class="gsc_a_c"]/a', $node);
        $citations = $citationNode->length > 0 ? (int) $citationNode[0]->nodeValue : 0;

        // ✅ ดึงปีที่เผยแพร่
        $yearNode = $xpath->query('.//td[@class="gsc_a_y"]/span', $node);
        $year = $yearNode->length > 0 ? $yearNode[0]->nodeValue : "Unknown";

        // ✅ ดึงรายละเอียดของ Paper จากลิงก์ของ Paper
        $paper_details = scrape_paper_details($paper_link);

        $papers[] = array_merge([
            "title" => $title,
            "link" => $paper_link,
            "citations" => $citations,
            "year" => $year
        ], $paper_details);

        sleep(rand(4, 7)); // ✅ ป้องกันโดนบล็อก
    }

    return [
        "name" => $name,
        "affiliation" => $affiliation,
        "h_index" => $h_index,
        "i10_index" => $i10_index,
        "scholar_profile_url" => $url,
        "papers" => $papers
    ];
}

// ✅ ฟังก์ชันดึงรายละเอียดของ Paper
function scrape_paper_details($paper_url) {
    if ($paper_url == "#") return [];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $paper_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 10.0; Win64; x64)");
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

    $html = curl_exec($ch);
    curl_close($ch);

    $dom = new DOMDocument();
    @$dom->loadHTML($html);
    $xpath = new DOMXPath($dom);

    $authors = $xpath->query('//div[@class="gsc_oci_value"]')->item(0)?->nodeValue ?? "Unknown Authors";
    $journal = $xpath->query('//div[contains(text(), "Journal")]/following-sibling::div')->item(0)?->nodeValue ?? null;
    $publication_date = $xpath->query('//div[contains(text(), "Publication date")]/following-sibling::div')->item(0)?->nodeValue ?? null;
    $doi = $xpath->query('//div[contains(text(), "DOI")]/following-sibling::div')->item(0)?->nodeValue ?? null;
    $description = $xpath->query('//div[contains(text(), "Description")]/following-sibling::div')->item(0)?->nodeValue ?? null;

    return [
        "authors" => $authors,
        "journal" => $journal,
        "publication_date" => $publication_date,
        "doi" => $doi,
        "description" => $description
    ];
}

// ✅ รายชื่อนักวิจัยที่ต้องการเพิ่มข้อมูล
$new_researchers = [
    "vnTKGAcAAAAJ"  // นักวิจัยใหม่
    
];

foreach ($new_researchers as $user_id) {
    $data = scrape_scholar_profile($user_id);
    if ($data) {
        $researchers[] = $data; // ✅ เพิ่มข้อมูลเข้าอาร์เรย์เดิม
    } else {
        echo "❌ ไม่สามารถดึงข้อมูลจาก Google Scholar ได้: $user_id\n";
    }

    sleep(rand(7, 10)); // ✅ ป้องกันโดนบล็อก
}

// ✅ บันทึกข้อมูลเป็น JSON โดย **เพิ่ม** ข้อมูลใหม่เข้าไปในไฟล์เดิม
file_put_contents($json_file, json_encode($researchers, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

echo "✅ บันทึกข้อมูลเสร็จสิ้น: scholar_data.json\n";

?>
