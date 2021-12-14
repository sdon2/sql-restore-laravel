<?php

namespace SqlRestoreLaravel\Classes;

use Exception;
use ZipArchive;

class ZipReader
{
    protected $archive;

    public function __construct($archivename)
    {
        $archive = new ZipArchive();
        if (!$archive->open($archivename)) {
            throw new Exception('Unable to open zip file');
        }
        $this->archive = $archive;
    }

    public function readContents($filename)
    {
        if ($this->archive->status === ZipArchive::ER_OK) {
            if (!$fp = $this->archive->getStream($filename)) {
                throw new Exception('File not found in Zip Archive');
            }
            return stream_get_contents($fp);
        }

        throw new Exception('Unable to open zip file');
    }

    public function __destruct()
    {
        try {
            $this->archive->close();
        }
        catch (\Throwable $exception) {
            // Do nothing
        }
    }
}
