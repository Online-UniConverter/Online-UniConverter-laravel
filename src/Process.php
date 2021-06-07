<?php

namespace OnlineUniConverter\Laravel;

use Exception;

class Process
{
    use HttpClient;

    const STEP_SUCCESS = 'success';
    const STEP_FAILED = 'failed';
    const STEP_WAITING = 'waiting';
    const TIMEOUT = 120;

    public $id;
    public $status;
    public $data = [];
    public $import_task = [];
    public $feature_task = [];
    public $export_task = [];
    public $code;
    public $msg;
    public $export_url;
    public $oss_form_url;
    public $oss_form_params;

    private $api_url = 'http://48046449-1266070635367705.test.functioncompute.com';
    // private $api_url = 'https://api.cloudconvert.com';
    private $api_key;

    /**
     * @param string $api_key
     */
    function __construct($api_key = '')
    {
        $this->setClient();
        $this->api_key = $api_key;
    }

    /**
     * @param array|object $data
     */
    private function fill($data = [])
    {
        foreach ($data as $key => $value) {
            $this->{$key} = $value;
        }
        $this->id = $this->data['id'] ?? 0;
        $this->status = $this->data['status'] ?? '';
        $this->import_task = $this->data['tasks'][0] ?? [];
        $this->feature_task = $this->data['tasks'][1] ?? [];
        $this->export_task = $this->data['tasks'][2] ?? [];
        $this->export_url = $this->export_task['result']['files'][0]['url'] ?? "";
        $this->oss_form_url = $this->import_task['result']['form']['url'] ?? "";
        $this->oss_form_params = $this->import_task['result']['form']['parameters'] ?? [];
    }

    /**
     * @return $this
     * @internal param string $action
     */
    public function status()
    {
        $response = $this->http->get($this->api_url . '/v2/jobs/' . $this->id, ['api_key' => $this->api_key]);
        $this->fill($response);

        return $this;
    }

    /**
     * @param array $params
     * @return mixed
     * @throws \Exception
     */
    public function process($params = [])
    {
        $response = $this->http->post($this->api_url . '/v2/jobs', $params);
        $this->fill($response);
        $this->checkErrors();

        return $this;
    }

    /**
     * @param Convert $input
     * @return mixed
     * @throws \Exception
     */
    public function upload($input)
    {
        $params = $this->oss_form_params;

        $obj = $this->createMultipleFileObject($input);
        $params['file'] = $obj->toJson();

        $params['x:timestamp'] = time();
        $params['name'] = $input->getFilename();

        unset($params['expire']);

        $response = $this->http->post($this->oss_form_url, $params);
        $code = $response['code'] ?? 1;
        if ($code) throw new Exception('Problem uploading file ' . $response['msg'] ?? '');

        return $this;
    }

    public function getData()
    {
        return $this->data;
    }

    /**
     * @return array
     * @throws \Exception
     */
    public function download()
    {
        if (!$this->export_url) throw new \Exception('Not ready to download');

        return $this->http->request($this->export_url, 'get')->contents();
    }

    /**
     * Blocks until the conversion is finished
     * @param int $timeout
     * @param Convert $input
     * @return bool
     * @throws \Exception
     */
    public function waitForConversion($timeout, $input = null)
    {
        if ($this->oss_form_url) $this->upload($input);
        $time = 0;
        while ($time++ <= $timeout) {
            sleep(1);
            $this->status();
            $this->checkErrors();
            if ($this->isFinished()) return true;
        }
        throw new \Exception('Timeout');
    }

    /**
     * @return mixed
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @return bool
     */
    public function isFinished()
    {
        return $this->status === self::STEP_SUCCESS;
    }

    /**
     * @throws \Exception
     */
    public function checkErrors()
    {
        if ($this->code || $this->status === self::STEP_FAILED) throw new \Exception($this->msg);
    }

    /**
     * @param Convert $input
     * @return ConvertMultiple
     */
    protected function createMultipleFileObject($input)
    {
        $setting = $input->toArray();
        $obj = new ConvertMultiple;
        $obj->file = $setting['file'];
        $obj->filename = $setting['filename'];

        return $obj;
    }
}