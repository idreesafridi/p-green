<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorksDetails extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'construction_site_id',

        'window_company',
        'window_company_prize',

        'hybrid_system_company',
        'hybrid_system_company_prize',

        'electric_system_company',
        'electric_system_company_prize',

        'construction_system_company1',
        'construction_system_company_prize1',

        'construction_system_company2',
        'construction_system_company_prize2',

        'photovoltic',
        'photovoltic_prize',

        'coordinator',
        'works_manager',
    ];
}
