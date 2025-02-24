<?php

namespace App\Helper;

use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;

class Censurator
{
    public function __construct(private readonly ContainerBagInterface $params)
    {

    }

    public function purify(string $string): string {
        $mots_bannis = file_get_contents($this->params->get('app.censurator_file'));

        $mots_bannis = array_filter(explode("\r\n", $mots_bannis));
        //dd($mots_bannis);
        $patterns = array_map(function ($word) {
            return "/\b" . preg_quote($word, '/') . "\b/i"; // Ajoute les limites de mots et rend insensible à la casse
        }, $mots_bannis);

        return preg_replace($patterns, "*", $string);
    }
}