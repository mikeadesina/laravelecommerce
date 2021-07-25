<?php

namespace Database\Seeders;

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
        //
        //factory(App\Product::class,50)->create();
        //\App\Models\Product::factory()->times(60)->create();
        Product::factory()->times(60)->create();
    }
}
