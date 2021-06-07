<?php

namespace OnlineUniConverter\Laravel;

/*
|--------------------------------------------------------------------------
| OnlineUniConverter Laravel API
|--------------------------------------------------------------------------
|
| OnlineUniConverter is a file conversion service. Convert anything to anything
| more than 100 different audio, video, document, ebook, archive, image,
| spreadsheet and presentation formats supported.
|
*/

use Exception;
use Illuminate\Filesystem\Filesystem;

class OnlineUniConverter
{
    use HttpClient;

    const IMPORT_URL = 'import/url';
    const IMPORT_UPLOAD = 'import/upload';

    const MODE_CONVERT = 'convert';
    const MODE_COMPRESS = 'compress';

    const TIMEOUT = 120;
    const VERSION = '2.0.0';

    protected $fileSystem;

    /**
     * @var string
     */
    private $api_key;

    /**
     * @var Process
     */
    private $process;

    /**
     * @var Convert
     */
    private $input;

    /**
     * @var Convert
     */
    private $output;

    private $resource;
    private $input_format;
    private $output_format;
    private $converterOptions;
    private $operation;

    /**
     * Configuration options
     * @var Config
     */
    private $config;

    /**
     * @param $config
     * @internal param null $api_key
     */
    function __construct($config = null)
    {
        $this->setConfig($config);
        $this->setFilesystem();
    }

    /**
     * @param Config $config
     */
    public function setConfig($config = null)
    {
        if (is_array($config)) $this->config = new Config($config);
        if (is_object($config)) $this->config = $config;

        $this->api_key = is_string($config) ? $config : (is_object($this->config) ? $this->config->get('api_key') : null);
    }

    /**
     * @param Filesystem $fileSystem
     */
    public function setFilesystem($fileSystem = null)
    {
        $this->fileSystem = (!is_null($fileSystem)) ? $fileSystem : new Filesystem();
    }

    /**
     * @return $this
     * @throws Exception
     */
    public function convert()
    {
        $this->operation = self::MODE_CONVERT;

        return $this->save();
    }

    /**
     * @return $this
     * @throws Exception
     */
    public function compress()
    {
        $this->operation = self::MODE_COMPRESS;

        return $this->save();
    }

    /**
     * @param $resource
     * @return $this
     * @throws Exception
     */
    public function from($resource)
    {
        if (empty($this->resource) && !empty($resource)) $this->resource = $resource;

        switch (true) {
            case $this->isUrl():
                $this->input = new ConvertRemoteFile($this->resource);
                break;
            case $this->isFilePath():
            case $this->isSymfonyUpload():
                $this->input = new ConvertLocalFile($this->resource);
                break;
            default:
                throw new Exception("File input is not readable");
        }

        return $this;
    }

    /**
     * @param $type
     * @return $this
     * @throws Exception
     */
    public function to($type)
    {
        if (!$this->input) throw new Exception('Please set the file before converting');

        $this->output = new ConvertLocalFile($type, $this->input);

        return $this;
    }

    /**
     * @return $this|OnlineUniConverter
     * @throws Exception
     * @internal param null $path
     * @internal param null $output
     */
    private function save()
    {
        $this->startProcess();

        if ($this->getProcess()->isFinished()) return $this->downloadConvertedFile();

        return $this->convertFileAndSaveTo();
    }

    /**
     * @param $type
     * @return $this
     */
    public function input($type)
    {
        $this->input_format = $this->filterType($type);
        return $this;
    }

    /**
     * @param $type
     * @return $this
     */
    public function output($type)
    {
        $this->output_format = $this->filterType($type);
        return $this;
    }

    /**
     * @param Process $process
     * @return mixed
     */
    public function setProcess($process)
    {
        return $this->process = $process;
    }

    /**
     * @return Process
     */
    public function getProcess()
    {
        return $this->process;
    }

    /**
     * @return $this
     * @throws Exception
     */
    public function startProcess()
    {
        if (!$this->hasApiKey()) throw new Exception('No API key provided.');
        if (!$this->getInputFormat() || !$this->getOutputFormat()) throw new Exception('Invalid formats provided');

        $this->setProcess(new Process($this->api_key));
        $this->getProcess()->process($this->getParams());

        return $this;
    }

