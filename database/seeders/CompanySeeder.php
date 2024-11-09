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
                    'name' => 'PT. Cahaya Solusindo Internusa',
                    'address' => 'Jl. Letjen S Parman No.58, Sumberrejo, Pakis, Kec. Banyuwangi, Kabupaten Banyuwangi, Jawa Timur 68419',
                    'phone' => '6282111777179',
                ],

            ]
        );
    }
}
