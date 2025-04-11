<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Invoice;
use App\Models\Product;

class InvoiceProduct extends Model
{
    protected $fillable = [
        'user_id',
        'invoice_id',
        'product_id',
        'qty',
        'sale_price',
    ];

    public function user(){
        return $this->belongsTo(User::class);
    }
    public function invoice(){
        return $this->belongsTo(Invoice::class);
    }
    public function product(){
        return $this->belongsTo(Product::class);
    }

}
