<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\InvoiceProduct;
use App\Models\Customer;
use App\Models\User;

class Invoice extends Model
{
    protected $fillable = [
        'total',
        'discount',
        'vat',
        'payable',
        'user_id',
        'customer_id',
    ];

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function customer(){
        return $this->belongsTo(Customer::class);
    }

    public function invoice_products(){
        return $this->hasMany(InvoiceProduct::class);
    }
    

}
