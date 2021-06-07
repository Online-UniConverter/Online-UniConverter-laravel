<?php

namespace OnlineUniConverter\Laravel;

interface ConvertInterface
{
    public function getPath();

    public function getFilename();

    public function getFormat();

    public function save();

    public function toArray();

    public function getConversionSettings();
}