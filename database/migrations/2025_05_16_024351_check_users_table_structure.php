<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Check if the users table exists
        if (Schema::hasTable('users')) {
            Log::info('Users table exists');
            
            // Check if the role column exists
            if (Schema::hasColumn('users', 'role')) {
                Log::info('Role column exists in users table');
                
                // Get a list of all roles in the database
                $roles = DB::table('users')
                    ->select('role')
                    ->distinct()
                    ->get()
                    ->pluck('role')
                    ->toArray();
                
                Log::info('Distinct roles found: ' . json_encode($roles));
                
                // Count admin users
                $adminCount = DB::table('users')
                    ->where('role', 'admin')
                    ->count();
                
                Log::info('Admin count: ' . $adminCount);
                
                // If no admin users are found, try checking with case-insensitive matching
                if ($adminCount === 0) {
                    // Try different case variations of 'admin'
                    $adminVariations = [
                        'Admin', 
                        'ADMIN', 
                        'administrator',
                        'Administrator',
                        'ADMINISTRATOR'
                    ];
                    
                    foreach ($adminVariations as $variation) {
                        $count = DB::table('users')
                            ->where('role', $variation)
                            ->count();
                        
                        if ($count > 0) {
                            Log::info("Found {$count} admin users with role value '{$variation}'");
                            
                            // Update the role column to be consistently 'admin'
                            DB::table('users')
                                ->where('role', $variation)
                                ->update(['role' => 'admin']);
                                
                            Log::info("Updated {$count} admin users to have role 'admin'");
                        }
                    }
                }
                
                // Check for null or empty role values
                $nullRoleCount = DB::table('users')
                    ->whereNull('role')
                    ->count();
                    
                Log::info('Null role count: ' . $nullRoleCount);
                
                $emptyRoleCount = DB::table('users')
                    ->where('role', '')
                    ->count();
                    
                Log::info('Empty role count: ' . $emptyRoleCount);
                
                // Set default role to 'user' if null or empty
                if ($nullRoleCount > 0 || $emptyRoleCount > 0) {
                    DB::table('users')
                        ->where(function($query) {
                            $query->whereNull('role')
                                ->orWhere('role', '');
                        })
                        ->update(['role' => 'user']);
                        
                    Log::info('Updated ' . ($nullRoleCount + $emptyRoleCount) . ' users to have role "user"');
                }
            } else {
                Log::warning('Role column does not exist in users table');
                
                // Add role column if it doesn't exist
                Schema::table('users', function (Blueprint $table) {
                    $table->string('role')->default('user')->after('email');
                });
                
                Log::info('Added role column to users table with default value "user"');
            }
        } else {
            Log::error('Users table does not exist');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // This migration only checks and logs information, so no reverse action needed
    }
};
