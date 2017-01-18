<?php

namespace App\Http\Controllers;

use Elasticsearch;
use Illuminate\Http\Request;
use Laravel\Lumen\Routing\Controller as Controller;

class HomeController extends Controller
{
    public function index()
    {
        return view('home');
    }

    public function post(Request $request)
    {
        // User input from the search form.
        $input = 'kumara'; // $request->input('input');

        $hosts = [
            '127.0.0.1:9200'
        ];


        // Instantiate a new client, set hosts and build it
        $client = Elasticsearch\ClientBuilder::create()
                                                ->setHosts($hosts)
                                                ->build();

        // Set the query parameters.

        // Parameters.
        $parameters = [
            'index' => 'folklor',
            'type' => 'story',
            'body' =>  [
                'query' => [
                    'multi_match' => [
                        'query' => $input,
                        'fields' => ['title^5','text','category^2', 'name^5', 'place^5', 'municipality^3', 'region^2']
                    ]
                ],
                'aggs' => [
                    'tellers' => [
                        'terms' => [ 'field' => 'name.raw' ]
                    ],
                    'places' => [
                        'terms' => [ 'field' => 'place.raw' ]
                    ],
                    'categories' => [
                        'terms' => [ 'field' => 'category.raw' ]
                    ],
                    'volumes' => [
                        'terms' => [ 'field' => 'volume.raw' ]
                    ],
                    'municipality' => [
                        'terms' => [ 'field' => 'municipality.raw' ]
                    ],
                    'region' => [
                        'terms' => [ 'field' => 'region.raw' ]
                    ],
                    'year' => [
                        'terms' => [ 'field' => 'year' ]
                    ],
                    'location' => [
                        'terms' => [ 'field' => 'location.raw' ]
                    ]
                ]
            ]
        ];

        // Do the search.
        $search = $client->search($parameters);

        // Go directly to the point where are the data.
        $outter_hits = $search['hits'];
        $inner_hits = $outter_hits['hits'];

        // How many hits are returned?
        $hits = count($inner_hits);

        // Return output and pass it to the view.
        return view('home', ['inner_hits' => $inner_hits, 'hits' => $hits]);
    }
}
