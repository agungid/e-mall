<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'slug', 'image'
    ];
    
        
    /**
     * getImageAttribute
     *
     * @param  mixed $value image value 
     * @return void
     */
    public function getImageAttribute($value)
    {
        return asset('storage/categories/'.$value);
    }
    
    /**
     * products relationship
     *
     * @return void
     */
    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
