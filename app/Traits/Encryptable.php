<?php

namespace App\Traits;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Crypt;

trait Encryptable
{
    // Encrypt attributes before saving
    public function setAttribute($key, $value)
    {

        if (in_array($key, $this->encryptable) && !empty($value)) {
            $this->attributes[$key] = Crypt::encrypt($value);
        } else {
            parent::setAttribute($key, $value);
        }
    }

    public function getAttribute($key)
          {

             // Retrieve the value
              $value = parent::getAttribute($key);
    
              // Check if the value is encrypted
              if (Str::startsWith($value, ['eyJpdiI6', 'eyJuYW1lIjoi']) ) {
                  try {
                      // Decrypt the value
                      return decrypt($value);
                  } catch (\Illuminate\Contracts\Encryption\DecryptException $e) {
                      // Handle decryption failure, you may want to log the error or return a default value
                  }
          }
    
      // Return the value as is
      return $value;
          }

 // Additional method to encrypt file contents
 public function encryptFileContents($filePath)
 {
     try {
         $content = file_get_contents($filePath);
         if ($content === false) {
             return null;
         }
         return Crypt::encrypt($content);
     } catch (\Exception $e) {
         return null;
     }
 }

 // Additional method to decrypt file contents
 public function decryptFileContents($encryptedData, $filePath)
 {
     try {
         $decryptedContent = Crypt::decrypt($encryptedData);
         if ($decryptedContent === false) {
             return false;
         }
         return file_put_contents($filePath, $decryptedContent) !== false;
     } catch (\Exception $e) {
         return false;
     }
 }
}