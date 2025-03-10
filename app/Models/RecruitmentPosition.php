<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RecruitmentPosition extends Model
{
    use HasFactory;

    protected $table = 'recruitment_position';

    protected $fillable = [
        'name_th',
        'name_en',
    ];

    /**
     * Get the recruitments that use this position.
     */
    public function recruitments()
    {
        return $this->hasMany(Recruitment::class, 'position_id');
    }
}
