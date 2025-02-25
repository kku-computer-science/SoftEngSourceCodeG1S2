<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;

class FetchOrcidJson extends Command
{
    protected $signature = 'fetch:orcid-json';
    protected $description = 'Fetch researcher data from ORCID API and save as JSON';

    public function handle()
    {
        $orcidIds = [
            '0000-0001-9245-7400', // Punyaphol Horata
            '0000-0001-5289-1149', // คนที่ 2
            '0000-0001-5315-751X',  // คนที่ 3
            '0000-0001-7233-6572',  
            '0000-0001-7579-2485',  
            '0000-0001-8255-5998',
            '0000-0001-8441-8962',
            '0000-0001-8691-285X', 
            '0000-0001-9027-7836',
            '0000-0001-9054-0737',
            '0000-0002-1889-7288',
            '0000-0002-2042-3284',
            '0000-0002-2603-0879',
            '0000-0002-3813-6910',
            '0000-0002-3960-4181',
            '0000-0002-3978-3431',
            '0000-0002-4289-9443',
            '0000-0002-4358-7927',
            '0000-0002-4454-1736',
            '0000-0002-4689-3006',
            '0000-0002-4704-687X',
            '0000-0002-6403-6518',
            '0000-0002-7806-7390',
            '0000-0002-7941-6150',
            '0000-0002-8012-6855',
            '0000-0002-9063-0705',
            '0000-0003-1026-191X',
            '0000-0003-1948-4183',
            '0000-0003-3900-6453',
            '0000-0003-4473-2206',
            '0009-0002-6086-230X',
            '0009-0006-5806-2572',
            '0009-0008-9197-4703', 
        ];

        foreach ($orcidIds as $orcidId) {
            $this->info("📱 Fetching ORCID ID: $orcidId ...");
            $data = $this->fetchOrcidData($orcidId);
            
            if ($data === null) {
                $this->error("⚠️ Failed to fetch data for ORCID: $orcidId");
                continue;
            }

            $researcherData = [
                'name' => $this->fetchName($data),
                'affiliation' => $this->fetchAffiliation($data),
                'h_index' => "N/A", 
                'i10_index' => "N/A", 
                'papers' => $this->fetchWorks($orcidId, $data),
            ];

            Storage::disk('local')->put("orcid/orcid_$orcidId.json", json_encode($researcherData, JSON_PRETTY_PRINT));
            $this->info("✅ Saved JSON -> storage/app/orcid/orcid_$orcidId.json");
        }
    }

    private function fetchOrcidData($orcidId)
    {
        $response = Http::withHeaders([
            'Accept' => 'application/json',
        ])->get("https://pub.orcid.org/v3.0/$orcidId/record");

        if ($response->failed()) {
            $this->error("❌ Failed to fetch ORCID data for $orcidId. Status: " . $response->status());
            return null;
        }

        return $response->json();
    }

    private function fetchName($data)
    {
        return $data['person']['name']['credit-name']['value'] 
            ?? ($data['person']['name']['given-names']['value'] ?? 'N/A') . ' ' . ($data['person']['name']['family-name']['value'] ?? '');
    }

    private function fetchAffiliation($data)
    {
        $affiliationGroups = $data['activities-summary']['employments']['affiliation-group'] ?? [];
        if (!empty($affiliationGroups)) {
            $employment = $affiliationGroups[0]['summaries'][0]['employment-summary'] ?? null;
            if ($employment) {
                return ($employment['department-name'] ?? 'N/A') . ', ' . ($employment['organization']['name'] ?? 'N/A');
            }
        }
        return 'N/A';
    }

    private function fetchWorks($orcidId, $data)
    {
        $works = [];
        foreach ($data['activities-summary']['works']['group'] ?? [] as $workGroup) {
            $workSummary = $workGroup['work-summary'][0] ?? null;
            if (!$workSummary) continue;

            $putCode = $workSummary['put-code'] ?? null;
            $workDetails = $this->fetchWorkDetails($orcidId, $putCode);

            $works[] = [
                'title' => $workSummary['title']['title']['value'] ?? 'Untitled',
                'year' => $workSummary['publication-date']['year']['value'] ?? 'Unknown',
                'journal' => $workSummary['journal-title']['value'] ?? 'Unknown Journal',
                'doi' => $workSummary['external-ids']['external-id'][0]['external-id-value'] ?? 'N/A',
                'url' => $workSummary['external-ids']['external-id'][0]['external-id-url']['value'] ?? 'N/A',
                'citation' => $workDetails['citation'] ?? 'N/A',
                'contributors' => $workDetails['contributors'] ?? [],
            ];
        }
        return $works;
    }

    private function fetchWorkDetails($orcidId, $putCode)
    {
        if (!$putCode) return ['citation' => 'N/A', 'contributors' => []];
        
        $url = "https://pub.orcid.org/v3.0/$orcidId/work/$putCode";
        $response = Http::withHeaders(['Accept' => 'application/json'])->get($url);
        
        if ($response->failed()) {
            $this->error("❌ Failed to fetch work details for ORCID: $orcidId, PutCode: $putCode. Status: " . $response->status());
            return ['citation' => 'N/A', 'contributors' => []];
        }
        
        $workData = $response->json();
        $citation = $workData['citation']['citation-value'] ?? 'N/A';
        
        $contributors = [];
        foreach ($workData['contributors']['contributor'] ?? [] as $contributor) {
            $contributors[] = [
                'name' => $contributor['credit-name']['value'] ?? 'Unknown',
                'role' => $contributor['contributor-attributes']['contributor-role'] ?? 'N/A',
            ];
        }

        return ['citation' => $citation, 'contributors' => $contributors];
    }
}
