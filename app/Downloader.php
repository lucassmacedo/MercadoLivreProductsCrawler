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

        $products = $this->resolver->getAllProducts($this->url);
        foreach ($products as $product) {
            $array[] = $this->resolver->getProductInfo($product);
        }


        /**
         * Write your Logic
         *
         */

        $fp = fopen(storage_path('data.csv'), 'w');

        foreach ($array as $linha) {
            fputcsv($fp, $linha);
        }

        fclose($fp);


    }

}
