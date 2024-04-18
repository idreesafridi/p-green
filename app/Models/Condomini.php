<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Condomini extends Model
{
    use HasFactory;

    protected $table = 'condominio';

    public function cantiere()
    {
        return $this->hasOne(Cantiere::class, 'cantiereId', 'Fk_Cantiere');
    }
}