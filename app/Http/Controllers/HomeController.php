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

        // How long took the search?
        $time = microtime(true) - $_SERVER["REQUEST_TIME_FLOAT"];
        $time = round($time, 3);
        $time = ' in '.$time.' seconds';

        // Your search returned X result(s)...
        if ($hits == 0)
        {
            $results = 'no results';
            $time = '';
        }
        elseif ($hits == 1)
        {
            $results = $hits.' result';
        }
        else
        {
            $results = $hits.' results';
        }

        $performance = 'Your search returned '.$results.$time.'.';

        $search_results = 'Search Results:';

        // Output hits, results, etc.
        // To singular or plural of second see: http://ell.stackexchange.com/questions/7817/singular-or-plural-for-seconds
        $output = '<hr>';

        // Loop the array and get the variables.
        for ($h = 0; $h < $hits; $h++) {

            $i = $h + 1;

            // Story title
            $title = $inner_hits[$h]['_source']['title'];
            if (empty($title))
            {
                $title = '<small>N/A</small>';
            }

            // Story text
            $text = $inner_hits[$h]['_source']['text'];
            $text = nl2br($text);
            $word_count = str_word_count($text);
            if (empty($text))
            {
                $text = 'N/A';
                $word_count = '<small>N/A</small>';
            }

            // Teller
            $teller = $inner_hits[$h]['_source']['name'];
            if (empty($teller))
            {
                $teller = '<small>N/A</small>';
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
            // Map link
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

            // Bibliographic information
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


            // Information about the storyteller
            // Name
            $name = $inner_hits[$h]['_source']['name'];

            // Date of birth
            $dob = $inner_hits[$h]['_source']['dob'];
            // Place of birth
            $pob = $inner_hits[$h]['_source']['pob'];
            // Birth information
            $birth_info = '(<i class="fa fa-star-o" data-toggle="tooltip" data-placement="bottom" title="Birth"></i> <small>'.$dob.' in '.$pob.'</small>)';
            if (empty($dob))
            {
                $birth_info = '(<i class="fa fa-star-o" data-toggle="tooltip" data-placement="bottom" title="Birth"></i> <small>'.$pob.'</small>)';
            }
            elseif (empty($pob))
            {
                $birth_info = '(<i class="fa fa-star-o" data-toggle="tooltip" data-placement="bottom" title="Birth"></i> <small>'.$dob.'</small>)';
            }
            elseif (empty($dob) AND empty($pod))
            {
                $birth_info = '';
            }

            // Sex
            $sex = $inner_hits[$h]['_source']['sex'];
            if (empty($sex))
            {
                $sex = '<i class="fa fa-fw fa-genderless" data-toggle="tooltip" data-placement="bottom" title="Gender N/A"></i> <small>N/A</small>';
            }
            elseif ($sex = 'M')
            {
                $sex = '<i class="fa fa-fw fa-mars" data-toggle="tooltip" data-placement="bottom" title="Gender"></i> <small>Male</small>';
            }
            else
            {
                $sex = '<i class="fa fa-fw fa-venus" data-toggle="tooltip" data-placement="bottom" title="Gender"></i> <small>Female</small>';
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

            // Basic information about the storyteller
            $teller_info = '
                <p style="padding-top: 12px;">
                    <i class="fa fa-lg fa-user-o" data-toggle="tooltip" data-placement="bottom" title="Basic information about the storyteller"></i>
                    <strong>'.$name.'</strong> '.$birth_info.'
                </p>

                <p>
                    '.$sex.'<br>

                    <i class="fa fa-fw fa-male" data-toggle="tooltip" data-placement="bottom" title="Father\'s name"></i>
                        <small style="padding-right: 16px;">'.$father_name.'</small><br>

                    <i class="fa fa-fw fa-university" data-toggle="tooltip" data-placement="bottom" title="Education"></i>
                        <small style="padding-right: 16px;">'.$education.'</small><br>

                    <i class="fa fa-fw fa-th-large" data-toggle="tooltip" data-placement="bottom" title="Occupation"></i>
                        <small style="padding-right: 16px;">'.$occupation.'</small>
                </p>';

            if (empty($name))
            {
                $teller_info = '';
            }

            // Relevance score
            $_score = $inner_hits[$h]['_score'];
            $_score = $_score * 100;
            $_score = round($_score,0);

            $output .= '
                <p class="lead">'.$i.'. '.$title.'</p>

                <p>
                    <a aria-expanded="false" aria-controls="collapse-text'.$i.'" data-toggle="collapse" href=".collapse-text'.$i.'">
                        <i class="fa fa-lg fa-plus-circle text-success" data-toggle="tooltip" data-placement="bottom" title="Word count. Click to read the full story and see more information."></i>
                    </a>
                    <small style="padding-right: 16px;">'.$word_count.'</small>

                    <i class="fa fa-lg fa-user-circle" data-toggle="tooltip" data-placement="bottom" title="Storyteller"></i> <small style="padding-right: 16px;">'.$teller.'</small>

                    <i class="fa fa-lg fa-calendar" data-toggle="tooltip" data-placement="bottom" title="Date when the story was collected."></i> <small style="padding-right: 16px;">'.$year.'</small>

                    '.$map.'
                    <small style="padding-right: 16px;">'.$place.$municipality.$country.'</small>

                    <!-- a href=".heart" data-target=".heart" data-toggle="modal">
                        <i class="fa fa-lg fa-heart-o text-danger" data-toggle="tooltip" data-placement="bottom" title="Do you like this?"></i>
                    </a>
                    <small style="padding-right: 16px;">0</small -->

                    <span class="pull-right">
                        <i class="fa fa-lg fa-list text-success" data-toggle="tooltip" data-placement="bottom" title="Result relevance"></i>
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

                    '.$teller_info.'

                </div>

                <hr>';
        }

        // Aggregations
        $aggregations = $search['aggregations'];

        // Categories
        $categories = $aggregations['categories']['buckets'];
        $categories_hits = count($categories);

        $categories_count = '
            <div style="padding: 4px 0;">
                <a aria-expanded="false" aria-controls="collapse-categorys" data-toggle="collapse" href=".collapse-categorys">
                    <h4>
                        <i class="fa fa-lg fa-fw fa-folder-open-o" data-toggle="tooltip" data-placement="bottom" title=""></i> Categories ('.$categories_hits.')
                    </h4>
                </a>

                <div class="collapse collapse-categorys">
                    <table class="table table-condensed table-hover">
                        <tbody>';

        for ($h = 0; $h < $categories_hits; $h++) {
            $category = $categories[$h]['key'];
            $count = $categories[$h]['doc_count'];
            $categories_count .= '
                <tr>
                    <td>'.$category.' ('.$count.')</td>
                    <td><a href="#"><i class="fa fa-filter" data-toggle="tooltip" data-placement="bottom" title="Filter current search."></i></a></td>
                    <td><a href="#"><i class="fa fa-database" data-toggle="tooltip" data-placement="bottom" title="Search everything within this category in the entire database."></i></a></td>
                </tr>';
        }

        $categories_count .= '
                        </tbody>
                    </table>
                </div>
            </div>';

        // Storytellers
        $tellers = $aggregations['tellers']['buckets'];
        $tellers_hits = count($tellers);

        $tellers_count = '
            <div style="padding: 4px 0;">
                <a aria-expanded="false" aria-controls="collapse-tellers" data-toggle="collapse" href=".collapse-tellers">
                    <h4>
                        <i class="fa fa-lg fa-fw fa-user-circle" data-toggle="tooltip" data-placement="bottom" title=""></i> Storytellers ('.$tellers_hits.')
                    </h4>
                </a>

                <div class="collapse collapse-tellers">
                    <table class="table table-condensed table-hover">
                        <tbody>';

        for ($h = 0; $h < $tellers_hits; $h++) {
            $teller = $tellers[$h]['key'];
            $count = $tellers[$h]['doc_count'];
            $tellers_count .= '
                <tr>
                    <td>'.$teller.' ('.$count.')</td>
                    <td><a href="#"><i class="fa fa-filter" data-toggle="tooltip" data-placement="bottom" title="Filter current search."></i></a></td>
                    <td><a href="#"><i class="fa fa-database" data-toggle="tooltip" data-placement="bottom" title="Search everything of this storyteller in the entire database."></i></a></td>
                </tr>';
        }

        $tellers_count .= '
                        </tbody>
                    </table>
                </div>
            </div>';


        // Places
        $places = $aggregations['places']['buckets'];
        $places_hits = count($places);

        $places_count = '
            <div style="padding: 4px 0;">
                <a aria-expanded="false" aria-controls="collapse-places" data-toggle="collapse" href=".collapse-places">
                    <h4>
                        <i class="fa fa-lg fa-fw fa-globe" data-toggle="tooltip" data-placement="bottom" title=""></i> Places ('.$places_hits.')
                    </h4>
                </a>

                <div class="collapse collapse-places">
                    <table class="table table-condensed table-hover">
                        <tbody>';

        for ($h = 0; $h < $places_hits; $h++) {
            $place = $places[$h]['key'];
            $count = $places[$h]['doc_count'];
            $places_count .= '
                <tr>
                    <td>'.$place.' ('.$count.')</td>
                    <td><a href="#"><i class="fa fa-filter" data-toggle="tooltip" data-placement="bottom" title="Filter current search."></i></a></td>
                    <td><a href="#"><i class="fa fa-database" data-toggle="tooltip" data-placement="bottom" title="Search everything from this place in the entire database."></i></a></td>
                </tr>';

        }

        $places_count .= '
                        </tbody>
                    </table>
                </div>
            </div>';

        // Volumes
        $volumes = $aggregations['volumes']['buckets'];
        $volumes_hits = count($volumes);

        $volumes_count = '
            <div style="padding: 4px 0;">
                <a aria-expanded="false" aria-controls="collapse-volumes" data-toggle="collapse" href=".collapse-volumes">
                    <h4>
                        <i class="fa fa-lg fa-fw fa-book" data-toggle="tooltip" data-placement="bottom" title=""></i> Volumes ('.$volumes_hits.')
                    </h4>
                </a>

                <div class="collapse collapse-volumes">
                    <table class="table table-condensed table-hover">
                        <tbody>';

        for ($h = 0; $h < $volumes_hits; $h++) {
            $volume = $volumes[$h]['key'];
            $count = $volumes[$h]['doc_count'];
            $volumes_count .= '
                <tr>
                    <td>'.$volume.' ('.$count.')</td>
                    <td><a href="#"><i class="fa fa-filter" data-toggle="tooltip" data-placement="bottom" title="Filter current search."></i></a></td>
                    <td><a href="#"><i class="fa fa-database" data-toggle="tooltip" data-placement="bottom" title="Search everything within this volume in the entire database."></i></a></td>
                </tr>';
        }

        $volumes_count .= '
                        </tbody>
                    </table>
                </div>
            </div>';

        if(isset($categories_hits) OR isset($tellers_hits) OR isset($places_hits) OR isset($volumes_hits))
        {
            $filters = 'Filters:';
        }

        // Return output and pass it to the view.
        return view('home', [
            'search' => $search,
            'performance' => $performance,
            'search_results' => $search_results,
            'output' => $output,
            'filters' => $filters,
            'categories' => $categories,
            'categories_hits' => $categories_hits,
            'categories_count' => $categories_count,
            'volumes' => $volumes,
            'volumes_hits' => $volumes_hits,
            'volumes_count' => $volumes_count,
            'tellers' => $tellers,
            'tellers_hits' => $tellers_hits,
            'tellers_count' => $tellers_count,
            'places' => $places,
            'places_hits' => $places_hits,
            'places_count' => $places_count
        ]);
    }
}
