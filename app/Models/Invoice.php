<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Invoice extends Model
{
    use HasFactory;

     /**
     * fillable
     *
     * @var array
     */
    protected $fillable = [
        'invoice', 
        'customer_id', 
        'courier', 
        'courier_service', 
        'courier_cost', 
        'weight', 
        'name', 
        'phone', 
        'city_id', 
        'province_id', // refactor ini hanya cukup city saja
        'address', 
        'status', 
        'grand_total', 
        'snap_token'
    ];

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function city()
    {
        return $this->belongsTo(City::class);
    }

    public static function generateInvoiceNumber()
    {
        /**
         * algorithm generate no invoice
         */
        $length = 10;
        $random = '';
        for ($i = 0; $i < $length; $i++) {
            $random .= rand(0, 1) ? rand(0, 9) : chr(rand(ord('a'), ord('z')));
        }

        //generate no invoice
        return 'INV-'.Str::upper($random);
    }
}
