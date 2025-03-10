<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Recruitment extends Model
{
    use HasFactory;

    protected $table = 'recruitment';

    protected $fillable = [
        'research_group_id',
        'title_th',
        'title_en',
        'position_id',
        'job_description_th',
        'job_description_en',
        'place_th',
        'place_en',
        'salary',
        'other_th',
        'other_en',
        'apply_channel_th',
        'apply_channel_en',
    ];

    /**
     * Get the research group for this recruitment.
     */
    public function researchGroup()
    {
        return $this->belongsTo(ResearchGroup::class, 'research_group_id');
    }

    /**
     * Get the position associated with this recruitment.
     */
    public function position()
    {
        return $this->belongsTo(RecruitmentPosition::class, 'position_id');
    }

    /**
     * Get the qualifications for this recruitment.
     */
    public function qualifications()
    {
        return $this->hasMany(RecruitmentQualification::class, 'recruitment_id');
    }
}
