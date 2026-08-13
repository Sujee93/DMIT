<?php

namespace Database\Seeders;

use App\Models\Customer;
use Illuminate\Database\Seeder;

class CustomerSeeder extends Seeder
{
    /**
     * Seed the fixed companies used on the Tax Invoice template.
     */
    public function run(): void
    {
        Customer::updateOrCreate(
            ['key' => 'singer'],
            [
                'name' => 'Singer Sri Lanka PLC',
                'address' => "No. 80, Nawam Mawatha,\nColombo 02.",
                'tin_number' => '124008026',
                'is_fixed' => true,
            ]
        );

        Customer::updateOrCreate(
            ['key' => 'arpico'],
            [
                'name' => 'Richard Peiris Distributors Ltd.',
                'address' => "No. 310, High Level Road,\nNavinna, Maharagama.",
                'tin_number' => '124009065',
                'is_fixed' => true,
            ]
        );

        Customer::updateOrCreate(
            ['key' => 'ramadia'],
            [
                'name' => 'Ramadia Ranmal Holiday Resort',
                'address' => '346, 5 Old Galle Rd, Moratuwa',
                'tin_number' => '114133841',
                'telephone' => '0382 298 921',
                'is_fixed' => true,
            ]
        );
    }
}
