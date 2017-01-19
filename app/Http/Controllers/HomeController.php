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
                <h3>Your search returned '.$results.'</h3>

                <hr>';

        // Loop the array and concatenate (with $var .= 'value';) the $output variable.
        for ($h = 0; $h < $hits; $h++) {

            $i = $h + 1;

            $title = $inner_hits[$h]['_source']['title'];
            if (empty($title))
            {
                $title = '<small>N/A</small>';
            }

            $name = $inner_hits[$h]['_source']['name'];
            if (empty($name))
            {
                $name = '<small>N/A</small>';
            }

            $place = $inner_hits[$h]['_source']['place'];
            if (empty($place))
            {
                $place = '';
            }

            $municipality = ', '.$inner_hits[$h]['_source']['municipality'];
            if ($municipality == ', ')
            {
                $municipality = '';
            }

            $country = ', '.$inner_hits[$h]['_source']['country'];
            if ($country == ', ')
            {
                $country = '';
            }

            if (empty($place) AND empty($municipality) AND empty($country))
            {
                $place = '<small>N/A</small>';
                $municipality = '';
                $country = '';
            }

            $category = $inner_hits[$h]['_source']['category'];
            if (empty($category))
            {
                $category = '<small>N/A</small>';
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

            $map = '<a href="https://www.google.com/maps/place//@'.$lat.','.$lon.',9z/data=!4m5!3m4!1s0x0:0x0!8m2!3d'.$lat.'!4d'.$lon.'" target="_blank"><i class="fa fa-map-marker"></i></a>';

            if (empty($lat) or empty($lon))
            {
                $map = '<i class="fa fa-map-marker"></i>';
            }


            $text = $inner_hits[$h]['_source']['text'];
            $word_count = str_word_count($text);
            if (empty($text))
            {
                $text = 'N/A';
                $word_count = '<small>N/A</small>';
            }

            $output .= '
                <h3>'.$i.'. '.$title.'</h3>

                    <p>
                        <a aria-expanded="false" aria-controls="collapse-text'.$i.'" data-toggle="collapse" href=".collapse-text'.$i.'" style="padding-right: 16px;">
                            <i class="fa fa-plus-circle" data-toggle="tooltip" data-placement="bottom" title="Word count. Click to expand."></i> <small>'.$word_count.'</small>
                        </a>

                        <i class="fa fa-user"></i> <small style="padding-right: 16px;">'.$name.'</small>
                        <i class="fa fa-calendar"></i> <small style="padding-right: 16px;">'.$year.'</small>
                        '.$map.' <small style="padding-right: 16px;">'.$place.$municipality.$country.'</small>
                        <i class="fa fa-heart-o"></i> <small style="padding-right: 16px;">0</small>
                    </p>

                <div class="collapse collapse-text'.$i.'">
                    <p>'.$text.'</p>

                    <p>
                        <i class="fa fa-folder-open-o"></i> '.$category.'<br>
                    </p>

                    <p>
                        <i class="fa fa-book"></i> <small style="padding-right: 16px;">'.$volume.'</small>
                        <i class="fa fa-file-text-o"></i> <small style="padding-right: 16px;">'.$page.'</small>
                        <i class="fa fa-hashtag"></i> <small style="padding-right: 16px;">'.$nr.'</small>
                    </p>
                </div>

                <hr>';
        }


        // Return output and pass it to the view.
        return view('home', ['inner_hits' => $inner_hits, 'output' => $output]);
    }
}
