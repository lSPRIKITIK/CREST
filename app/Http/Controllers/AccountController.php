<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Carbon\Carbon;

class AccountController extends Controller
{
public function index()
    {
        // Fetch all employees and their associated user login data
        $accounts = \App\Models\Employee::with('user')->get();

        // Pass the $accounts variable to the view
        return view('accounts.index', compact('accounts'));
    }
    public function store(Request $request)
    {
        $validated = $request->validate([
            'employee_fn' => 'required|string|max:255',
            'employee_ln' => 'required|string|max:255',
            'role' => 'required|string|in:Founders/General Manager,Sales & Record Personnel,Production Manager,Craftsmen,Delivery Personnel',
            'username' => 'required|string|unique:users,username',
            'password' => 'required|string|min:6',
        ]);

        DB::transaction(function () use ($validated) {
            $employee = Employee::create([
                'employee_fn' => $validated['employee_fn'],
                'employee_ln' => $validated['employee_ln'],
                'role' => $validated['role'],
            ]);

            User::create([
                'employee_id' => $employee->employee_id,
                'username' => $validated['username'],
                'password' => Hash::make($validated['password']),
                'password_updated_date' => Carbon::now(),
                'account_status' => 'active',
            ]);
        });

        return back()->with('success', 'Staff account created successfully.');
    }

    public function edit($id)
    {
        $employee = Employee::with('user')->findOrFail($id);
        return view('accounts.edit', compact('employee'));
    }

    public function update(Request $request, $id)
    {
        $employee = Employee::with('user')->findOrFail($id);
        $userId = $employee->user ? $employee->user->getKey() : null;
        $userKeyName = $employee->user ? $employee->user->getKeyName() : 'id';

        $validated = $request->validate([
            'employee_fn' => 'required|string|max:255',
            'employee_ln' => 'required|string|max:255',
            'role' => 'required|string|in:Founders/General Manager,Sales & Record Personnel,Production Manager,Craftsmen,Delivery Personnel',
            'username' => [
                'required',
                'string',
                Rule::unique('users', 'username')->ignore($userId, $userKeyName),
            ],
            'password' => 'nullable|string|min:6', // Optional: only update if typed
        ]);

        DB::transaction(function () use ($validated, $employee) {
            $employee->update([
                'employee_fn' => $validated['employee_fn'],
                'employee_ln' => $validated['employee_ln'],
                'role' => $validated['role'],
            ]);

            if ($employee->user) {
                $userData = ['username' => $validated['username']];

                // Only hash and update the password if the admin typed a new one
                if (!empty($validated['password'])) {
                    $userData['password'] = Hash::make($validated['password']);
                    $userData['password_updated_date'] = Carbon::now();
                }

                $employee->user->update($userData);
            }
        });

        return redirect()->route('accounts.index')->with('success', 'Account updated successfully.');
    }

    public function destroy($id)
    {
        $employee = Employee::findOrFail($id);

        // Prevent the admin from deleting their own active account
        if (Auth::user()->employee->employee_id === $employee->employee_id) {
            return back()->withErrors(['error' => 'You cannot delete your own account while logged in.']);
        }

        try {
            DB::transaction(function () use ($employee) {
                if ($employee->user) {
                    $employee->user->delete();
                }
                $employee->delete();
            });
            return back()->with('success', 'Account deleted successfully.');

        } catch (\Illuminate\Database\QueryException $e) {
            // Catches the restrictOnDelete constraint from the payments table
            return back()->withErrors(['error' => 'Cannot delete this account. This employee has processed payments or orders in the system.']);
        }
    }

    public function toggleStatus($id)
    {
        $employee = Employee::with('user')->findOrFail($id);

        // Prevent the admin from deactivating their own account
        if (Auth::user()->employee->employee_id === $employee->employee_id) {
            return back()->withErrors(['error' => 'You cannot deactivate your own account.']);
        }

        if ($employee->user) {
            $newStatus = $employee->user->account_status === 'active' ? 'inactive' : 'active';
            $employee->user->update(['account_status' => $newStatus]);

            return back()->with('success', "Account for {$employee->employee_fn} successfully marked as {$newStatus}.");
        }

        return back()->withErrors(['error' => 'No login credentials found for this employee.']);
    }
}
