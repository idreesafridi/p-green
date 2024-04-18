<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RegPracDoc extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'status_reg_prac_id',
        'construction_site_id',
        'allow',
        'file_name',
        'description',
        'file_path',
        'bydefault',
        'state',
        'updated_on',
        'updated_by',
        'reminders_emails',
        'reminders_days',
        'file',
        'status',
    ];
    // relation with construction
    public function ConstructionSite()
    {
        return $this->belongsTo(ConstructionSite::class, 'construction_site_id');
    }
    // relation with statusRegPrac
    public function statusRegPrac()
    {
        return $this->belongsTo(statusRegPrac::class, 'status_reg_prac_id');
    }
    // relation with RegPracDocFile
    public function RegPracDocFile()
    {
        return $this->hasMany(RegPracDocFile::class);
    }
}
