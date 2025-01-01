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
        $user = User::whereDoesntHave('superAdmin')->firstOrFail();
        $group = Group::create([
            'name' => 'Group 1',
            'description' => 'Group created by a user',
            'lang' => 'en',
            'creator_id' => $user->id,
        ]);
        $user->groups()->attach($group->id,[
            'inviter_id' => $user->id,
            'joined_at' => now()
        ]);
        $user = User::whereHas('superAdmin')->firstOrFail();
        $group = Group::create([
            'name' => 'Group 2',
            'description' => 'Group created by a user who is actually a super admin',
            'color' => '#FF0000',
            'lang' => 'en',
            'creator_id' => $user->id,
        ]);
        $user->groups()->attach($group->id,[
            'inviter_id' => $user->id,
            'joined_at' => now()
        ]);
    }
}
