<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\ScopuscallController;
use App\Http\Controllers\OrcidCallController;
// use App\Http\Controllers\ScholarcallController;

class CallPaperController extends Controller
{
    public function callBoth($id)
    {
        // เรียก ORCID API
        $orcidResponse = app(OrcidCallController::class)->fetchWorks($id);

        // เรียก Scopus API
        $scopusResponse = app(ScopuscallController::class)->create($id);

        // 📌 เรียก Google Scholar API
        // $scholarResponse = app(ScholarcallController::class)->fetchAndSave($id);

        return response()->json([
            'orcid' => json_decode($orcidResponse->getContent(), true),
            'scopus' => json_decode($scopusResponse->getContent(), true),
            // 'scholar' => json_decode($scholarResponse->getContent(), true),
        ]);
    }
}
