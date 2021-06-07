<?php

namespace OnlineUniConverter\Laravel;

use Illuminate\Filesystem\Filesystem;

class ConvertLocalFile extends Convert implements ConvertInterface
{
    /**
     * ConvertLocalFile constructor.
     * @param $file
     * @param Convert $input
     * @throws \Exception
     */
    function __construct($file, $input = null)
    {
        parent::__construct($file, $input);
        $this->setOperation(OnlineUniConverter::IMPORT_UPLOAD);
        $this->setFilesystem();
    }

    public function setFilesystem($fileSystem = null)
    {
        $this->fileSystem = (!is_null($fileSystem)) ? $fileSystem : new Filesystem();
    }

    /**
     * @return mixed
     * @throws \Exception
     */
    public function save()
    {
        if ($this->validateSave()) return $this->saveFile($this->getFilepath(), $this->getData());

        throw new \Exception('File not writable or no data available: ' . $this->getFilepath());
    }

    /**
     * @return bool
     */
    protected function validateSave()
    {
        return $this->fileSystem->isWritable($this->getPath()) && $this->getData();
    }

    public function getConversionSettings()
    {
        return [
            'file' => $this->getInputFile(),
            'filename' => $this->getInputFilename(),
        ];
    }

    public function getInputFile()
    {
        return @fopen($this->getFilepath(), 'r');
    }

    public function getInputFilename()
    {
        return basename($this->getFilepath());
    }

    /**
     * @param $file_path
     * @param $data
     * @return mixed
     */
    private function saveFile($file_path, $data)
    {
        return $this->fileSystem->put($file_path, $data);
    }

    /**
     * @return array
     */
    public function getParams()
    {
        return [
            'operation' => $this->getOperation(),
            'platform' => 'sdk'
        ];
    }
}