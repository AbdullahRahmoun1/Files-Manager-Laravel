<?php

namespace Database\Seeders;

use App\Models\Group;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class GroupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Group::create([
            'name' => 'Group 1',
            'description' => 'Group created by a user',
            'lang' => 'en',
            'creator_id' => User::whereDoesntHave('superAdmin')->firstOrFail()->id,
        ]);
        Group::create([
            'name' => 'Group 2',
            'description' => 'Group created by a user who is actually a super admin',
            'color' => '#FF0000',
            'lang' => 'en',
            'creator_id' => User::whereHas('superAdmin')->firstOrFail()->id,
        ]);
    }
}
