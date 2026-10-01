<?php

namespace Database\Seeders;

use App\Enums\ContactType;
use App\Models\Contact;
use App\Models\Product;
use Illuminate\Database\Seeder;

/**
 * Optional sample data for trying the system out:
 *   php artisan db:seed --class=DemoSeeder
 */
class DemoSeeder extends Seeder
{
    public function run(): void
    {
        $products = [
            ['SN-1001', 'Urban Runner Sneaker', 'Black', '42', 2850, 3900],
            ['SN-1002', 'Urban Runner Sneaker', 'White', '41', 2850, 3900],
            ['FM-2001', 'Classic Oxford Formal', 'Brown', '43', 4200, 5750],
            ['SD-3001', 'Comfort Strap Sandal', 'Tan', '40', 1150, 1650],
            ['SL-4001', 'Kids Velcro School Shoe', 'Black', '33', 1700, 2400],
            ['BT-5001', 'Rugged Work Boot', 'Olive', '44', 5600, 7400],
        ];

        foreach ($products as [$code, $name, $color, $size, $cost, $price]) {
            Product::query()->firstOrCreate(['code' => $code], compact('name', 'color', 'size', 'cost', 'price') + ['is_active' => true]);
        }

        $contacts = [
            [ContactType::Supplier, 'Lanka Footwear Mfg.', 'Lanka Footwear (Pvt) Ltd', '0112 345 678'],
            [ContactType::Supplier, 'Step Up Imports', 'Step Up Imports', '0117 654 321'],
            [ContactType::Customer, 'City Shoe Palace', 'City Shoe Palace', '0771 234 567'],
            [ContactType::Customer, 'Galle Road Footwear', 'Galle Road Footwear', '0712 345 678'],
            [ContactType::Customer, 'Kandy Kids Corner', null, '0751 112 233'],
        ];

        foreach ($contacts as [$type, $name, $company, $phone]) {
            Contact::query()->firstOrCreate(['name' => $name, 'type' => $type], compact('company', 'phone') + ['is_active' => true]);
        }
    }
}
