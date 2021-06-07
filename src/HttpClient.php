<?php

namespace OnlineUniConverter\Laravel;

use GuzzleHttp\Client;
use OnlineUniConverter\Laravel\HttpClientAdapter\GuzzleAdapter;

trait HttpClient
{
    public $http;

    /**
     * @param HttpClientAdapter\HttpClientInterface $adapter
     */
    public function setClient($adapter = null)
    {
        if (!is_null($adapter)) {
            $this->http = $adapter;
        } else {
            $this->setGuzzleAdapter();
        }
    }

    public function setGuzzleAdapter()
    {
        $this->http = new GuzzleAdapter;
    }
}