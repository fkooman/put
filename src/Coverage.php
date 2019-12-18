<?php

namespace fkooman\Put;

use RuntimeException;

class Coverage
{
    /**
     * @param string $outputFile
     *
     * @return void
     */
    public static function writeReport($outputFile, array $coverageData)
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
                $srcCode[] = str_replace([' ', "\t"], ['&nbsp;', '&nbsp;&nbsp;&nbsp;&nbsp;'], htmlentities($srcLine));
            }

            $templateData[$srcFile] = [
                'srcCode' => $srcCode,
                'coverageData' => $coverageData[$srcFile],
            ];
        }

        ob_start();
        include __DIR__.'/coverage_template.php';
        $htmlPage = ob_get_contents();
        ob_end_clean();
        if (false === @file_put_contents($outputFile, $htmlPage)) {
            throw new RuntimeException(sprintf('unable to write to "%s"', $outputFile));
        }
    }
}
