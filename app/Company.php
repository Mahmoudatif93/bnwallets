<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    protected $fillable = ['name', 'company_image','kind','api','enable','api2','idapi2','AcountID','AcountEmail','AcountPassword'];


    public function cards()
    {
        return $this->hasMany(Cards::class);

    }//end of Cards
    
}
