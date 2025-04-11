<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Invoice;
use App\Models\User;

class Customer extends Model
{
    protected $fillable = [
        'name',
        'email',
        'phone',
        'user_id',
    ];
      // User সম্পর্ক (একজন ইউজারের একাধিক কাস্টমার থাকতে পারে)
      public function user()
      {
          return $this->belongsTo(User::class);
      }
  
      // Invoice সম্পর্ক (একজন কাস্টমারের একাধিক Invoice থাকতে পারে)
      public function invoices()
      {
          return $this->hasMany(Invoice::class);
      }
}
