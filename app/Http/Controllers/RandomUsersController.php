<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Client\Pool;
use Illuminate\Support\Facades\Http;
use SoapBox\Formatter\Formatter;
use Response;


class RandomUsersController extends Controller
{

    /**
     * Output the XML file
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('welcome');

    }

    /**
     * Output the XML file
     *
     * @return \Illuminate\Http\Response
     */
    public function xml()
    {
        // Take the provided url
        $url = 'https://randomuser.me/api/';

        //Create the concurrent requests
        $responses = Http::pool(fn (Pool $pool) => [
            $pool->as('request_1')->get($url),
            $pool->as('request_2')->get($url),
            $pool->as('request_3')->get($url),
            $pool->as('request_4')->get($url),
            $pool->as('request_5')->get($url),
            $pool->as('request_6')->get($url),
            $pool->as('request_7')->get($url),
            $pool->as('request_8')->get($url),
            $pool->as('request_9')->get($url),
            $pool->as('request_10')->get($url),
        ]);

        // iterate through the requests and collect users
        $users = [];

        for ($x = 1; $x <= 10; $x++) {
            $r = $responses['request_'.$x];
            if ($r->successful()){
                $users[] = $this->buildUser($r->body());
            }

        // Some basic validation
            if ($r->clientError()){
                echo $r->throw()->json();
            }
        }

        // To sort by the last name, I converted this to a collection so I could use the Laravel sorting.
        $c = collect($users)->sortByDesc('last_name');

        // Builds the XML data
        $formatter = Formatter::make($c, Formatter::JSON);
        $xml = $formatter->toXml();

        // Renders the XML file
        return response($xml, 200)
                ->header('Content-Type', 'text/xml')
                ->header('Cache-Control', 'public')
                ->header('Content-Description', 'File Transfer')
                ->header('Content-Disposition', 'attachment; filename=random_users.xml')
                ->header('Content-Transfer-Encoding', 'binary');

    }

    /**
     * Build a user
     *
     * @return array
     */
    public function buildUser($body)
    {
        $userArray = json_decode($body, true);
        $last_name = $userArray['results'][0]['name']['last'];
        $first_name = $userArray['results'][0]['name']['first'];
        $title = $userArray['results'][0]['name']['title'];
        $phone = $userArray['results'][0]['phone'];
        $email = $userArray['results'][0]['email'];
        $country = $userArray['results'][0]['location']['country'];

        return [
            'first_name' => $first_name,
            'last_name' => $last_name,
            'title' => $title,
            'phone' => $phone,
            'email' => $email,
            'country' => $country,
        ];
    }
}
