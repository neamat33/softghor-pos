<?php
namespace Database\Seeders;
use App\Product;
use App\Services\ProductService;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $product = Product::create([
            // 'type' => 'standard',
            'name' => 'Mobile Phone',
            'code' => '000001',
            'category_id' => random_int(1, 5),
            'brand_id' => random_int(1, 5),
            'cost' => 4150,
            'price' => 4500,
            'image' => 'dashboard/images/not-available.png',
            'main_unit_id'=>1
        ]);
        $data= (object)[
            'opening_stock'=>[100],
        ];
        ProductService::make_opening_stock_purchase($data,$product);

        $product = Product::create([
            // 'type' => 'standard',
            'name' => 'laptop Comouter',
            'code' => '000002',
            'category_id' => random_int(1, 5),
            'brand_id' => random_int(1, 5),
            'cost' => 72000,
            'price' => 78000,
            'image' => 'dashboard/images/not-available.png',
            'main_unit_id'=>1
        ]);
        ProductService::make_opening_stock_purchase($data,$product);

        $product = Product::create([
            // 'type' => 'standard',
            'name' => 'Desktop Computer',
            'code' => '000003',
            'category_id' => random_int(1, 5),
            'brand_id' => random_int(1, 5),
            'cost' => 375,
            'price' => 458,
            'image' => 'dashboard/images/not-available.png',
            'main_unit_id'=>1
        ]);
        ProductService::make_opening_stock_purchase($data,$product);

        $product =Product::create([
            // 'type' => 'standard',
            'name' => 'T Shirt',
            'code' => '000004',
            'category_id' => random_int(1, 5),
            'brand_id' => random_int(1, 5),
            'cost' => 1200,
            'price' => 1500,
            'image' => 'dashboard/images/not-available.png',
            'main_unit_id'=>1
        ]);
        ProductService::make_opening_stock_purchase($data,$product);

        $product = Product::create([
            // 'type' => 'standard',
            'name' => 'Ladis Shirt',
            'code' => '000005',
            'category_id' => random_int(1, 5),
            'brand_id' => random_int(1, 5),
            'cost' => 700,
            'price' => 900,
            'image' => 'dashboard/images/not-available.png',
            'main_unit_id'=>1
        ]);
        ProductService::make_opening_stock_purchase($data,$product);

        $product = Product::create([
            // 'type' => 'standard',
            'name' => 'Freez',
            'code' => '000006',
            'category_id' => random_int(1, 5),
            'brand_id' => random_int(1, 5),
            'cost' => 4200,
            'price' => 4500,
            'image' => 'dashboard/images/not-available.png',
            'main_unit_id'=>1
        ]);
        ProductService::make_opening_stock_purchase($data,$product);

        $product = Product::create([
            // 'type' => 'standard',
            'name' => 'Air Condition',
            'code' => '000007',
            'category_id' => random_int(1, 5),
            'brand_id' => random_int(1, 5),
            'cost' => 91350,
            'price' => 96000,
            'image' => 'dashboard/images/not-available.png',
            'main_unit_id'=>1
        ]);
        ProductService::make_opening_stock_purchase($data,$product);



        $product =Product::create([
            // 'type' => 'standard',
            'name' => 'Door Export',
            'code' => '000008',
            'category_id' => random_int(1, 5),
            'brand_id' => random_int(1, 5),
            'cost' => 13945,
            'price' => 15000,
            'image' => 'dashboard/images/not-available.png',
            'main_unit_id'=>1
        ]);
        ProductService::make_opening_stock_purchase($data,$product);

        $product = Product::create([
            // 'type' => 'standard',
            'name' => 'Blajer For Men',
            'code' => '000009',
            'category_id' => random_int(1, 5),
            'brand_id' => random_int(1, 5),
            'cost' => 2500,
            'price' => 3000,
            'image' => 'dashboard/images/not-available.png',
            'main_unit_id'=>1
        ]);
        ProductService::make_opening_stock_purchase($data,$product);

        $product = Product::create([
            // 'type' => 'standard',
            'name' => 'Dril Machine',
            'code' => '000010',
            'category_id' => random_int(1, 5),
            'brand_id' => random_int(1, 5),
            'cost' => 2750,
            'price' => 3000,
            'image' => 'dashboard/images/not-available.png',
            'main_unit_id'=>1
        ]);
        ProductService::make_opening_stock_purchase($data,$product);

        $product = Product::create([
            // 'type' => 'standard',
            'name' => 'Gaming Laptop',
            'code' => '000011',
            'category_id' => random_int(1, 5),
            'brand_id' => random_int(1, 5),
            'cost' => 145200,
            'price' => 150000,
            'image' => 'dashboard/images/not-available.png',
            'main_unit_id'=>1
        ]);
        ProductService::make_opening_stock_purchase($data,$product);
    }
}
