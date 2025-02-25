<?php

namespace App\Http\Controllers;

use App\Models\ResearchGroup;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class ResearchGroupDetailController extends Controller
{
    public function request($id)
    {
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
        ])->where('id', '=', $id)->get();

        $authorInRG = ResearchGroup::where('id', '=', $id)->first();
        $authors = $authorInRG->author;


        return view('researchgroupdetail', compact('resgd', 'authors'));
    }
}
