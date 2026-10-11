<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    protected $primaryKey = 'client_id';

    // This line disables mass assignment protection so firstOrCreate() works
    protected $guarded = [];

    public function orders() {
        return $this->hasMany(Order::class, 'client_id');
    }
}
