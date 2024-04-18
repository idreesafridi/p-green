<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChiavettaDoc extends Model
{
    use HasFactory, HasUuids;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'allow',
        'folder_name',
        'description',
        'updated_on',
        'updated_by',
        'reminders_emails',
        'reminders_days',
        'bydefault',
        'state',
    ];
}
