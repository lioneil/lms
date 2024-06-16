<?php

namespace Core\Application\Service\Contracts;

interface Exportable
{
    /**
     * Export a resource or resources to a human-readable
     * format. E.g. PDF, Spreadsheet, CSV, etc.
     *
     * @param  array  $attributes
     * @param  string $format
     * @param  string $filename
     * @return mixed
     */
    public function export(array $attributes, string $format, string $filename = null);
}
