<?php

namespace App\Models;

use App\Traits\Encryptable;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DocumentAndContact extends Model
{
    use HasFactory , Encryptable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $encryptable = [
        'document_number',
        'issued_by',
        'release_date',
        'expiration_date',
        'fiscal_document_number',
        'vat_number',
        'contact_email',
        'contact_number',
        'alt_refrence_name',
        'alt_contact_number'
    ];
    protected $guarded = [];

    // public function setAttribute($key, $value)
    // {
    //     // Check if the attribute should be encrypted
    //     if (in_array($key, $this->encryptable) && !is_null($value)) {
    //         // Encrypt the value before setting
    //         $this->attributes[$key] = encrypt($value);
    //     } else {
    //         parent::setAttribute($key, $value);
    //     }
    // }

    /**
     * Accessor for decryption when retrieving attributes.
     *
     * @param string $key
     * @return mixed
     */
    // public function getAttribute($key)
    // {
    //     // Retrieve the value
    //     $value = parent::getAttribute($key);

    //     // Check if the value is encrypted and needs decryption
    //     if (Str::startsWith($value, ['eyJpdiI6', 'eyJuYW1lIjoi']) && in_array($key, $this->encryptable)) {
    //         try {
    //             // Decrypt the value
    //             return decrypt($value);
    //         } catch (\Illuminate\Contracts\Encryption\DecryptException $e) {
    //             // Handle decryption failure, you may want to log the error or return a default value
    //             // dd($e);
    //             return null; // Return null if decryption fails
    //         }
    //     }

    //     // Return the value as is
    //     return $value;
    // }



    /**
     * relation with ConstructionSite
     */
    public function ConstructionSite()
    {
        return $this->belongsTo(ConstructionSite::class, 'construction_site_id');
    }
}
