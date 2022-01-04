<?php

namespace App\Http\Controllers\Location;

class CountriesController extends BaseController
{
    /**
     * Countries JSON filename.
     */
    private $filename = 'location/countries.json';

    /**
     * Decoded JSON countries file.
     * @var array
     */
    private $countries;

    /**
     * CountriesController constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->countries = $this->getCountries($this->filename);
    }

    /**
     * Get location from JSON file.
     * @param $filename
     * @return array
     */
    private function getCountries($filename)
    {
        $path = storage_path() . '/' . $filename;
        $array = json_decode(file_get_contents($path), true);

        return $array;
    }

    /**
     * Get all countries info list
     *
     * [
     *      {"name":"Montserrat","alpha2Code":"MS","timezones":["UTC-04:00"]},
     *      {...},
     *      ...
     * ]
     */
    public function index()
    {
        return response()->json($this->countries, 200);
    }

    /**
     * Get country by country code (alpha2Code).
     *
     * MS -> Montserrat
     *
     * @param $countryCode
     * @return object
     */
    public function getCountryByCountryCode($countryCode)
    {
        $requestedCountry = null;

        foreach ($this->countries as $country) {
            if ($country['alpha2Code'] === $countryCode) {
                 $requestedCountry = $country;
                 break;
            }
        }

        if (is_null($requestedCountry)) {
            abort(404);
        } else {
            return response()->json($requestedCountry, 200);
        }
    }
}
