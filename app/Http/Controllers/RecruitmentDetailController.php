<?php

namespace App\Http\Controllers;

use App\Models\ResearchGroup;
use App\Models\Recruitment;
use App\Models\RecruitmentPosition;
use App\Models\RecruitmentQualification;
use Illuminate\Http\Request;

class RecruitmentDetailController extends Controller
{
    public function request($id)
    {
        // ดึงข้อมูล recruitment พร้อมกับ researchGroup
        $recruitment = Recruitment::with('researchGroup')->where('id', '=', $id)->first();

        // ดึงข้อมูล position name จากตาราง recruitment_position
        $position = RecruitmentPosition::where('id', $recruitment->position_id)->first();

        // ดึงข้อมูล qualifications จากตาราง recruitment_qualification
        $qualifications = RecruitmentQualification::where('recruitment_id', $recruitment->id)->get();

        return view('recruitmentdetail', compact('recruitment', 'position', 'qualifications'));
    }
}