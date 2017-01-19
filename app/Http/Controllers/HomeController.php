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
                <h3><strong>Your search returned '.$results.'</strong></h3>

                <hr>';

        // Loop the array and get the variables.
        for ($h = 0; $h < $hits; $h++) {

            $i = $h + 1;

            $title = $inner_hits[$h]['_source']['title'];
            if (empty($title))
            {
                $title = '<small>N/A</small>';
            }


            $text = $inner_hits[$h]['_source']['text'];
            $text = nl2br($text);
            $word_count = str_word_count($text);
            if (empty($text))
            {
                $text = 'N/A';
                $word_count = '<small>N/A</small>';
            }

            // Storyteller
            $name = $inner_hits[$h]['_source']['name'];
            $a_name = '
                <a aria-expanded="false" aria-controls="collapse-name'.$i.'" data-toggle="collapse" href=".collapse-name'.$i.'">
                    <i class="fa fa-lg fa-user" data-toggle="tooltip" data-placement="bottom" title="Storyteller. Click to more information."></i>
                </a>';
            if (empty($name))
            {
                $a_name = '<i class="fa fa-lg fa-user" title="Storyteller not available."></i>';
                $name = '<small>N/A</small>';
            }

            // Year
            $year = $inner_hits[$h]['_source']['year'];
            if (empty($year))
            {
                $year = '<small>N/A</small>';
            }

            // Coordinates, latittude and longitude
            $lat = $inner_hits[$h]['_source']['lat'];
            $lon = $inner_hits[$h]['_source']['lon'];

            // Map
            $map = '<a href="https://www.google.com/maps/place//@'.$lat.','.$lon.',9z/data=!4m5!3m4!1s0x0:0x0!8m2!3d'.$lat.'!4d'.$lon.'" target="_blank"><i class="fa fa-lg fa-map-o" data-toggle="tooltip" data-placement="bottom" title="Location where the story was collected. Click to see the location on a map."></i></a>';

            if (empty($lat) OR empty($lon))
            {
                $map = '<i class="fa fa-lg fa-map-o" data-toggle="tooltip" data-placement="bottom" title="Location where the story was collected."></i>';
            }

            // Place
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

            // Category
            $category = $inner_hits[$h]['_source']['category'];
            if (empty($category))
            {
                $category = '<small>N/A</small>';
            }

            // Volume
            $volume = $inner_hits[$h]['_source']['volume'];
            if (empty($volume))
            {
                $volume = '<small>N/A</small>';
            }

            // Page
            $page = $inner_hits[$h]['_source']['page'];
            if (empty($page))
            {
                $page = '<small>N/A</small>';
            }

            // Number
            $nr = $inner_hits[$h]['_source']['nr'];
            if (empty($nr))
            {
                $nr = '<small>N/A</small>';
            }


            // Information about the storyteller.
            // Date of birth.
            $dob = $inner_hits[$h]['_source']['dob'];
            if (empty($dob))
            {
                $dob = '<small>N/A</small>';
            }
            // Education
            $education = $inner_hits[$h]['_source']['education'];
            if (empty($education))
            {
                $education = '<small>N/A</small>';
            }
            // Father's name
            $father_name = $inner_hits[$h]['_source']['father_name'];
            if (empty($father_name))
            {
                $father_name = '<small>N/A</small>';
            }
            // Occupation
            $occupation = $inner_hits[$h]['_source']['occupation'];
            if (empty($occupation))
            {
                $occupation = '<small>N/A</small>';
            }
            // Place of birth
            $pob = $inner_hits[$h]['_source']['pob'];
            if (empty($pob))
            {
                $pob = '<small>N/A</small>';
            }

            // Relevance score
            $_score = $inner_hits[$h]['_score'];
            $_score = $_score * 100;
            $_score = round($_score,0);

            $output .= '
                <h3>'.$i.'. '.$title.'</h3>

                    <p>
                        <a aria-expanded="false" aria-controls="collapse-text'.$i.'" data-toggle="collapse" href=".collapse-text'.$i.'">
                            <i class="fa fa-lg fa-caret-square-o-down text-success" data-toggle="tooltip" data-placement="bottom" title="Word count. Click to read the full story."></i>
                        </a>
                        <small style="padding-right: 16px;">'.$word_count.'</small>

                        '.$a_name.'
                        <small style="padding-right: 16px;">'.$name.'</small>

                        <i class="fa fa-lg fa-calendar" data-toggle="tooltip" data-placement="bottom" title="Date when the story was collected."></i> <small style="padding-right: 16px;">'.$year.'</small>

                        '.$map.'
                        <small style="padding-right: 16px;">'.$place.$municipality.$country.'</small>

                        <a href="">
                            <i class="fa fa-lg fa-heart-o text-danger" data-toggle="tooltip" data-placement="bottom" title="Do you like this?"></i>
                        </a>
                        <small style="padding-right: 16px;">0</small>

                        <span class="pull-right">
                            <i class="fa fa-compass text-warning" data-toggle="tooltip" data-placement="bottom" title="Result relevance"></i>
                            <small style="padding-right: 16px;">'.$_score.'</small>
                        </span>
                    </p>

                <div class="collapse collapse-text'.$i.'">
                    <p style="padding-top: 16px;">'.$text.'</p>

                    <p>
                        <i class="fa fa-lg fa-folder-open-o" data-toggle="tooltip" data-placement="bottom" title="Category"></i> '.$category.'<br>
                    </p>

                    <p>
                        <i class="fa fa-lg fa-book" data-toggle="tooltip" data-placement="bottom" title="Volume"></i> <small style="padding-right: 16px;">'.$volume.'</small>
                        <i class="fa fa-lg fa-file-text-o" data-toggle="tooltip" data-placement="bottom" title="Page"></i> <small style="padding-right: 16px;">'.$page.'</small>
                        <i class="fa fa-lg fa-hashtag" data-toggle="tooltip" data-placement="bottom" title="Story number"></i> <small style="padding-right: 16px;">'.$nr.'</small>
                    </p>
                </div>

                <div class="collapse collapse-name'.$i.'">
                    <p style="padding-top: 16px;">
                        <i class="fa fa-lg fa-user-o data-toggle="tooltip" data-placement="bottom" title="Storyteller name""></i> <strong>'.$name.'</strong> (<small><i class="fa fa-star-o" data-toggle="tooltip" data-placement="bottom" title="Date of birth"></i> '.$dob.'</small>)
                    </p>
                    <p>
                        <i class="fa fa-fw fa-male" data-toggle="tooltip" data-placement="bottom" title="Father\'s name"></i> '.$father_name.'<br>
                        <i class="fa fa-fw fa-th-large" data-toggle="tooltip" data-placement="bottom" title="Occupation"></i> '.$occupation.'<br>
                        <i class="fa fa-fw fa-university" data-toggle="tooltip" data-placement="bottom" title="Education"></i> '.$education.'<br>
                    </p>
                </div>

                <hr>';
        }


        // Return output and pass it to the view.
        return view('home', ['inner_hits' => $inner_hits, 'output' => $output]);
    }
}
