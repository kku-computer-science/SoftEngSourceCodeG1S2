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

        // ดึงข้อมูล research group
        $resgd = ResearchGroup::with([
            'user' => function ($query) use ($id) {
                $query->select('users.*', 'roles.name as role')
                    ->join('work_of_research_groups as w', function($join) use ($id){
                        $join->on('users.id','=','w.user_id')
                            ->where('w.research_group_id', '=', $id);
                    })
                    ->selectRaw("
                        CASE
                            WHEN w.role = 1 THEN 'Head'
                            WHEN w.role = 2 THEN 'Member'
                            WHEN w.role = 3 THEN 'Post-Doc'
                            WHEN w.role = 4 THEN 'Visitors'
                            ELSE 'Unknown'
                        END AS research_group_role
                    ")
                    ->leftJoin('model_has_roles as mh', 'users.id', '=', 'mh.model_id')
                    ->leftJoin('roles', 'mh.role_id', '=', 'roles.id')
                    ->orderBy('users.id');
            }
        ])->where('id', '=', $recruitment->research_group_id)->get();

        // ดึงข้อมูล position name จากตาราง recruitment_position
        $position = RecruitmentPosition::where('id', $recruitment->position_id)->first();

        // ดึงข้อมูล qualifications จากตาราง recruitment_qualification
        $qualifications = RecruitmentQualification::where('recruitment_id', $recruitment->id)->get();

        return view('recruitmentdetail', compact('recruitment', 'resgd', 'position', 'qualifications'));
    }
}