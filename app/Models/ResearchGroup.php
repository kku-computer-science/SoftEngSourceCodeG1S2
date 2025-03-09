<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ResearchGroup extends Model
{
    use HasFactory;
    protected $fillable = [
        'group_name_th', 'group_name_en', 'group_detail_th', 'group_detail_en', 'group_desc_th', 'group_desc_en', 'group_image'
    ];

    public function user()
    {
        return $this->belongsToMany(User::class,'work_of_research_groups')->withPivot(['role','permissions']);
        // OR return $this->hasOne('App\Phone');
    }

    public function author()
    {
        return $this->belongsToMany(Author::class,'author_in_research_groups');
        // OR return $this->hasOne('App\Phone');
    }
    public function product(){
        return $this->hasOne(Product::class,'group_id');
    }

    public function recruitment()
    {
        return $this->hasMany(Recruitment::class, 'research_group_id');
    }
}
