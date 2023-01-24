<?php

namespace App\Service\Helper;

use Symfony\Component\Finder\Finder;

class Csv
{
    protected const DIRECTORY = 'public/uploads';
    protected const IGNORE_FIRST_LINE = false;
    protected const CSV_SEPARATOR = ',';

    protected ?Finder $finder = null;

    public function __construct()
    {
        $this->finder = new Finder();
    }

    public function read(
        string $name,
        string $separator = self::CSV_SEPARATOR,
        bool   $ignoreFirstLine = self::IGNORE_FIRST_LINE
    ): ?array
    {
        $this->finder->files()
            ->in(self::DIRECTORY)
            ->name($name . '.csv');

        if (!$this->finder->hasResults()) {
            throw new \RuntimeException("File $name.csv not found");
        }

        $rows = [];

        foreach ($this->finder as $csv) {
            if (($handle = fopen($csv->getRealPath(), "r")) !== FALSE) {
                $i = 0;

                while (($data = fgetcsv($handle, null, $separator)) !== FALSE) {
                    $i++;

                    if ($ignoreFirstLine && $i == 1) {
                        continue;
                    }

                    $rows[] = $data;
                }
                fclose($handle);
            }
        }

        return $rows;
    }
}
