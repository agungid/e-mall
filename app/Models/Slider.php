<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Slider extends Model
{
    use HasFactory;

    /**
     * fillable
     *
     * @var array
     */
    protected $fillable = [
        'image', 'link'
    ];
    
    /**
     * get path image 
     *
     * @param  mixed $value
     * @return void
     */
    public function getImageAttribute($value)
    {
        return asset('storage/sliders/'.$value);
    }
}
