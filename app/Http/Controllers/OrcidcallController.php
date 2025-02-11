<?php

namespace App\Http\Controllers;

use App\Models\Author;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Paper;
use App\Models\Source_data;
use Carbon\Carbon;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;

class OrcidApiController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param string $id
     * @return \Illuminate\Http\Response
     */
    public function create($id)
    {
        // Decrypting the user ID from the encrypted parameter
        $id = Crypt::decrypt($id);
        $data = User::find($id);

        // Getting user's name to search on ORCID API
        $fname = substr($data['fname_en'], 0, 1);
        $lname = $data['lname_en'];
        
        // Fetching data from ORCID API
        $url = Http::get('https://pub.orcid.org/v3.0/search', [
            'q' => "family-name:$lname given-name:$fname",
            'rows' => 10, // Number of records to return, adjust as needed
        ])->json();

        $content = $url['result'];

        // Iterate over the returned records
        foreach ($content as $item) {
            if (!isset($item['error'])) {
                // Check if this paper is already in the database
                if (Paper::where('paper_name', '=', $item['title'])->first() == null) {
                    $orcid_id = $item['orcid']; // ORCID ID of the paper
                    $paper_data = Http::get("https://pub.orcid.org/v3.0/works/{$orcid_id}")->json();

                    // Create or update Paper model
                    $paper = new Paper;
                    $paper->paper_name = $item['title'];
                    $paper->paper_type = $item['type'];
                    $paper->paper_sourcetitle = $item['sourceTitle'];
                    $paper->paper_url = $item['url'];

                    $date = Carbon::parse($item['publishedDate'])->format('Y');
                    $paper->paper_yearpub = $date;

                    // Handle other fields like volume, issue, citation, DOI, etc.
                    $paper->paper_volume = $item['volume'] ?? null;
                    $paper->paper_issue = $item['issue'] ?? null;
                    $paper->paper_citation = $item['citationCount'] ?? 0;
                    $paper->paper_page = $item['pageRange'] ?? null;
                    $paper->paper_doi = $item['doi'] ?? null;
                    $paper->save();

                    // Associate source data (same as Scopus example)
                    $source = Source_data::findOrFail(1);
                    $paper->source()->sync($source);

                    // Process authors (assuming this data is available in ORCID response)
                    $authors = $paper_data['authors'];
                    foreach ($authors as $author_data) {
                        $author_fname = $author_data['givenName'];
                        $author_lname = $author_data['familyName'];

                        // Check if the author already exists in the database
                        $author = Author::firstOrCreate([
                            'author_fname' => $author_fname,
                            'author_lname' => $author_lname,
                        ]);

                        // Attach the author to the paper (associating them as an author)
                        $paper->author()->attach($author);
                    }
                }
            }
        }
    }
}
