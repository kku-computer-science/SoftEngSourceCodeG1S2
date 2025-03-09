<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RecruitmentQualification extends Model
{
    use HasFactory;

    protected $table = 'recruitment_qualification';

    protected $fillable = [
        'recruitment_id',
        'text_th',
        'text_en',
    ];

    /**
     * Get the recruitment that owns this qualification.
     */
    public function recruitment()
    {
        return $this->belongsTo(Recruitment::class, 'recruitment_id');
    }
}
