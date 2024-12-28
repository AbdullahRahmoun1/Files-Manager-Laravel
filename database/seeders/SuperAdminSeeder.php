<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::create([
            'name' => 'super',
            'email' => 'super@test.com',
            'password' => '12345'
        ]);
        $user->superAdmin()->create(['user_id'=>$user->id]);
    }
}
