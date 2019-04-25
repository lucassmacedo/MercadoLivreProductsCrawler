<?php
/**
 * Dom Parser
 */

namespace App\Html;

use Symfony\Component\DomCrawler\Crawler;

/**
 * Class Parser
 * @package App\Html
 */
class Parser
{
    /**
     * Extrai o HTML por expressão regular  e retorna o array com as informações principais do produto
     *
     * @param $html
     * @param $array
     */
    public static function getInfosProduct($html, &$array)
    {
        $parser = new Crawler($html);
        $array = [];
        if (preg_match('/({"item_id")([\s\S]*?)(?=\))/', $html, $matches)) {
            try {
                $melidata = json_decode($matches[0]);
                if (empty($melidata)) {
                    throw new \Exception();
                }
                $array = [
                    'price'         => $melidata->recommendations->track_info->trigger->item_info->price,
                    'title'         => $melidata->recommendations->track_info->trigger->item_info->title,
                    'free_shipping' => $melidata->recommendations->track_info->trigger->item_info->free_shipping,
                ];

                if (isset($melidata->recommendations) && isset($melidata->recommendations->track_info->trigger->item_info->attributes)) {
                    foreach ($melidata->recommendations->track_info->trigger->item_info->attributes as $item) {
                        if ($item->id == 'PART_NUMBER') {
                            $array['id'] = $item->value_id;
                        }
                    }
                }
                dd($array);
                $array['description'] = trim($parser->filter('.item-description__text')->text());
                $array['image'] = trim($parser->filter('.gallery-trigger')->attr('href'));
            } catch (\Exception $exception) {
                dd($exception->getMessage(), $melidata);
            }
        }
    }

    /**
     * Extrai o HTML contendo o link dos produtos e retorna um array contendo o link dos produtos
     *
     * @param $html
     * @param $array
     */
    public static function getAllProducts($html, &$array)
    {
        $parser = new Crawler($html);
        $parser->filter('.item-link')->each(function (Crawler $node) use (&$array) {
            $link = $node->attr('href');
            $array[] = $link;
        });
    }

    /**
     * Deterina se exist euma nova página e retorna o link ou false se não existir.
     *
     * @param $html
     *
     * @return bool|string
     */
    public static function hasNextPage($html)
    {
        $parser = new Crawler($html);
        $node = $parser->filter('.andes-pagination__button--next:not(.andes-pagination__button--disabled)');
        if ($node->count() > 0) {
            return $node->children()->attr('href');
        }
        return false;
    }

}
