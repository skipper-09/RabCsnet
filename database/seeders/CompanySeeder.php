<?php

namespace Database\Seeders;

use App\Models\Company;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CompanySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Company::insert(
            [
                [
                    'id'=>'3c795b1b-9354-46db-8ad0-156dc52159e4',
                    'name' => 'PT. Cahaya Solusindo Internusa',
                    'address' => 'Jl. Letjen S Parman No.58, Sumberrejo, Pakis, Kec. Banyuwangi, Kabupaten Banyuwangi, Jawa Timur 68419',
                    'phone' => '6282111777179',
                ],
                [
                    'id'=> '3c795b1b-9354-46db-8ad0-156dc52159e5',
                    'name'=> 'PT. Internusa Duta Makmur',
                    'address'=> 'Ruko Gajah Mada Square, Jl. Gajah Mada.187 Blok A 21, Kaliwates Kidul, Kaliwates, Kec. Kaliwates, Kabupaten Jember, Jawa Timur 68121',
                    'phone'=> '6282116879977',
                ]
            ]
        );
    }
}
