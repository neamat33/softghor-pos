<?php
namespace Database\Seeders;
use App\Brand;
use Illuminate\Database\Seeder;

class BrandSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Brand::create([
            'name' => 'Apple',
            'slug' => str_slug('Apple'),
            'description' => 'Apple Brand Description'
        ]);
        Brand::create([
            'name' => 'Microsoft',
            'slug' => str_slug('Microsoft'),
            'description' => 'Microsoft Brand Description'
        ]);
        Brand::create([
            'name' => 'Nokia',
            'slug' => str_slug('Nokia'),
            'description' => 'Nokia Brand Description'
        ]);
        Brand::create([
            'name' => 'Samsung',
            'slug' => str_slug('Samsung'),
            'description' => 'Sumsang Brand Description'
        ]);
        Brand::create([
            'name' => 'Sony',
            'slug' => str_slug('Sony'),
            'description' => 'Sony Brand Description'
        ]);
    }
}
