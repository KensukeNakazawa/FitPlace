<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

use App\Models\Admin;

class AdminTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = Admin::create(
            [
                'login_id' => 'infokensuke.n@gmail.com',
                'password' => Hash::make('s9w3i58m'),
                'name' => 'Kensuke-N',
                'authority' => '1'
            ]
        );
    }
}
