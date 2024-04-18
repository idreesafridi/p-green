<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BusinessDetail extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'company_name',
        'company_type'
    ];

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function materialPrice(){
        return $this->hasMany(MaterialPrice::class, 'business_detail_id');
    }
}
