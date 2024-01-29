<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
  /**
   * Seed the application's database.
   */
  public function run(): void
  {
    DB::beginTransaction();

    try {
      \App\Models\Role::factory()->createMany([
        [
          'name' => 'ADMIN',
          'description' => 'Administrator',
        ],
        [
          'name' => 'GENERIC',
          'description' => 'Generic user',
        ],
      ]);

      DB::commit();
    } catch (\Exception $e) {
      error_log("\n[-] Error: duplicate key value roles\n");

      DB::rollBack();

      return;
    }
  }
}
