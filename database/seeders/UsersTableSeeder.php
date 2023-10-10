<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::truncate();

        User::firstOrCreate([
            'id' => '1',
            'type' => '0',
            'email' => 'superadmin@gmail.com',
            'nama' => 'Super Admin',
            'password' => Hash::make('admin'),
        ]);
    }
}
