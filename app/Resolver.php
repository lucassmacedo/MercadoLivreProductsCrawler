<?php
/**
 * Http functions
 */

namespace App;

use App\Html\Parser;
use GuzzleHttp\Client;
use GuzzleHttp\Cookie\CookieJar;

/**
 * Class Resolver
 * @package App\Http
 */
class Resolver
{
    /**
     * Guzzle client
     * @var Client
     */
    private $client;

    /**
     * Guzzle cookie
     * @var CookieJar
     */
    private $cookie;


    private $console;

    /**
     * Receives dependencies
     *
     * @param Client $client
     * @param Ubench $bench
     * @param bool $retryDownload
     */
    public function __construct(Client $client, $console)
    {
        $this->client = $client;
        $this->console = $console;
        $this->cookie = new CookieJar();
    }

    /**
     * Grabs all lessons & series from the website.
     */
    public function getAllProducts($url)
    {
        $array = [];

        $html = $this->getAllPage($url);

        Parser::getAllProducts($html, $array);

        while ($nextPage = Parser::hasNextPage($html)) {
            $this->console->info(".:: Buscando produtos {$nextPage}  ::.");
            $html = $this->getAllPage($nextPage);
            Parser::getAllProducts($html, $array);
        }

        return $array;
    }


    public function getProductInfo($url)
    {
        $array = [];

        $html = $this->getAllPage($url);

        Parser::getInfosProduct($html, $array);

        $this->console->info("\n .:: Informações obtidas do produto: ** {$array['title']} **  ::.");
        return $array;
    }


    /**
     * Gets the html from the all page.
     *
     * @return string
     *
     * @throws \Exception
     */
    private function getAllPage($url)
    {
        try {
            $response = $this->client->get($url);
            return $response->getBody()->getContents();
        } catch (\Exception $exception) {
           $this->console->error("Error: A URL ($url) não foi encontrada");
        }

    }

}
