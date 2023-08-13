<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MainCompany extends Model
{
    protected $fillable = ['name', 'company_image','kind','enable'];


    public function Company()
    {
        return $this->hasMany(Company::class);

    }//end of Company


}
