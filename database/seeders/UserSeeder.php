<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        DB::table('users')->insert([
            [
                'username' => 'admin',
                'name' => 'IPMS Administrator',
                'email' => 'admin@ipms.test',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'status' => 'approved',
                'approved_at' => $now,
                'job_position' => 'System Administrator',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'username' => 'user1',
                'name' => 'Project Manager',
                'email' => 'project.manager@ipms.test',
                'password' => Hash::make('password'),
                'role' => 'user',
                'status' => 'approved',
                'approved_at' => $now,
                'job_position' => 'Project Manager',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'username' => 'user2',
                'name' => 'Project Coordinator',
                'email' => 'project.coordinator@ipms.test',
                'password' => Hash::make('password'),
                'role' => 'user',
                'status' => 'approved',
                'approved_at' => $now,
                'job_position' => 'Project Coordinator',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'username' => 'user3',
                'name' => 'Pending User',
                'email' => 'pending.user@ipms.test',
                'password' => Hash::make('password'),
                'role' => 'user',
                'status' => 'pending',
                'job_position' => 'Project Contributor',
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);
    }
}
