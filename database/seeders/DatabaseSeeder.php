<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Create the Founder/General Manager employee record
        $employee = Employee::create([
            'employee_fn' => 'Dexter',
            'employee_mn' => 'B.',
            'employee_ln' => 'Aquino',
            'role' => 'Founders/General Manager',
            'contact_number' => '09123456789',
        ]);

        // 2. Create the linked User account
        User::create([
            'employee_id' => $employee->employee_id,
            'username' => 'admin_dexter',
            'password' => Hash::make('Admin123!'),
            'account_status' => 'active',
            'failed_attempts' => 0,
            'password_updated_date' => Carbon::now(),
        ]);
    }
}
