<?php

namespace Core\Application\Service\Concerns;

use ZipArchive;

trait CanExtractFile
{
    /**
     * Returns a new ZipArchive instance.
     *
     * @return \ZipArchive
     */
    public function zip()
    {
        return new ZipArchive;
    }

    /**
     * Extract to given target destination.
     *
     * @param  string $filepath
     * @param  string $destination
     * @return string
     */
    public function extract($filepath, $destination)
    {
        $zip = $this->zip();

        if ($zip->open($filepath) === true) {
            $zip->extractTo($destination);
            $zip->close();
        } else {
            $destination = false;
        }

        return $destination;
    }
}
