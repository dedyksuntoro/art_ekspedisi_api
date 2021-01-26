<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use App\User;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::create([
            'name' => 'Dedyk',
            'identity_id' => '12345678345',
            'gender' => 1,
            'address' => 'Jl Saja Dulu',
            'photo' => 'dedyk.png', //note: tidak ada gambar
            'email' => 'dedyk.ds@gmail.com',
            'password' => app('hash')->make('dsa,1992'),
            'phone_number' => '08564656407',
            'api_token' => Str::random(40),
            'role' => 0,
            'status' => 1
        ]);
    }
}
