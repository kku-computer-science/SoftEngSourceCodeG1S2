<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Paper;
use App\Models\Source_data;
use App\Models\Author;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Http\scraping\GoogleScholar\ScraperGoogleScholar;

class ScholarcallController extends Controller
{
    /**
     * ฟังก์ชันดึงข้อมูลผลงานวิจัยจาก Google Scholar และบันทึกลงฐานข้อมูล
     */
    public function fetchAndSave($userId)
    {
        $scraper = new ScraperGoogleScholar();
        $scholarData = $scraper->scrapeScholarProfile($userId);

        if (!$scholarData) {
            return response()->json(["error" => "ไม่พบข้อมูลนักวิจัยหรือเกิดข้อผิดพลาด"], 404);
        }

        DB::beginTransaction(); // ✅ ใช้ Transaction เพื่อความปลอดภัย
        try {
            foreach ($scholarData["papers"] as $paperData) {
                // ✅ ตรวจสอบว่ามี Paper นี้ในฐานข้อมูลหรือยัง
                $paper = Paper::firstOrNew(['paper_name' => $paperData['title']]);

                // หากยังไม่มี ให้ทำการเพิ่ม
                if (!$paper->exists) {
                    $paper->paper_name = $paperData['title']; // dc:title
                    $paper->paper_url = $paperData['link']; // prism:url
                    $paper->paper_sourcetitle = $paperData['journal'] ?? 'Unknown'; // prism:publicationName
                    $paper->paper_volume = $paperData['prism:volume'] ?? null; // prism:volume
                    $paper->paper_issue = $paperData['prism:issueIdentifier'] ?? null; // prism:issueIdentifier
                    $paper->paper_page = $paperData['prism:pageRange'] ?? null; // prism:pageRange
                    $paper->paper_yearpub = $paperData['year'] ?? null; // prism:coverDate
                    $paper->paper_doi = $paperData['doi'] ?? null; // prism:doi
                    $paper->paper_citation = $paperData['citations'] ?? 0; // citedby-count
                    $paper->paper_type = $paperData['prism:aggregationType'] ?? null; // prism:aggregationType
                    $paper->paper_subtype = $paperData['subtypeDescription'] ?? null; // subtypeDescription
                    $paper->abstract = $paperData['description'] ?? null; // Abstract
                    
                    $paper->save();

                    // ✅ บันทึกแหล่งที่มาของข้อมูล (Google Scholar)
                    $source = Source_data::firstOrCreate(['source_name' => 'Google Scholar']);
                    $paper->source()->syncWithoutDetaching([$source->id]);

                    // ✅ บันทึกผู้แต่ง
                    if (!empty($paperData['authors'])) {
                        $authors = explode(", ", $paperData['authors']);
                        $x = 1;
                        $totalContributors = count($authors);

                        foreach ($authors as $authorName) {
                            $nameParts = explode(" ", $authorName);
                            $fname = array_shift($nameParts);
                            $lname = implode(" ", $nameParts);

                            // ✅ ตรวจสอบว่า Author เป็นผู้ใช้ระบบอยู่แล้วหรือไม่
                            $existingUser = User::where([['fname_en', '=', $fname], ['lname_en', '=', $lname]])->first();

                            if ($existingUser) {
                                // ✅ ถ้าเป็นผู้ใช้ระบบ ให้เชื่อมโยงกับ `teacher()`
                                $paper->teacher()->syncWithoutDetaching([$existingUser->id => ['author_type' => ($x === 1 ? 1 : ($x === $totalContributors ? 3 : 2))]]);
                            } else {
                                // ✅ ถ้าไม่มีในระบบ ให้เพิ่มเป็น `Author`
                                $author = Author::where([['author_fname', '=', $fname], ['author_lname', '=', $lname]])->first();

                                if (!$author) {
                                    $author = new Author();
                                    $author->author_fname = $fname;
                                    $author->author_lname = $lname;
                                    $author->save();
                                }
                                // ✅ แนบผู้แต่งเข้ากับ paper
                                $paper->author()->syncWithoutDetaching([$author->id => ['author_type' => ($x === 1 ? 1 : ($x === $totalContributors ? 3 : 2))]]);
                            }
                            $x++;
                        }
                    }
                }
            }
            DB::commit();
            return response()->json(["success" => "Data recorded success"]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(["error" => "Fail to recorded Data: " . $e->getMessage()], 500);
        }
    }
}
