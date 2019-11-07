<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
         $this->call(UsersSeeder::class);
         $this->call(TrackBySeeder::class);
         $this->call(ServiceTypeSeeder::class);
         $this->call(ServiceProviderTypeSeeder::class);
         $this->call(FuelBalanceSeeder::class);
         $this->call(ExpensesSeeder::class);
         $this->call(FuelTypeSeeder::class);
         $this->call(FuelSupplierSeeder::class);
         $this->call(ExpiryDateSeeder::class);


    }
}
