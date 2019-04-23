<?php

namespace App\Commands;

use App\Downloader;
use GuzzleHttp\Client;
use Exception;
use Illuminate\Console\Scheduling\Schedule;
use LaravelZero\Framework\Commands\Command;

class StartCommand extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'start {url}';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Display an inspiring quote';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {

        $this->info('.:: Iniciando aplicação ::.');

        $url = $this->argument('url');
        if (!preg_match("/mercadolivre\.com\.br/", parse_url($url, PHP_URL_HOST))) {
            $this->error("Error: A URL ($url) não faz parte do MercadoLivre.com.br");
            die();
        }


        /*
         * Dependencies
         */
        $client = new Client();

        /*
         * App
         */
        $app = new Downloader($client, $url, $this);

        try {
            $app->start();
        } catch (Exception $e) {
            echo 'ERROR: ' . $e->getMessage();
        }
    }

    /**
     * Define the command's schedule.
     *
     * @param \Illuminate\Console\Scheduling\Schedule $schedule
     * @return void
     */
    public function schedule(Schedule $schedule)
    {
        // $schedule->command(static::class)->everyMinute();
    }
}
