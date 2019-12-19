<?php

namespace fkooman\Put;

use RuntimeException;

class Coverage
{
    /**
     * @param string $outputFile
     * @param string $currentWorkingDir
     *
     * @return void
     */
    public static function writeReport($outputFile, $currentWorkingDir, array $coverageData)
    {
        // get the source code from the files analyzed by the code coverage
        $templateData = [];
        $sourceFileList = array_keys($coverageData);
        foreach ($sourceFileList as $srcFile) {
            if (false === $filePointer = @fopen($srcFile, 'r')) {
                throw new RuntimeException(sprintf('unable to open "%s"', $srcFile));
            }
            $srcCode = [];
            while (false !== $srcLine = fgets($filePointer)) {
                $srcCode[] = htmlentities(rtrim($srcLine));
            }

            $srcName = substr($srcFile, strlen($currentWorkingDir) + 1);
            $templateData[$srcName] = [
                'srcCode' => $srcCode,
                'coverageData' => $coverageData[$srcFile],
                'coveragePercent' => self::calculatePercentage($coverageData[$srcFile]),
            ];
        }

        // sort the $templateData array by coverageDataPercent
        uasort($templateData, function ($a, $b) {
            if ($a['coveragePercent'] == $b['coveragePercent']) {
                return 0;
            }

            return ($a['coveragePercent'] < $b['coveragePercent']) ? -1 : 1;
        });

        ob_start();
        include __DIR__.'/coverage_template.php';
        $htmlPage = ob_get_contents();
        ob_end_clean();
        if (false === @file_put_contents($outputFile, $htmlPage)) {
            throw new RuntimeException(sprintf('unable to write to "%s"', $outputFile));
        }
    }

    /**
     * @return int
     */
    private static function calculatePercentage(array $coverageData)
    {
        $coveredAmount = 0;
        $uncoveredAmount = 0;
        foreach ($coverageData as $rowCovered) {
            if (1 === $rowCovered) {
                ++$coveredAmount;
            } else {
                ++$uncoveredAmount;
            }
        }

        return floor($coveredAmount / ($coveredAmount + $uncoveredAmount) * 100);
    }
}
