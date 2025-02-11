<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Paper;
use App\Models\Academicwork;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Bibtex;
use RenanBr\BibTexParser\Listener;
use RenanBr\BibTexParser\Parser;
use RenanBr\BibTexParser\Processor;

class HomeController extends Controller
{

    public function index()
    {
        $papers = [];
        $acaworks = [];
        $year = range(Carbon::now()->year - 4, Carbon::now()->year);
        $years = range(Carbon::now()->year, Carbon::now()->year - 5);
        $from = Carbon::now()->year - 16;
        $to = Carbon::now()->year - 6;

        $from2 = "{$from}-01-01";  // 2009-01-01
        $to2 = "{$to}-12-31"; 

        $p2 = Paper::with([
            'teacher' => function ($query) {
                $query->select(DB::raw("CONCAT(concat(left(fname_en,1),'.'),' ',lname_en) as full_name"))->addSelect('user_papers.author_type');
            },
            'author' => function ($query) {
                $query->select(DB::raw("CONCAT(concat(left(author_fname,1),'.'),' ',author_lname) as full_name"))->addSelect('author_of_papers.author_type');
            },

        ])->whereBetween('paper_yearpub', [$from, $to])->orderBy('paper_yearpub', 'desc')->get()->toArray();

        $ac2 = Academicwork::with([
            'teacher' => function ($query) {
                $query->select(DB::raw("CONCAT(concat(left(fname_en,1),'.'),' ',lname_en) as full_name"))->addSelect('user_of_academicworks.author_type');
            },
            'author' => function ($query) {
                $query->select(DB::raw("CONCAT(concat(left(author_fname,1),'.'),' ',author_lname) as full_name"))->addSelect('author_of_academicworks.author_type');
            },
    
        ])->whereBetween('ac_year', [$from2, $to2])->orderBy('ac_year', 'desc')->get()->toArray();


        $paper2 = array_map(function ($tag) {
            $t = collect($tag['teacher']);
            $a = collect($tag['author']);
            $aut = $t->concat($a);
            $aut = $aut->sortBy(['author_type', 'asc']);
            $sorted = $aut->implode('full_name', ', ');
            return array(
                'id' => $tag['id'],
                'author' => $sorted,
                'paper_name' => $tag['paper_name'],
                'paper_sourcetitle' => $tag['paper_sourcetitle'],
                'paper_type' => $tag['paper_type'],
                'paper_subtype' => $tag['paper_subtype'],
                'paper_yearpub' => $tag['paper_yearpub'],
                'paper_url' => $tag['paper_url'],
                'paper_volume' => $tag['paper_volume'],
                'paper_issue' => $tag['paper_issue'],
                'paper_citation' => $tag['paper_citation'],
                'paper_page' => $tag['paper_page'],
                'paper_doi' => $tag['paper_doi'],
            );
        }, $p2);


        foreach ($years as $key => $value) {
            $p = Paper::with([
                'teacher' => function ($query) {
                    $query->select(DB::raw("CONCAT(concat(left(fname_en,1),'.'),' ',lname_en) as full_name"))->addSelect('user_papers.author_type');
                },
                'author' => function ($query) {
                    $query->select(DB::raw("CONCAT(concat(left(author_fname,1),'.'),' ',author_lname) as full_name"))->addSelect('author_of_papers.author_type');
                },

            ])->where('paper_yearpub', '=', $value)->orderBy('paper_yearpub', 'desc')->get()->toArray();

            $paper = array_map(function ($tag) {
                $t = collect($tag['teacher']);
                $a = collect($tag['author']);
                $aut = $t->concat($a);
                $aut = $aut->sortBy(['author_type', 'asc']);
                $sorted = $aut->implode('full_name', ', ');
                return array(
                    'id' => $tag['id'],
                    'author' => $sorted,
                    'paper_name' => $tag['paper_name'],
                    'paper_sourcetitle' => $tag['paper_sourcetitle'],
                    'paper_type' => $tag['paper_type'],
                    'paper_subtype' => $tag['paper_subtype'],
                    'paper_yearpub' => $tag['paper_yearpub'],
                    'paper_url' => $tag['paper_url'],
                    'paper_volume' => $tag['paper_volume'],
                    'paper_issue' => $tag['paper_issue'],
                    'paper_citation' => $tag['paper_citation'],
                    'paper_page' => $tag['paper_page'],
                    'paper_doi' => $tag['paper_doi'],
                );
            }, $p);
            $papers[$value] = collect($paper);
        }
        $papers[$to] = collect($paper2);



        $academic2 = array_map(function ($tag2) {
            $t = collect($tag2['teacher']);
            $a = collect($tag2['author']);
            $aut = $t->concat($a);
            $aut = $aut->sortBy(['author_type', 'asc']);
            $sorted = $aut->implode('full_name', ', ');
            return array(
                'id' => $tag2['id'],
                'author' => $sorted,
                'ac_name' => $tag2['ac_name'],
                'ac_type' => $tag2['ac_type'],
                'ac_sourcetitle' => $tag2['ac_sourcetitle'],
                'ac_year' => $tag2['ac_year'],
                'ac_refnumber' => $tag2['ac_refnumber'],
                'ac_page' => $tag2['ac_page'],
            );
        }, $ac2);

        foreach ($years as $key2 => $value2) {
            $ac = Academicwork::with([
                'teacher' => function ($query) {
                    $query->select(DB::raw("CONCAT(concat(left(fname_en,1),'.'),' ',lname_en) as full_name"))->addSelect('user_of_academicworks.author_type');
                },
                'author' => function ($query) {
                    $query->select(DB::raw("CONCAT(concat(left(author_fname,1),'.'),' ',author_lname) as full_name"))->addSelect('author_of_academicworks.author_type');
                },
        
            ])->where('ac_year','=','{$value2}-01-01')->orderBy('ac_year', 'desc')->get()->toArray();

            $academic = array_map(function ($tag2) {
                $t = collect($tag2['teacher']);
                $a = collect($tag2['author']);
                $aut = $t->concat($a);
                $aut = $aut->sortBy(['author_type', 'asc']);
                $sorted = $aut->implode('full_name', ', ');
                return array(
                    'id' => $tag2['id'],
                    'author' => $sorted,
                    'ac_name' => $tag2['ac_name'],
                    'ac_type' => $tag2['ac_type'],
                    'ac_sourcetitle' => $tag2['ac_sourcetitle'],
                    'ac_year' => $tag2['ac_year'],
                    'ac_refnumber' => $tag2['ac_refnumber'],
                    'ac_page' => $tag2['ac_page'],
                );
            }, $ac);
            $acaworks[$value2] = collect($academic);
        }
        $acaworks[$to2] = collect($academic2);



        $paper_tci = [];
        $paper_scopus = [];
        $paper_wos = [];
        $paper_orcid = [];
        $paper_scholar = [];
        $academic_other = [];
        

        foreach ($year as $key => $value) {
            $paper_scopus[] = Paper::whereHas('source', function ($query) {
                return $query->where('source_data_id', '=', 1);
            })->whereIn('paper_type', ['Conference Proceeding', 'Journal', 'Book Series'])
                ->where(DB::raw('(paper_yearpub)'), $value)->count();
        }

        foreach ($year as $key => $value) {
            $paper_wos[] = Paper::whereHas('source', function ($query) {
                return $query->where('source_data_id', '=', 2);
            })->whereIn('paper_type', ['Conference Proceeding', 'Journal', 'Book Series'])
                ->where(DB::raw('(paper_yearpub)'), $value)->count();
        }

        foreach ($year as $key => $value) {
            $paper_tci[] = Paper::whereHas('source', function ($query) {
                return $query->where('source_data_id', '=', 3);
            })->whereIn('paper_type', ['Conference Proceeding', 'Journal', 'Book Series'])
                ->where(DB::raw('(paper_yearpub)'), $value)->count();
        }

        foreach ($year as $key => $value) {
            $paper_orcid[] = Paper::whereHas('source', function ($query) {
                return $query->where('source_data_id', '=', 4);
            })->whereIn('paper_type', ['Conference Proceeding', 'Journal', 'Book Series'])
                ->where(DB::raw('(paper_yearpub)'), $value)->count();
        }

        foreach ($year as $key => $value) {
            $paper_scholar[] = Paper::whereHas('source', function ($query) {
                return $query->where('source_data_id', '=', 5);
            })->whereIn('paper_type', ['Conference Proceeding', 'Journal', 'Book Series'])
                ->where(DB::raw('(paper_yearpub)'), $value)->count();
        }

        foreach ($year as $key => $value) {
            $academic_other[] = Academicwork::whereYear('ac_year', '=', $value)->count();
        }
        

        $num = $this->getnum();
        //$paper_tci_numall = $num['paper_tci'];
        $paper_scopus_numall = $num['paper_scopus'];
        //$paper_wos_numall = $num['paper_wos'];
        //$paper_orcid_numall = $num['paper_orcid'];
        //$paper_scholar_numall = $num['paper_scholar'];
        $academic_other_numall = $num['academic_other'];

        return view('home', compact('papers'))->with('year', json_encode($year, JSON_NUMERIC_CHECK))
            ->with('paper_scopus', json_encode($paper_scopus, JSON_NUMERIC_CHECK))
            //->with('paper_orcid', json_encode($paper_orcid, JSON_NUMERIC_CHECK))
            //->with('paper_scholar', json_encode($paper_scholar, JSON_NUMERIC_CHECK))
            ->with('academic_other', json_encode($academic_other, JSON_NUMERIC_CHECK))
            ->with('paper_scopus_numall', json_encode($paper_scopus_numall, JSON_NUMERIC_CHECK))
            //->with('paper_orcid_numall', json_encode($paper_orcid_numall, JSON_NUMERIC_CHECK))
            ->with('academic_other_numall', json_encode($academic_other_numall, JSON_NUMERIC_CHECK));
            
        
       

    }

