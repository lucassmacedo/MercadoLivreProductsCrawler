<?php
/**
 * Main cycle of the app
 */

namespace App;

use GuzzleHttp\Client as HttpClient;
use Illuminate\Support\Facades\Storage;
use League\Flysystem\Filesystem;

/**
 * Class Downloader
 * @package App
 */
class Downloader
{
    /**
     * Http Client object
     * @var $client
     */
    private $client;

    /**
     * Http Resolver object
     * @var $resolver
     */
    private $resolver;

    /**
     * URL Loja Mercado Livre
     * @var $url
     */
    private $url;

    /**
     * Receives dependencies
     *
     * @param HttpClient $client
     * @param  $url
     * @param  $console
     */
    public function __construct(HttpClient $client, $url, $console)
    {
        $this->client = $client;
        $this->url = $url;
        $this->resolver = new Resolver($client, $console);
    }

    /**
     * Start the logic
     *
     */
    public function start()
    {
        $array = [];

        $localLessons = $this->resolver->getAllProducts($this->url);
        foreach ($localLessons as $localLesson) {
            $array[] = $this->resolver->getProductInfo($localLesson);
        }


        /**
         * Write your Logic
         *
         */

        Storage::disk('local')->put('data.json', json_encode($array));
    }

}
