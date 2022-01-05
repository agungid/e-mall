<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    use HasFactory;

    /**
     * fillable
     *
     * @var array
     */
    protected $fillable = [
        'province_id', 'city_id', 'name'
    ];

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    public function provice()
    {
        return $this->belongsTo(Province::class);
    }
}
