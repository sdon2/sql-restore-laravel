<?php

namespace SqlRestoreLaravel\Commands;

use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use Spatie\TemporaryDirectory\TemporaryDirectory;
use SqlRestoreLaravel\Classes\ZipReader;
use Symfony\Component\Process\Process;

class AppMeditibbInit extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sql:restore {--Z|zip-file=database/dumps/db_dump.zip} {--S|sql-file=db_dump.sql}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Initializes appmeditibb database';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    protected function init()
    {
        ini_set('memory_limit', '-1');
        set_time_limit(0);
    }

    protected function createTempFile()
    {
        
        $temporaryDirectory = (new TemporaryDirectory())->force()->create();
        $dumpfile = App::basePath($this->option('zip-file'));
        $reader = new ZipReader($dumpfile);
        $sql = $reader->readContents($this->option('sql-file'));
        $temp_file = $temporaryDirectory->path($this->option('sql-file'));

        if (file_put_contents($temp_file, $sql) !== false) {
            return $temp_file;
        } else {
            return false;
        }
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->useCommandLine();
    }

    protected function useCommandLine()
    {
        $this->init();

        $this->info('Executing MySQL cmd::');

        try {
            if ($temp_file = $this->createTempFile()) {

                $host =  Config::get('database.connections.mysql.host');
                $username = Config::get('database.connections.mysql.username');
                $password = Config::get('database.connections.mysql.password');
                $database = Config::get('database.connections.mysql.database');

                $process = new Process(['mysql', "--host=$host", "--user=$username", "--password=$password", "--database=$database", "--execute=SOURCE " . $temp_file], null, null, null, null);
                $process->start();

                $process->wait();

                if ($process->getExitCode() != 0) {
                    $this->error($process->getErrorOutput());
                } else {
                    $this->comment($process->getOutput());
                    $this->info('Importing dump file using MySQL Command Completed');
                }

                unlink($temp_file);
            } else {
                throw new Exception('Unable to create temp file');
            }
        } catch (Exception $ex) {
            $this->error("Error: " . $ex->getMessage());
        }
    }
}
