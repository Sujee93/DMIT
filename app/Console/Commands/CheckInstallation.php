<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Throwable;

/**
 * Diagnoses common hosting problems (cPanel etc.): php artisan app:check
 */
class CheckInstallation extends Command
{
    protected $signature = 'app:check';

    protected $description = 'Check the server, database and folder permissions for common installation problems';

    private int $problems = 0;

    public function handle(): int
    {
        $this->info('Checking installation...');
        $this->newLine();

        $this->line('<comment>PHP</comment>');
        $this->check('PHP version '.PHP_VERSION.' (8.1+ required)', version_compare(PHP_VERSION, '8.1.0', '>='),
            'Select PHP 8.1 or newer in cPanel > MultiPHP Manager. The terminal may use a different PHP than the website.');
        foreach (['pdo_mysql', 'mbstring', 'openssl', 'tokenizer', 'xml', 'ctype', 'fileinfo', 'gd'] as $ext) {
            $this->check("Extension {$ext}", extension_loaded($ext), "Enable {$ext} in cPanel > Select PHP Version > Extensions.");
        }

        $this->newLine();
        $this->line('<comment>Configuration</comment>');
        $this->check('APP_KEY is set', (string) config('app.key') !== '', 'Run: php artisan key:generate');
        $this->check('APP_DEBUG is off', ! config('app.debug'), 'Set APP_DEBUG=false in .env on a live server.', warning: true);
        $this->check('APP_URL = '.config('app.url'), (bool) filter_var(config('app.url'), FILTER_VALIDATE_URL),
            'Set APP_URL in .env to the exact address you open in the browser.');
        if (file_exists(base_path('bootstrap/cache/config.php'))) {
            $this->line('  <fg=yellow>!</> Config is cached - after editing .env run: php artisan config:clear (then config:cache again)');
        }

        $this->newLine();
        $this->line('<comment>Database</comment>');
        try {
            DB::connection()->getPdo();
            $this->check('Connected to '.config('database.connections.'.config('database.default').'.database'), true);
            $version = DB::selectOne('select version() as v')->v ?? '?';
            $this->line("  Server version: {$version}");

            foreach (['users', 'sessions', 'business_settings', 'products', 'contacts', 'invoices', 'invoice_items', 'customer_payments', 'supplier_payments'] as $table) {
                $this->check("Table {$table}", Schema::hasTable($table), 'Run: php artisan migrate --force');
            }

            $pending = $this->pendingMigrations();
            $this->check('All migrations have run', $pending === [], 'Pending: '.implode(', ', $pending).'. Run: php artisan migrate --force');

            if (Schema::hasTable('users')) {
                $this->check('An administrator account exists', DB::table('users')->where('role', 'admin')->where('is_active', true)->exists(),
                    'Run: php artisan db:seed --force');
            }
        } catch (Throwable $e) {
            $this->check('Database connection', false, 'Check DB_HOST, DB_DATABASE, DB_USERNAME, DB_PASSWORD in .env. Error: '.$e->getMessage());
        }

        $this->newLine();
        $this->line('<comment>Folders</comment>');
        foreach (['storage/app/public', 'storage/framework/cache', 'storage/framework/sessions', 'storage/framework/views', 'storage/logs', 'bootstrap/cache'] as $dir) {
            $path = base_path($dir);
            $this->check("{$dir} is writable", is_dir($path) && is_writable($path), "Run: mkdir -p {$dir} && chmod -R 775 {$dir}");
        }
        $link = public_path('storage');
        $this->check('public/storage link exists (logo uploads)', is_link($link) || is_dir($link),
            'Run: php artisan storage:link   (if symlinks are blocked: ln -s '.storage_path('app/public').' '.$link.')');

        try {
            Cache::put('app-check', 'ok', 10);
            $this->check('Cache read/write', Cache::get('app-check') === 'ok', 'Check that storage/framework/cache is writable.');
            Cache::forget('app-check');
        } catch (Throwable $e) {
            $this->check('Cache read/write', false, $e->getMessage());
        }

        $this->newLine();
        if ($this->problems === 0) {
            $this->info('Everything looks good.');
        } else {
            $this->error("{$this->problems} problem(s) found - fix the items marked with x above.");
        }
        $this->line('Recent errors are logged in storage/logs/ (search for the error code shown on screen).');

        return $this->problems === 0 ? self::SUCCESS : self::FAILURE;
    }

    private function check(string $label, bool $ok, string $fix = '', bool $warning = false): void
    {
        if ($ok) {
            $this->line("  <fg=green>✓</> {$label}");

            return;
        }

        $this->line($warning ? "  <fg=yellow>!</> {$label}" : "  <fg=red>x</> {$label}");
        if ($fix !== '') {
            $this->line("      <fg=gray>{$fix}</>");
        }
        if (! $warning) {
            $this->problems++;
        }
    }

    /**
     * @return array<int, string>
     */
    private function pendingMigrations(): array
    {
        if (! Schema::hasTable('migrations')) {
            return ['all'];
        }

        $ran = DB::table('migrations')->pluck('migration')->all();
        $files = array_map(fn ($f) => basename($f, '.php'), glob(database_path('migrations/*.php')) ?: []);

        return array_values(array_diff($files, $ran));
    }
}