    /**
     * @return bool
     */
    public function isUrl()
    {
        return (bool)filter_var($this->resource, FILTER_VALIDATE_URL);
    }

    /**
     * @return bool
     */
    public function isFilePath()
    {
        if (is_string($this->resource)) return !empty($this->fileSystem) && $this->fileSystem->isFile($this->resource);

        return false;
    }

    /**
     * @return bool
     */
    public function isSymfonyUpload()
    {
        return is_a($this->resource, 'Symfony\Component\HttpFoundation\File\UploadedFile');
    }

    /**
     * @return $this
     * @throws Exception
     */
    private function downloadConvertedFile()
    {
        $data = $this->getProcess()->download();
        if ($this->output) {
            $this->output->setData($data);
            $this->output->save();
        }
        return $this;
    }

    /**
     * @return $this
     * @throws Exception
     */
    public function download()
    {
        return $this->downloadConvertedFile();
    }


    /**
     * @param $type
     * @return mixed
     */
    private function filterType($type)
    {
        $a = explode('.', $type);

        return end($a);
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return ($this->process->getStatus()) ? 'Process is: ' . $this->process->getStatus() : 'Process has not started yet';
    }

    /**
     * @return $this|OnlineUniConverter
     * @throws Exception
     * @internal param $path
     */
    public function convertFileAndSaveTo()
    {
        if ($this->getProcess()->waitForConversion(self::TIMEOUT, $this->input)) return $this->downloadConvertedFile();

        throw new Exception('Problem saving file');
    }

    /**
     * @param $api_key
     * @return $this
     */
    public function setApiKey($api_key)
    {
        $this->api_key = $api_key;

        return $this;
    }

    /**
     * @return bool
     */
    public function hasApiKey()
    {
        return !empty($this->api_key);
    }

    /**
     * @return null
     */
    public function getApiKey()
    {
        return $this->api_key;
    }

    /**
     * @return mixed
     */
    public function getInput()
    {
        if (!empty($this->inputs)) return $this->inputs;
        return $this->input;
    }

    /**
     * @return string
     * @throws Exception
     */
    public function getInputFormat()
    {
        return isset($this->input) ? $this->input->getFormat() : $this->input_format;
    }

    /**
     * @return string
     * @throws Exception
     */
    public function getOutputFormat()
    {
        return isset($this->output) ? $this->output->getFormat() : $this->output_format;
    }

    /**
     * @param $name
     * @param $value
     */
    private function setConverterOption($name, $value)
    {
        $this->converterOptions[$name] = $value;
    }

    /**
     * Set the ratio
     * Value can be 0-1
     * @param $float
     * @return $this
     */
    public function ratio($float)
    {
        $this->setConverterOption('ratio', $float);
        return $this;
    }

    /**
     * Set the resolution
     * Values can be:
     *        - 144p
     *        - 240p
     *        - 360p
     *        - 480p
     *        - 720p
     *        - 1080p
     * @param $resolution
     * @return $this
     */
    public function resolution($resolution)
    {
        $this->setConverterOption('resolution', $resolution);
        return $this;
    }

    /**
     * Set the bitrate
     * Values can be:
     *        - 320
     *        - 256
     *        - 128
     *        - 64
     *        - 32
     * @param $bitrate
     * @return $this
     */
    public function bitrate($bitrate)
    {
        $this->setConverterOption('bitrate', $bitrate);
        return $this;
    }

    public function reset()
    {
        if ($this->getProcess() && $this->getProcess()->isFinished()) {
            $this->resource = null;
            $this->operation = null;
            $this->input = null;
            $this->output = null;
            $this->process = null;
            $this->input_format = null;
            $this->output_format = null;
        }
    }

    /**
     * @return array
     * @throws Exception
     */
    public function getParams()
    {
        // 压缩选项
        $converterOptions = $this->converterOptions ?? [];

        $tasks = [
            $this->input->getParams(),
            array_merge([
                'operation' => $this->operation,
                'input_format' => $this->input->getFormat(),
                'output_format' => $this->output->getFormat(),
            ], $converterOptions)
        ];

        return ['tasks' => $tasks, 'api_key' => $this->api_key];
    }
}
