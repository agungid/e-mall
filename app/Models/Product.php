<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;
    
    /**
     * fillable
     *
     * @var array
     */
    protected $fillable = [
        'image', 'title', 'slug', 'category_id', 'user_id', 'description', 'weight', 'price', 'stock', 'discount'
    ];
    
    /**
     * replace image to path
     *
     * @param  mixed $value
     * @return void
     */
    public function getImageAttribute($value)
    {
        return asset('storage/products/'.$value);
    }
    
    /**
     * view review average rating product
     *
     * @param  mixed $value
     * @return void
     */
    public function getReviewsAvgRatingAttribute($value)
    {
        return $value ? substr($value, 0, 3) : 0;
    }
    
    /**
     * relation with user
     *
     * @return void
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    /**
     * relation with category
     *
     * @return void
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }
    
    /**
     * relation with carts
     *
     * @return void
     */
    public function carts()
    {
        return $this->hasMany(Cart::class);
    }
    
    /**
     * relation with review
     *
     * @return void
     */
    public function reviews()
    {
        return $this->hasMany(Review::class);
    }
    
    /**
     * relation with orders
     *
     * @return void
     */
    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}
