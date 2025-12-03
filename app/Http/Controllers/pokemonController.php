<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Client\Pool;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use stdClass;

use function PHPUnit\Framework\isEmpty;

class pokemonController extends Controller
{
    public function index(Request $request) {
        try {

            $page = $request->get('page', 0);
            $limit = $request->get('limit', 20);

            // Return cached response if it exists
            if (Cache::has('p' . $page . 'l' . $limit)) {
                return Cache::get('p' . $page . 'l' . $limit);
            }
            
            if ($page === 0) {
                $offset = 0;
            } else {
                $offset = $limit * $page;
            }

            $response = Http::get('https://pokeapi.co/api/v2/pokemon/',[
                'limit' => $limit,
                'offset'  => $offset,
            ]);


            
            if ($response->successful()) {
                $data = collect($response['results']);
                
                // Make request to get pokemon details for each of the returned pokemons
                $detailResponse = Http::pool(fn (Pool $pool) => $data->map(fn($pokemon) => $pool->get($pokemon['url'])));

                $list = collect($detailResponse)->map(function ($res) {

                    // Get the required information from the details
                    if ($res->ok()) {
                        $info = $res;

                        $filteredInfo = new stdClass();

                        $filteredInfo->name = ucfirst($info['name']);
                        $filteredInfo->image = $info['sprites']['front_default'];
                        $filteredInfo->types = $info['types'];
                        $filteredInfo->height = $info['height'];
                        $filteredInfo->weight = $info['weight'];

                        $temp_array = array();

                        foreach ($filteredInfo->types as $type) {
                            array_push($temp_array, $type['type']);
                        }
                        
                        $filteredInfo->types = $temp_array;

                        return $filteredInfo;
                    }
                    return null;
                });

                $filteredData = new stdClass();

                $filteredData->data = $list;
                $filteredData->hasMorePages = isset($response['next']) ? true : false;

                // Cache the response
                Cache::add('p' . $page . 'l' . $limit, $filteredData, now()->addDay());

                return $filteredData;

            }
            
            return response()->json(['error' => 'Failed to fetch Pokemon data'], 500);
        } catch (\Exception $e) {
            return response()->json(['error' => 'API request failed'], 500);
        }
    }

    public function search(string $name = '') {
        try {
            $response = Http::get('https://pokeapi.co/api/v2/pokemon/' . $name);

            if ($response->successful()) {

                $pokemonData = new stdClass();
                $pokemonDetail = new stdClass();

                $pokemonDetail->name = ucfirst($response['name']);
                $pokemonDetail->image = $response['sprites']['front_default'];
                $pokemonDetail->types = $response['types'];
                $pokemonDetail->height = $response['height'];
                $pokemonDetail->weight = $response['weight'];

                $temp_array = array();

                foreach ($pokemonDetail->types as $type) {
                    array_push($temp_array,$type['type']);
                }

                $pokemonDetail->types = $temp_array;

                $pokemonData->data = $pokemonDetail;
                $pokemonData->error = null;

                return response()->json($pokemonData);
            } else {

                $pokemonData = new stdClass();
                $pokemonData->data = null;
                $pokemonData->error = 'noPokemonFound';

                return response()->json($pokemonData);
            }

        } catch (\Exception $e) {
            return response()->json(['error' => 'API request failed: ' . $e]);
        }
    }
}
