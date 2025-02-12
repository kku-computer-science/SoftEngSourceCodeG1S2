<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Paper;
use App\Models\Source_data;
use App\Models\Author;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use App\Http\scraping\GoogleScholar\ScraperGoogleScholar;
use App\Http\Utility\UserUtility;

class ScholarcallController extends Controller
{
    /**
     * ฟังก์ชันดึงข้อมูลผลงานวิจัยจาก Google Scholar และบันทึกลงฐานข้อมูล
     */
    public function fetchAndSave($userId)
    {
        // ✅ ตรวจสอบว่ามี userId หรือไม่
        if (!$userId) {
            return response()->json(['error' => 'Invalid User ID'], 400);
        }

        $source = Source_data::firstOrCreate(['source_name' => 'Google Scholar']);

        // ✅ ตรวจสอบว่ามี user หรือไม่ ก่อนเรียก getUserSearchKey
        $user = User::find($userId);
        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        // ✅ ตรวจสอบว่าได้ Scholar ID หรือไม่
        $ScholarID = UserUtility::getUserSearchKey($userId, $source->id);
        if (!$ScholarID) {
            return response()->json(['error' => 'No GoogleScholar ID found for this user'], 404);
        }

        $scraper = new ScraperGoogleScholar();
        $scholarData = $scraper->scrapeScholarProfile($ScholarID);

        // ✅ ตรวจสอบว่าได้ข้อมูลมาจริงหรือไม่
        if (!$scholarData || empty($scholarData["papers"])) {
            Log::error("Google Scholar scraping failed or returned empty for UserID: $userId");
            return response()->json(["error" => "ไม่พบข้อมูลนักวิจัยหรือเกิดข้อผิดพลาด"], 404);
        }

        DB::beginTransaction(); // ✅ ใช้ Transaction เพื่อความปลอดภัย
        try {
            $savedPapers = []; // ✅ เก็บข้อมูลบทความที่บันทึกสำเร็จ

            foreach ($scholarData["papers"] as $paperData) {
                // ✅ ตรวจสอบว่ามี Paper นี้อยู่ในฐานข้อมูลหรือไม่
                $paper = Paper::where('paper_name', $paperData['title'])->first();

                if (!$paper) {
                    // ✅ ถ้ายังไม่มี ให้เพิ่มข้อมูลใหม่
                    $paper = Paper::create([
                        'paper_name' => $paperData['title'],
                        'paper_url' => $paperData['link'] ?? null,
                        'paper_sourcetitle' => $paperData['journal'] ?? null,
                        'paper_volume' => $paperData['prism:volume'] ?? null,
                        'paper_issue' => $paperData['prism:issueIdentifier'] ?? null,
                        'paper_page' => $paperData['prism:pageRange'] ?? null,
                        'paper_yearpub' => $paperData['year'] ?? null,
                        'paper_doi' => $paperData['doi'] ?? null,
                        'paper_citation' => $paperData['citations'] ?? 0,
                        'paper_type' => $paperData['prism:aggregationType'] ?? null,
                        'paper_subtype' => $paperData['subtypeDescription'] ?? null,
                        'abstract' => $paperData['description'] ?? null,
                    ]);

                    // ✅ เชื่อมโยงบทความกับแหล่งข้อมูล
                    $paper->source()->syncWithoutDetaching([$source->id]);

                    // ✅ เชื่อมโยงบทความกับนักวิจัย (อาจเป็นอาจารย์ในระบบ)
                    $paper->teacher()->syncWithoutDetaching([$userId]);

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
                                // ถ้าเป็นผู้ใช้ระบบ ให้เชื่อมโยงกับ `teacher()`
                                $paper->teacher()->syncWithoutDetaching([$existingUser->id => ['author_type' => ($x === 1 ? 1 : ($x === $totalContributors ? 3 : 2))]]);
                            } else {
                                // ✅ ถ้าไม่มีในระบบ ให้เพิ่มเป็น `Author` แบบ Manual Assignment
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

                    // ✅ เก็บข้อมูลบทความที่บันทึกสำเร็จ
                    $savedPapers[] = [
                        'title' => $paper->paper_name,
                        'url' => $paper->paper_url,
                        'journal' => $paper->paper_sourcetitle,
                        'year' => $paper->paper_yearpub,
                        'doi' => $paper->paper_doi,
                        'citations' => $paper->paper_citation
                    ];
                }
            }
            DB::commit();

            // ✅ คืนค่าข้อมูลบทความที่บันทึกสำเร็จ
            return response()->json([
                "success" => true,
                "message" => "Data recorded successfully",
                "papers" => $savedPapers
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            Log::error("Error saving data: " . $e->getMessage());
            return response()->json(["error" => "Fail to recorded Data: " . $e->getMessage()], 500);
        }
    }
}
