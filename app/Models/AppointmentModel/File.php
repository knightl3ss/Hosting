<?php

namespace App\Models\AppointmentModel;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\EncryptsAttributes;

class File extends Model
{
    use HasFactory, EncryptsAttributes;
    
    /**
     * The attributes that should be encrypted.
     *
     * @var array
     */
    protected $encrypted = [
        'filename',
        'file_path'
    ];

    protected $fillable = [
        'filename',
        'file_path',
    ];
    
    /**
     * Get the original filename (for display purposes)
     *
     * @return string
     */
    public function getOriginalFilenameAttribute()
    {
        return pathinfo($this->filename, PATHINFO_FILENAME);
    }
}
