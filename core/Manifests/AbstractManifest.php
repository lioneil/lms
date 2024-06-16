<?php

namespace Core\Manifests;

abstract class AbstractManifest
{
    /**
     * Get the current widget manifest.
     *
     * @return array
     */
    protected function getManifest()
    {
        if (! is_null($this->manifest)) {
            return $this->manifest;
        }

        if (! file_exists($this->manifestPath)) {
            $this->build();
        }

        $this->files->get($this->manifestPath);

        return $this->manifest = file_exists($this->manifestPath) ?
            $this->files->getRequire($this->manifestPath) : [];
    }

    /**
     * Retrieve all enabled widgets.
     *
     * @return object
     */
    public function all()
    {
        return $this->enabled();
    }
}
