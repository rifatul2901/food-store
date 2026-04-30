<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class EmployeeSeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;'); // matikan foreign key checkclear
        DB::statement('SET FOREIGN_KEY_CHECKS=1;'); // hidupkan lagi
        $employees = [
            [
                'name'      => 'Rifatul Nabil',
                'email'     => 'rifatul@york-store.my.id',
                'password'  => Hash::make('rifatul'),
                'position'  => 'Kasir',
            ],
            [
                'name'      => 'Charistia',
                'email'     => 'charistia@york-store.my.id',
                'password'  => Hash::make('charistia'),
                'position'  => 'Stok Barang',
            ],
            [
                'name'      => 'Billy',
                'email'     => 'billy@york-store.my.id',
                'password'  => Hash::make('billy'),
                'position'  => 'Pengiriman',
            ],
            [
                'name'      => 'Chrisma',
                'email'     => 'chrisma@york-store.my.id',
                'password'  => Hash::make('chrisma'),
                'position'  => 'Customer Service',
            ],
            [
                'name'      => 'Liunsanda',
                'email'     => 'liunsanda@york-store.my.id',
                'password'  => Hash::make('liunsanda'),
                'position'  => 'Supervisor',
            ],
        ];

        DB::table('employees')->insert($employees);
    }
}