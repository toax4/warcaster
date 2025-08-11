<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\View;

class WarhammerAlgoliaService
{
    public static function fetch($facetFilters) {
        return Http::withHeaders([
            'Content-Type' => 'application/json',
            'x-algolia-api-key' => env("WARHAMMER_ALGOLIA_API_KEY", ""), // trouvée dans le payload ou config
            'x-algolia-application-id' => env("WARHAMMER_ALGOLIA_APP_ID", ""), // trouvé dans l’URL Algolia
            'x-algolia-agent' => 'Algolia for JavaScript (4.20.0); Browser; ...'
        ])->post(
            'https://'.strtolower(env("WARHAMMER_ALGOLIA_APP_ID", "")).'-dsn.algolia.net/1/indexes/*/queries',
            [
                "requests" => [self::buildPostData($facetFilters)]
            ]
        );
    }

    public static function buildPostData($facetFilters) {
        $myRequest = [
            "indexName" => "prod-lazarus-product-fr-fr",
            "params" => [
                "facetFilters" => $facetFilters,
                "hitsPerPage" => 100,
                "page" => 0,
                "query" => "",
                // "clickAnalytics" => true,
                "facets" => "['GameSystemsRoot.lvl0','brushType','format','genre','isAvailableWhileStocksLast','isLastChanceToBuy','isMadeToOrder','isNewRelease','isPreOrder','isPrintOnDemand','isWebstoreExclusive','material','paintColourRange paintType','productType','series']",
                // "filters" => "isNewRelease:'true'",
                // "highlightPostTag" => "__/ais-highlight__",
                // "highlightPreTag" => "__ais-highlight__",
                // "maxValuesPerFacet" => 101,
            ] 
        ];

        $myRequest["params"]["facetFilters"] = json_encode($myRequest["params"]["facetFilters"]);
        $myRequest["params"] = http_build_query($myRequest["params"]);

        return $myRequest;
    }
}