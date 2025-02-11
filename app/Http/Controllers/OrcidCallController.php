<?php

namespace App\Http\Controllers;

use App\Models\Author;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Paper;
use App\Models\Source_data;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Http\Utility\UserUtility;

class OrcidCallController extends Controller
{
    public function fetchWorks($id)
    {
        // 📌 ถอดรหัส ID ที่ส่งมาจากหน้า UI
        $id = Crypt::decrypt($id);  
        $user = User::find($id);
        
        $source_id = Source_data::where('source_name', 'ORCID')->first()->id;
        // $orcidId = UserUtility::getUserSearchKey($id, $source_id);
        $orcidId = UserUtility::getUserSearchKey($id, $source_id);
        if (!$orcidId) {
            return response()->json(['error' => 'No ORCID ID found for this user'], 404);
        }
        
        $orcidApiUrl = "https://pub.orcid.org/v3.0/{$orcidId}/works";
        $response = Http::withHeaders([
            'Authorization' => 'Bearer 2798605d-3db0-4518-82cb-f308d3bdf7f8',
            'Accept' => 'application/json',
        ])->get($orcidApiUrl);
        
        if ($response->failed()) {
            return response()->json(['error' => 'Failed to fetch ORCID data'], 500);
        }
        
        $works = $response->json()['group'] ?? [];
        foreach ($works as $workGroup) {
            foreach ($workGroup['work-summary'] as $work) {
                $putCode = $work['put-code'] ?? null;
                if (!$putCode) continue;
                
                $workDetails = $this->fetchWorkDetails($orcidId, $putCode);
                if (!$workDetails) continue;
                
                $title = $workDetails['title']['title']['value'] ?? 'Untitled';
                $doi = null;
                
                if (isset($workDetails['external-ids']['external-id'])) {
                    foreach ($workDetails['external-ids']['external-id'] as $externalId) {
                        if ($externalId['external-id-type'] === 'doi') {
                            $doi = $externalId['external-id-value'];
                            break;
                        }
                    }
                }
                
                $paper = Paper::updateOrCreate(
                    ['paper_name' => $title],
                    ['paper_doi' => $doi]
                );
                
                $paper->teacher()->syncWithoutDetaching([$id]);
                
                $source = Source_data::firstOrCreate(['source_name' => 'ORCID']);
                $paper->source()->syncWithoutDetaching([$source->id]);
                
                if (isset($workDetails['contributors']['contributor'])) {
                    $x = 1;
                    $totalContributors = count($workDetails['contributors']['contributor']);
                    foreach ($workDetails['contributors']['contributor'] as $contributor) {
                        $authorName = $contributor['credit-name']['value'] ?? null;
                        if (!$authorName) continue;
                        
                        $nameParts = explode(' ', $authorName, 2);
                        $fname = $nameParts[0] ?? '';
                        $lname = $nameParts[1] ?? '';

                        $existingUser = User::where([['fname_en', '=', $fname], ['lname_en', '=', $lname]])->first();
                        if ($existingUser) {
                            $paper->teacher()->syncWithoutDetaching([$existingUser->id => ['author_type' => $x === 1 ? 1 : ($x === $totalContributors ? 3 : 2)]]);
                        } else {
                            $author = Author::where([['author_fname', '=', $fname], ['author_lname', '=', $lname]])->first();
                            if (!$author) {
                                $author = new Author();
                                $author->author_fname = $fname;
                                $author->author_lname = $lname;
                                $author->save();
                            }
                            $paper->author()->syncWithoutDetaching([$author->id => ['author_type' => $x === 1 ? 1 : ($x === $totalContributors ? 3 : 2)]]);
                        }
                        $x++;
                    }
                }
            }
        }
        
        return response()->json([
            'message' => 'Data fetched successfully',
            'updated_papers' => Paper::whereHas('teacher', function ($query) use ($id) {
                $query->where('users.id', $id);
            })->latest()->get()
        ]);
    }
    
    private function fetchWorkDetails($orcidId, $putCode)
    {
        $workDetailsUrl = "https://pub.orcid.org/v3.0/{$orcidId}/work/{$putCode}";
        $response = Http::withHeaders([
            'Authorization' => 'Bearer 2798605d-3db0-4518-82cb-f308d3bdf7f8',
            'Accept' => 'application/json',
        ])->get($workDetailsUrl);
        
        if ($response->failed()) {
            return null;
        }
        
        return $response->json();
    }
}
