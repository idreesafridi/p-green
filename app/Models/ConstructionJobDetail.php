<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConstructionJobDetail extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'construction_site_id',
        'fixtures',
        'fixtures_company_price',
        'plumbing',
        'plumbing_company_price',
        'electrical',
        'electrical_installations_company_price',
        'construction',
        'construction_company1_price',
        'construction2',
        'construction_company2_price',
        'photovoltaic',
        'photovoltaic_price',
        'coordinator',
        'construction_manager',
    ];
}
