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

    /**
     * This method allows you to read a CSV file and return the content in the form of a 2D table
     * @param string $name without extension
     * @param string $separator to separate each data of a line
     * @param bool $ignoreFirstLine true if the first line is ignored, otherwise false
     * @return array
     */
    public function read(
        string $name,
        string $separator = self::CSV_SEPARATOR,
        bool   $ignoreFirstLine = self::IGNORE_FIRST_LINE
    ): array
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

    /**
     * This method allows you to transform a 2D array into an associative 2D array by taking the header
     * of the array as its value (line defined in the parameters)
     * @param array $rows 2D array to transform into an 2D associative array
     * @param int $headerPosition header position used to add a key for each row
     * @return array
     */
    public function headerAsAssocArray(array $rows, int $headerPosition = 0): array
    {
        $header = $rows[$headerPosition];
        unset($rows[$headerPosition]);

        return array_map(function ($row) use ($header) {
            return array_combine($header, $row);
        }, $rows);
    }
}
