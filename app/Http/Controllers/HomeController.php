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
        $input = $request->input('input');

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
            'size' => 100,
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

        // Your search returned X results.
        if ($hits == 0)
        {
            $results = 'no results';
        }
        elseif ($hits == 1)
        {
            $results = $hits.' result';
        }
        else
        {
            $results = $hits.' results';
        }

        // Output hits, results, etc.
        $output = '
                <h3>Your search returned '.$results.'.</h3>

                <table class="table table-bordered table-responsive table-striped">
                    <thead>
                        <th><i class="fa fa-hashtag"></i></th>
                        <th>Category</th>
                        <th>Title</th>
                        <th>Volume</th>
                        <th>Page</th>
                        <th>Number</th>
                        <th>Year</th>
                        <th>Score</th>
                        <th>Map</th>
                    </thead>
                    <tbody>';

        // Loop the array and concatenate (with $var .= 'value';) the $output variable.
        for ($h = 0; $h < $hits; $h++) {

            $i = $h + 1;

            $category = $inner_hits[$h]['_source']['category'];
            if (empty($category))
            {
                $category = '<small>N/A</small>';
            }

            $title = $inner_hits[$h]['_source']['title'];
            if (empty($title))
            {
                $title = '<small>N/A</small>';
            }

            $volume = $inner_hits[$h]['_source']['volume'];
            if (empty($volume))
            {
                $volume = '<small>N/A</small>';
            }

            $page = $inner_hits[$h]['_source']['page'];
            if (empty($page))
            {
                $page = '<small>N/A</small>';
            }

            $nr = $inner_hits[$h]['_source']['nr'];
            if (empty($nr))
            {
                $nr = '<small>N/A</small>';
            }

            $year = $inner_hits[$h]['_source']['year'];
            if (empty($year))
            {
                $year = '<small>N/A</small>';
            }

            $_score = $inner_hits[$h]['_score'];
            $_score = $_score * 100;
            $_score = round($_score,0);

            $lat = $inner_hits[$h]['_source']['lat'];

            $lon = $inner_hits[$h]['_source']['lon'];

            $map = '<a href="https://www.google.com.br/maps/place//@'.$lat.','.$lon.',9z/data=!4m5!3m4!1s0x0:0x0!8m2!3d'.$lat.'!4d'.$lon.'" target="_blank"><i class="fa fa-map-marker"></i></a>';

            if (empty($lat) or empty($lon))
            {
                $map = '<i class="fa fa-map-marker"></i>';
            }


            $text = $inner_hits[$h]['_source']['text'];
            if (empty($text))
            {
                $text = '<small>N/A</small>';
            }

            $output .= '
                <tr>
                    <td>'.$i.'</td>
                    <td>'.$category.'</td>
                    <td>'.$title.'</td>
                    <td>'.$volume.'</td>
                    <td>'.$page.'</td>
                    <td>'.$nr.'</td>
                    <td>'.$year.'</td>
                    <td>'.$_score.'</td>
                    <td>'.$map.'</td>';
        }

        $output .= '
                    </tbody>
                </table>';

        // Return output and pass it to the view.
        return view('home', ['inner_hits' => $inner_hits, 'output' => $output]);
    }
}
