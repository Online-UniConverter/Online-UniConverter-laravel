<?php

namespace OnlineUniConverter\Laravel\HttpClientAdapter;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use OnlineUniConverter\Laravel\OnlineUniConverter;

class GuzzleAdapter implements HttpClientInterface
{
    protected $multipartContent = [];
    private $client;
    private $response;

    /**
     * Uses Guzzle 6.*
     */
    public function __construct()
    {
        $this->client = new Client();
    }

    /**
     * @param string $url
     * @param array $params
     * @param null $query
     * @return mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function post($url, $params = [], $query = null)
    {
        $body = is_array($query) && is_array($params) ? array_merge($params, $query) : $params;

        if (isset($body['file']) && is_resource($body['file']->file)) return $this->multipart($url, $body);

        $api_key = $body['api_key'];
        unset($body['api_key']);

        $opts = ['json' => $body];

        $header = [
            'User-Agent' => 'OnlineUniConverter-php/v' . OnlineUniConverter::VERSION . ' (https://github.com/OnlineUniConverter/OnlineUniConverter-php)',
            'Authorization' => 'Bearer ' . $api_key,
            'accept-encoding' => 'application/json'
        ];
        $opts['headers'] = $header;

        try {
            $this->response = $this->client->post($url, $opts);
        } catch (\Exception $e) {
            throw $e;
        }

        return $this->returnJsonResponse();
    }

    /**
     * @param string $url
     * @param array $params
     * @param array $query
     * @return bool|mixed
     * @throws \Exception
     */
    public function get($url, $params = [], $query = [])
    {
        $query = array_merge($params, $query);

        $api_key = $query['api_key'];
        unset($query['api_key']);

        $opts = [];
        if (!empty($params) && !empty($query)) $opts['query'] = $query;

        $header = [
            'User-Agent' => 'OnlineUniConverter-php/v' . OnlineUniConverter::VERSION . ' (https://github.com/OnlineUniConverter/OnlineUniConverter-php)',
            'Authorization' => 'Bearer ' . $api_key,
            'accept-encoding' => 'application/json'
        ];
        $opts['headers'] = $header;

        try {
            $this->response = $this->client->get($url, $opts);
        } catch (ClientException  $e) {
            throw $e;
        }

        return $this->returnJsonResponse();
    }

    /**
     * @param string $url
     * @return mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function delete($url)
    {
        $this->response = $this->client->delete($url);

        return $this->returnJsonResponse();
    }

    /**
     * @param $url
     * @param string $method
     * @param array $params
     * @param array $query
     * @return $this
     */
    public function request($url, $method = 'GET', $params = [], $query = null)
    {
        $this->response = $this->client->{$method}($url, $params);

        return $this;
    }

    /**
     * @return mixed
     */
    public function contents()
    {
        return $this->response->getBody()->getContents();
    }

    /**
     * Return JSON encoded response
     *
     * @return mixed
     */
    protected function returnJsonResponse()
    {
        return json_decode($this->response->getBody()->__toString(), true);
    }

    /**
     * @param $url
     * @param $body
     * @return mixed
     * @throws \Exception
     */
    public function multipart($url, $body)
    {
        foreach ($body as $name => $contents) $this->getMultipartContent($name, $contents);

        $opts = ['multipart' => $this->multipartContent];
        $opts['proxy'] = 'localhost:8866';

        try {
            $this->response = $this->client->post($url, $opts);
        } catch (\Exception $e) {
            throw $e;
        }

        return $this->returnJsonResponse();
    }

    /**
     * @param $name
     * @param $contents
     * @return array
     */
    public function getMultipartContent($name, $contents)
    {
        if ($name == 'file') {
            $file_stream = new \GuzzleHttp\Psr7\Stream($contents->file, array('size' => 0));
            $this->addToMultiPart(['name' => $name, 'contents' => $file_stream, 'filename' => $contents->filename]);
        } else {
            $stream = fopen('php://temp', 'r+');
            fwrite($stream, $this->castContents($contents));
            rewind($stream);
            $file_stream = new \GuzzleHttp\Psr7\Stream($stream, array('size' => 0));
            $this->addToMultiPart(['name' => $name, 'contents' => $file_stream]);
        }

        return $this->multipartContent;
    }

    /**
     * Add single contentArray to final multipartContent array
     * @param $contentArray
     */
    private function addToMultiPart(array $contentArray)
    {
        if (!in_array($contentArray, $this->multipartContent)) $this->multipartContent[] = $contentArray;
    }

    /**
     * @param $contents
     * @return string
     */
    protected function castContents($contents)
    {
        if (is_numeric($contents)) return (string)$contents;
        if (is_bool($contents)) return $contents ? 'true' : 'false';
        if (is_string($contents)) return utf8_encode($contents);

        return $contents;
    }
}
