<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    // This tells Laravel the primary key is NOT 'id'
    protected $primaryKey = 'employee_id';

    protected $fillable = [
        'employee_fn',
        'employee_mi',
        'employee_ln',
        'role',
        'account_status'
    ];

    public function user() {
        return $this->hasOne(User::class, 'employee_id');
    }
    public function payments() {
        return $this->hasMany(Payment::class, 'employee_id');
    }
    public function deliveries() {
        return $this->hasMany(Delivery::class, 'employee_id');
    }
    public function salesReports() {
        return $this->hasMany(SalesReport::class, 'employee_id');
    }
}
