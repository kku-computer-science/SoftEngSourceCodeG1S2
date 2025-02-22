<?php

namespace App\Http\Utility;

use App\Models\Paper;


class PaperUtility
{
    /*

        {
            "title": "Service priority classification using machine learning",
            "link": "https:\/\/scholar.google.com\/citations?view_op=view_citation&hl=en&user=00JXDiUAAAAJ&sortby=pubdate&citation_for_view=00JXDiUAAAAJ:GnPB-g6toBAC",
            "citations": 0,
            "year": "2024",
            "authors": "Teratam Boonprapapan, Pusadee Seresangtakul, Punyaphol Horata",
            "journal": "Science, Engineering and Health Studies",
            "publication_date": "2024\/10\/3",
            "doi": "No DOI",
            "description": "This article details a procedure for classifying service cases with various priority levels based on machine learning (ML). It accurately defines the priority level of each service case. The presence of imbalanced datasets in service cases poses a challenge for achieving reliable classification accuracy. To address this, the use of the synthetic minority over-sampling technique (SMOTE) was proposed as the method for balancing the datasets prior to applying the ML method. From these experimental results, an improvement in the precision of the learning process was observed, which led to better outcomes in the test sets. This improvement was measured using the efficiency metrics from the confusion matrix. The experiment involved 6,182 service cases, categorized into four levels: critical, serious, moderate, and low. These were based on test comparisons with other ML methods. The accuracy achieved in the test data was 94.37%. By employing a hybrid technique to address the imbalance in SMOTE and the support vector machine model, it was found to be more effective than the comparative term frequency-inverse document frequency model that was used in conjunction with cosine similarity, which achieved an evaluation score of 70.14%."
        }
    */
    public static function isDuplicatePaper($paper_for_check)
    {
        $paper = Paper::where('title', $paper_for_check['title'])->first();
        if($paper == null){
            return false;
        }

        $authors = $paper->author()->get();
        $authors_paper = explode(',', $paper_for_check['authors']);
        $authors_paper = array_map('trim', $authors_paper);
        $authors_paper = array_map(function($author){
            return explode(' ', $author);
        }, $authors_paper);

        foreach($authors_paper as $author){
            $found = false;
            foreach($authors as $match_author){
                if($author[0] == $match_author->author_fname && $author[1] == $match_author->author_lname){
                    $found = true;
                    break;
                }
            }
            if(!$found){
                return false;
            }
        }

        return true;
    }

    public static function validatePaperObjectFormat($paper)
    {
        
        return true;
    }

    public static function isShortName($name)
    {
        // len <= 3
        // end with .
        // all char is upper
        if(strlen($name) <= 3 && $name[strlen($name)-1] == '.' && ctype_upper($name)){
            return true;
        }
        return false;
    }
}