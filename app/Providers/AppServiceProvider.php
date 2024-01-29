<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class AppServiceProvider extends ServiceProvider
{
  /**
   * Register any application services.
   */
  public function register(): void
  {
    //
  }

  /**
   * Bootstrap any application services.
   */
  public function boot(): void
  {
    // Validar la conexión con la base de datos
    try {
      DB::connection()->getPdo();
    } catch (\Exception $e) {
      error_log("\n[-] Error: " . $e->getMessage() . "\n");

      exit(1);
    }

    // Validaciones antes de ejecutar el servidor
    $running_in_console = app()->runningInConsole();

    $command = $running_in_console ? $_SERVER['argv'][1] ?? null : null;

    if ($running_in_console && $command == 'serve') {
      // Validar si las migraciones se han ejecutado
      try {
        $migrations_pending = Artisan::call('migrate:status') > 0;

        if ($migrations_pending) {
          throw new \Exception('Migrations have not been executed. Execute \'php artisan migrate\' to run them.');
        }
      } catch (\Exception $e) {
        error_log("\n[-] Error: " . $e->getMessage() . "\n");

        exit(1);
      }

      // Validar si los seeders se han ejecutado
      try {
        $exist_table_roles = Schema::hasTable('roles');

        $exist_data_in_table_roles = DB::table('roles')->exists();

        if ($exist_table_roles && !$exist_data_in_table_roles) {
          throw new \Exception('Seeders have not been executed. Execute \'php artisan db:seed\' to run them.');
        }
      } catch (\Exception $e) {
        error_log("\n[-] Error: " . $e->getMessage() . "\n");

        exit(1);
      }
    }
  }
}
