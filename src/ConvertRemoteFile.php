<?php

namespace OnlineUniConverter\Laravel;

use Illuminate\Filesystem\Filesystem;

class ConvertRemoteFile extends Convert implements ConvertInterface
{
    function __construct($file)
    {
        parent::__construct($file);
        $this->setOperation(OnlineUniConverter::IMPORT_URL);
        $this->setFilesystem();
    }

    public function setFilesystem($fileSystem = null)
    {
        $this->fileSystem = (!is_null($fileSystem)) ? $fileSystem : new Filesystem();
    }

    public function save()
    {
        if ($this->validateSave()) {
            return $this->fileSystem->put($this->getFilepath(), $this->getData());
        }
        throw new \Exception('File not writable: ' . $this->getFilepath());
    }

    protected function validateSave()
    {
        return $this->fileSystem->isWritable($this->getFilepath()) && $this->getData();
    }

    public function getConversionSettings()
    {
        $data['filename'] = $this->getFilename();
        $data['file'] = $this->getFile();

        return $data;
    }

    /**
     * @return array
     */
    public function getParams()
    {
        $params = [
            'operation' => $this->getOperation(),
            'url' => $this->getFile(),
            'file_name' => $this->getFilename(),
            'platform' => 'sdk'
        ];

        return $params;
    }
}



