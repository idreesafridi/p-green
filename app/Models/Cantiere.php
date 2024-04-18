<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cantiere extends Model
{
    use HasFactory;

    protected $table = 'cantiere';

    public function clientbelongs()
    {
        return $this->belongsTo(Cliente::class, 'FK_cliente', 'clienteId');
    }
}
