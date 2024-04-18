<?php

namespace App\Models;

use App\Traits\Encryptable;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PropertyData extends Model
{
    use HasFactory, Encryptable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'construction_site_id',
        'property_street',
        'property_house_number',
        'property_postal_code',
        'property_common',
        'property_province',
        'cadastral_dati',
        'cadastral_section',
        'cadastral_category',
        'cadastral_particle',
        'sub_ordinate',
        'Piano',
        'pod_code',
        'status',
    ];
      

    protected $encryptable = [
        'property_street',
        'property_house_number',
        'property_postal_code',
        'property_common',
        'property_province',
        'cadastral_dati',
        'cadastral_section',
        'cadastral_category',
        'cadastral_particle',
        'sub_ordinate',
        'pod_code',
    ];


//        // Mutator for encryption when setting attributes
//        public function setAttribute($key, $value)
//        {
//           // Ensure that the $this->attributes array is initialized
//            $this->attributes = $this->attributes ?? [];
 
//            if (in_array($key, $this->fillable) && !in_array($key, ['construction_site_id','Piano', 'status']) && !is_null($value)) {
//                // Encrypt the value before setting
//                $this->attributes[$key] = encrypt($value);
//            } else {
//                parent::setAttribute($key, $value);
//            }
//        }
 
//        // Accessor for decryption when retrieving attributes
//        public function getAttribute($key)
//        {
//           // Retrieve the value
//            $value = parent::getAttribute($key);
 
//            // Check if the value is encrypted
//            if (Str::startsWith($value, ['eyJpdiI6', 'eyJuYW1lIjoi']) && !in_array($key, ['construction_site_id','Piano', 'status']) ) {
//                try {
//                    // Decrypt the value
//                    return decrypt($value);
//                } catch (\Illuminate\Contracts\Encryption\DecryptException $e) {
//                    // Handle decryption failure, you may want to log the error or return a default value
//                // dd($e);
//                }
//        }
 
//    // Return the value as is
//    return $value;
//        }










    public function ConstructionSite()
    {
        return $this->belongsTo(ConstructionSite::class, 'construction_site_id');
    }
}
