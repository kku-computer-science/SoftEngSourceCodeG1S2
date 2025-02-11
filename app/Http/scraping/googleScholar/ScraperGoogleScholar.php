<?php

namespace App\Http\scraping\GoogleScholar;

use DOMDocument;
use DOMXPath;
use Illuminate\Support\Facades\Storage;

class ScraperGoogleScholar {
    

    public function __construct() {
        
    }

    

    // ✅ ฟังก์ชันดึงข้อมูลนักวิจัยจาก Google Scholar
    public static function scrapeScholarProfile($user_id) {
        $url = "https://scholar.google.com/citations?hl=en&user=" . $user_id . "&view_op=list_works&sortby=pubdate";

        echo "🔍 กำลังดึงข้อมูลนักวิจัย: $user_id ...\n";

        $html = ScraperGoogleScholar::fetchHTML($url);
        if (!$html) return null;

        $dom = new DOMDocument();
        @$dom->loadHTML($html);
        $xpath = new DOMXPath($dom);

        $name = $xpath->query('//div[@id="gsc_prf_in"]')->item(0)?->nodeValue ?? "Unknown Name";
        $affiliation = $xpath->query('//div[@class="gsc_prf_il"]')->item(0)?->nodeValue ?? "Unknown Affiliation";
        $h_index = (int) ($xpath->query('//td[@class="gsc_rsb_std"]')->item(2)?->nodeValue ?? 0);
        $i10_index = (int) ($xpath->query('//td[@class="gsc_rsb_std"]')->item(4)?->nodeValue ?? 0);

        $papers = [];
        $paper_nodes = $xpath->query('//tr[@class="gsc_a_tr"]');
        foreach ($paper_nodes as $node) {
            $titleNode = $xpath->query('.//a[@class="gsc_a_at"]', $node);
            $title = $titleNode->length > 0 ? $titleNode[0]->nodeValue : "No Title";
            $paper_link = $titleNode->length > 0 ? "https://scholar.google.com" . $titleNode[0]->getAttribute('href') : "#";

            $citationNode = $xpath->query('.//td[@class="gsc_a_c"]/a', $node);
            $citations = $citationNode->length > 0 ? (int) $citationNode[0]->nodeValue : 0;

            $yearNode = $xpath->query('.//td[@class="gsc_a_y"]/span', $node);
            $year = $yearNode->length > 0 ? $yearNode[0]->nodeValue : "Unknown";

            $paper_details = ScraperGoogleScholar::scrapePaperDetails($paper_link);

            $papers[] = array_merge([
                "title" => $title,
                "link" => $paper_link,
                "citations" => $citations,
                "year" => $year
            ], $paper_details);

            sleep(rand(4, 7));
        }

        return [
            "id" => $user_id,
            "name" => $name,
            "affiliation" => $affiliation,
            "h_index" => $h_index,
            "i10_index" => $i10_index,
            "scholar_profile_url" => $url,
            "papers" => $papers
        ];
    }

    // ✅ ดึงรายละเอียดของ Paper
    private static function scrapePaperDetails($paper_url) {
        if ($paper_url == "#") return [];

        $html = ScraperGoogleScholar::fetchHTML($paper_url);
        if (!$html) return [];

        $dom = new DOMDocument();
        @$dom->loadHTML($html);
        $xpath = new DOMXPath($dom);

        return [
            "authors" => $xpath->query('//div[@class="gsc_oci_value"]')->item(0)?->nodeValue ?? "Unknown Authors",
            "journal" => $xpath->query('//div[contains(text(), "Journal")]/following-sibling::div')->item(0)?->nodeValue ?? null,
            "publication_date" => $xpath->query('//div[contains(text(), "Publication date")]/following-sibling::div')->item(0)?->nodeValue ?? null,
            "doi" => $xpath->query('//div[contains(text(), "DOI")]/following-sibling::div')->item(0)?->nodeValue ?? null,
            "description" => $xpath->query('//div[contains(text(), "Description")]/following-sibling::div')->item(0)?->nodeValue ?? null
        ];
    }

    // ✅ ฟังก์ชันโหลด HTML ด้วย cURL
    private static function fetchHTML($url) {
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

        return $html;
    }

}

?>
