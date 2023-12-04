<?php
namespace Database\Seeders;
use App\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Category::create([
            'name' => 'Electronics',
            // 'code' => '45478'
        ]);
        Category::create([
            'name' => 'House',
            // 'code' => '7845'
        ]);
        Category::create([
            'name' => 'Fashion',
            // 'code' => '8956'
        ]);
        Category::create([
            'name' => 'Hardware',
            // 'code' => '3543434'
        ]);
        Category::create([
            'name' => 'Document',
            // 'code' => '343342'
        ]);
    }
}
