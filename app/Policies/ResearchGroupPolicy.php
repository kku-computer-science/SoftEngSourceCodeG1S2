<?php

namespace App\Policies;

use App\Models\ResearchGroup;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Support\Facades\DB;

class ResearchGroupPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function viewAny(User $user)
    {
        //
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\ResearchGroup  $researchGroup
     * @return mixed
     */
    public function view(User $user, ResearchGroup $researchGroup)
    {

    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        if ($user->hasRole('admin')) {
            return true;
        }
        return false;
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\ResearchGroup  $researchGroup
     * @return mixed
     */
    public function update(User $user, ResearchGroup $researchGroup)
    {
        $researchGroup = ResearchGroup::find($researchGroup->id)->user()->where('user_id', $user->id)->get();
        //$researchProject = User::with(['researchProject'])->where('id',$user->id)->get();
        if ($user->hasRole('admin')) {
            return true;
        }
        if ($user->hasRole('staff')) {
            return true;
        }
        foreach ($researchGroup as $res) {
            if ($user->id == $res->id && $res->pivot->permissions == '1') {
                return true;
            }
        }
        return false;
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\ResearchGroup  $researchGroup
     * @return mixed
     */

    // Only admin and HeadProject can delete
    public function delete(User $user, ResearchGroup $researchGroup)
    {
        // ตรวจสอบว่าผู้ใช้เป็น admin
        if ($user->hasRole('admin')) {
            return true;
        }
    
        // ตรวจสอบว่า user เป็น headproject ของ researchGroup 
        $isHeadProject = DB::table('work_of_research_groups')
            ->where('user_id', $user->id) // ตรวจสอบ user_id
            ->where('research_group_id', $researchGroup->id) // ตรวจสอบว่าอยู่ใน researchGroup นี้
            ->where('role', 1) // ตรวจสอบว่าเป็น headproject (role = 1)
            ->exists();
        return $isHeadProject;
    }    
    

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\ResearchGroup  $researchGroup
     * @return mixed
     */
    public function restore(User $user, ResearchGroup $researchGroup)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\ResearchGroup  $researchGroup
     * @return mixed
     */
    public function forceDelete(User $user, ResearchGroup $researchGroup)
    {
        //
    }
}
