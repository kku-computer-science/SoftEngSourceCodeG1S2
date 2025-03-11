<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Recruitment;
use Illuminate\Support\Facades\DB;

class RecruitmentPolicy
{
    /**
     * Determine whether the user can view any recruitments.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function viewAny(User $user)
    {
        return $this->isHeadProject($user);
    }

    /**
     * Determine whether the user can view the recruitment.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Recruitment  $recruitment
     * @return mixed
     */
    public function view(User $user, Recruitment $recruitment)
    {
        return $this->isHeadProject($user);
    }

    /**
     * Determine whether the user can create recruitments.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        return $this->isHeadProject($user);
    }

    /**
     * Determine whether the user can update the recruitment.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Recruitment  $recruitment
     * @return mixed
     */
    public function update(User $user, Recruitment $recruitment)
    {
        dd($this->isHeadProject($user)); // ตรวจสอบค่าที่ return ออกมา
        return $this->isHeadProject($user);
    }

    /**
     * Determine whether the user can delete the recruitment.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Recruitment  $recruitment
     * @return mixed
     */
    public function delete(User $user, Recruitment $recruitment)
    {
        return $this->isHeadProject($user);
    }

    /**
     * Check if the user is a headproject.
     *
     * @param  \App\Models\User  $user
     * @return bool
     */
    protected function isHeadProject(User $user)
    {
        return DB::table('work_of_research_groups')
            ->where('user_id', $user->id)
            ->where('role', 1) // ตรวจสอบว่าเป็น headproject (role = 1)
            ->exists();
    }
}