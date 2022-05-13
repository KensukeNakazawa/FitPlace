<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

use App\Models\Auth;
use Illuminate\Support\Facades\Hash;


class AuthTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Auth::create(
            [
                'email' => 'infokensuke.n@gmail.com',
                'password' => Hash::make('s9w3i58m')
            ]
        );
    }
}
