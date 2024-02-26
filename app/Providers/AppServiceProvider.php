<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

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
    if (env('APP_ENV') !== 'production') {
      // Validar la conexión con la base de datos
      try {
        DB::connection()->getPdo();
      } catch (\Exception $e) {
        Log::error('CONNECTION TO DATABASE', [
          'STATUS' => 'ERROR',
          'MESSAGE' => $e->getMessage(),
          'LINE_CODE' => $e->getLine(),
          'ACTION' => 'Exception to connect to database',
          'CONTROLLER' => AppServiceProvider::class,
          'METHOD' => 'boot',
          'TRACE' => $e->getTraceAsString(),
          'FILE' => $e->getFile(),
        ]);

        error_log("\n[-] ERROR: " . $e->getMessage() . "\n");

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
          Log::error('MIGRATIONS HAVE NOT BEEN EXECUTED', [
            'STATUS' => 'ERROR',
            'MESSAGE' => $e->getMessage(),
            'LINE_CODE' => $e->getLine(),
            'ACTION' => 'Exception to run server',
            'CONTROLLER' => AppServiceProvider::class,
            'METHOD' => 'boot',
            'TRACE' => $e->getTraceAsString(),
            'FILE' => $e->getFile(),
          ]);

          error_log("\n[-] ERROR: " . $e->getMessage() . "\n");

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
          Log::error('SEEDERS HAVE NOT BEEN EXECUTED', [
            'STATUS' => 'ERROR',
            'MESSAGE' => $e->getMessage(),
            'LINE_CODE' => $e->getLine(),
            'ACTION' => 'Exception to run server',
            'CONTROLLER' => AppServiceProvider::class,
            'METHOD' => 'boot',
            'TRACE' => $e->getTraceAsString(),
            'FILE' => $e->getFile(),
          ]);

          error_log("\n[-] ERROR: " . $e->getMessage() . "\n");

          exit(1);
        }
      }
    }
  }
}
