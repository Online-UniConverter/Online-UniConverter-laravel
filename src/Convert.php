<?php

namespace OnlineUniConverter\Laravel;

use Symfony\Component\HttpFoundation\File\UploadedFile;

abstract class Convert
{
    protected $path;
    protected $file;
    protected $filename;
    protected $format;
    protected $fileSystem;
    protected $data;
    protected $output;
    protected $operation;

    /**
     * Convert constructor.
     * @param $file
     * @param Convert $input
     * @throws \Exception
     */
    function __construct($file, $input = null)
    {
        if (!is_null($file)) $this->setFile($file, $input);
    }

    /**
     * @return mixed
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * @param $file
     * @param Convert $input
     * @return $this
     * @throws \Exception
     */
    public function setFile($file, $input = null)
    {
        if ($file instanceof UploadedFile) {
            $this->setFormat($file->getClientOriginalExtension());
            $this->setFilename(basename($file->getFilename()));
            $this->setPath(dirname($file->getPathname()));
        } else if ($this->isFile($file)) {
            $this->setFormat($this->parseExtension($file));
            $this->setPath(dirname($file));
            $this->setFilename(basename($file));
        } else if ($this->isFormat($file)) {
            $this->setFormat($file);
            $this->setPath('./');
            !is_null($input) && $this->setFilename($input->getFilename());
        } else if ($this->isURL($file)) {
            $this->setFormat($this->parseExtension($file));
            $this->setPath('./');
            $this->setFilename($this->parseFilename($file));
        } else {
            $this->setPath(dirname($file));
            $this->setFormat($this->parseExtension($file));
            $this->setFilename(basename($file));
        }

        $this->validateFormat($this->format);

        $this->file = $file;

        return $this;
    }

    /**
     * @return string
     */
    public function getFilepath()
    {
        return (isset($this->path) && !empty($this->path)) ? $this->path . '/' . $this->getFilename() : $this->getFilename();
    }

    /**
     * @return string
     * @throws \Exception
     */
    public function getExtension()
    {
        if ($filename = $this->getFilename()) return $this->parseExtension($filename);

        throw new \Exception('Unknown file path');
    }

    /**
     * @return string
     * @throws \Exception
     */
    public function getFormat()
    {
        if (is_null($this->format)) {
            $this->format = $this->getExtension();

            if (empty($this->format) && $this->getFile() && !empty($this->getFile()->getOutputFormat())) $this->format = $this->getFile()->getOutputFormat();
        }
        $this->validateFormat($this->format);

        return $this->format;
    }

    /**
     * @param $format
     * @throws \Exception
     */
    public function setFormat($format)
    {
        $this->validateFormat($format);
        $format = $this->stripQueryString($format);
        $this->format = $format;
    }

    /**
     * @param mixed $path
     */
    public function setPath($path)
    {
        $this->path = str_replace('http:', '', rtrim($path, '/'));
    }

    /**
     * @return string
     */
    public function getPath()
    {
        return empty($this->path) ? '.' : $this->path;
    }

    /**
     * @return mixed
     */
    public function getFilename()
    {
        return $this->filename;
    }

    /**
     * @param mixed $filename
     * @param null|string $ext
     */
    public function setFilename($filename, $ext = '')
    {
        $filename = $this->stripQueryString($filename);
        $this->filename = empty($ext) ? $filename : preg_replace("/{$this->parseExtension($filename)}$/", $ext, $filename);
    }

    /**
     * @return mixed
     */
    public function getOperation()
    {
        return $this->operation;
    }

    /**
     * @param mixed $operation
     */
    public function setOperation($operation)
    {
        $this->operation = $operation;
    }

    /**
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param mixed $data
     */
    public function setData($data)
    {
        $this->data = $data;
    }

    /**
     * @param Convert $input
     * @throws \Exception
     */
    public function filenameCheck(Convert $input)
    {
        if (empty($this->filename)) {
            $this->setPath($input->getPath());
            $this->setFilename($input->getFilename(), $this->getFormat());
        }
    }

    /**
     * @param $file
     * @return bool
     */
    protected function isFile($file)
    {
        return $this->parseExtension($file) != '' && strstr($file, '/') && is_file($file) === true;
    }

    /**
     * @param string $file_name
     * @return string
     */
    protected function parseExtension($file_name = '')
    {
        $ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        $ext = preg_replace("/(\?.*)/i", '', $ext);

        return $ext;
    }

    /**
     * @param string $url
     * @return string
     */
    protected function parseFilename($url = '')
    {
        return strtolower(pathinfo($url, PATHINFO_BASENAME));
    }

    /**
     * @param $format
     * @throws \Exception
     */
    protected function validateFormat($format)
    {
        if (empty($format) || !$this->isFormat($format)) throw new \Exception('Invalid format');
    }

    /**
     * @param $format
     * @return bool
     */
    protected function isFormat($format)
    {
        $format = preg_replace("/(\?.*)/i", '', $format);

        return ctype_alnum($format);
    }

    /**
     * @param Convert $output
     * @throws \Exception
     */
    public function prepareOutput(Convert $output)
    {
        $output->filenameCheck($this);
        $this->output = $output;
    }

    /**
     * @return mixed
     */
    public function toArray()
    {
        return $this->getConversionSettings();
    }

    /**
     * @param $format
     * @return mixed
     */
    protected function stripQueryString($format)
    {
        $format = preg_replace("/(\?.*)/i", '', $format);

        return $format;
    }

    private function isURL($url)
    {
        return filter_var($url, FILTER_VALIDATE_URL);
    }
}