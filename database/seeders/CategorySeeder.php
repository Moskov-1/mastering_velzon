<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            ['id' => 1, 'name' => 'About Us', 'type' => Category::_types()['BREAKFAST'], 
            'status' => '1'],
            ['id' => 2, 'name' => 'Terms & Conditions', 'type' => Category::_types()['BREAKFAST'], 
            'status' => '1'],
            ['id' => 3, 'name' => 'Privacy Policy', 'type' => Category::_types()['BREAKFAST'], 
            'status' => '1'],
        ];

        foreach ($data as $record) {
            if (!Category::where('id', $record['id'])->exists()) {
                Category::create($record);
            }
        }

    }
}