    public function getnum()
    {
        $paper_scopus[] = Paper::whereHas('source', function ($query) {
            return $query->where('source_data_id', '=', 1);
        })->whereIn('paper_type', ['Conference Proceeding', 'Journal', 'Book Series'])->count();


        $paper_orcid[] = Paper::whereHas('source', function ($query) {
            return $query->where('source_data_id', '=', 4);
        })->whereIn('paper_type', ['Conference Proceeding', 'Journal', 'Book Series'])->count();

        $paper_scholar[] = Paper::whereHas('source', function ($query) {
            return $query->where('source_data_id', '=', 5);
        })->whereIn('paper_type', ['Conference Proceeding', 'Journal', 'Book Series'])->count();

        $academic_other[] = Academicwork::whereIn('ac_type', ['book', 'อนุสิทธิบัตร'])->count();

        return compact('paper_scopus', 'academic_other');
    }
    public function bibtex($id)
    {
        $paper = Paper::with(['author' => function ($query) {
            $query->select('author_name');
        }])->find([$id])->first()->toArray();

        // $acabook = Academicwork::with(['author' => function ($query) {
        //     $query->select('author_name');
        // }])->find([$id])->first()->toArray();

        $Path['lib'] = './../lib/';
        require_once $Path['lib'] . 'lib_bibtex.inc.php';

        $Site = array();


        $Site['bibtex'] = new Bibtex('references.bib');
        $bb = $Site['bibtex'];

        $title = $paper['paper_name'];

        $a = collect($paper['author']);
        $author = $a->implode('author_name', ', ');
        $journal = $paper['paper_sourcetitle'];
        $volume = $paper['paper_volume'];
        $number = $paper['paper_citation'];
        $page = $paper['paper_page'];
        $year = $paper['paper_yearpub'];
        $doi = $paper['paper_doi'];
        

        $key = "kku";
        $arr = array("type" => $type, "key" => "kku", "author" => $author, "title" => $title, "journal" => $journal, "volume" => $volume, "number" => $number, 'year' => $year, 'pages' => $page, 'ee' => $doi);

        $bb->bibarr["kku"] = $arr;
        $key = "kku";

        return response()->json($key, $bb);
    }

    public function bibtexbook($id)
    {
        $acabook = Academicwork::with(['author' => function ($query) {
            $query->select('author_name');
        }])->find([$id])->first()->toArray();

        $Path['lib'] = './../lib/';
        require_once $Path['lib'] . 'lib_bibtex.printers.inc.php';

        $Site = array();


        $Site['bibtex'] = new Bibtex('references.bib');
        $bb = $Site['bibtex'];

        $title = $paper['paper_name'];

        $a = collect($paper['author']);
        $author = $a->implode('author_name', ', ');
        $journal = $paper['paper_sourcetitle'];
        $volume = $paper['paper_volume'];
        $number = $paper['paper_citation'];
        $page = $paper['paper_page'];
        $year = $paper['paper_yearpub'];
        $doi = $paper['paper_doi'];

        $key = "kku";
        $arr = array("type" => $type, "key" => "kku", "author" => $author, "title" => $title, "journal" => $journal, "volume" => $volume, "number" => $number, 'year' => $year, 'pages' => $page, 'ee' => $doi);

        $bb->bibarr["kku"] = $arr;
        $key = "kku";

        return response()->json($key, $bb);
    }


}
