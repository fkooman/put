<?php

namespace fkooman\Put;

class Coverage
{
    public static function writeReport($outputFile, array $coverageData)
    {
        $output = '<html><head><title>Coverage</title><style>
body {
    font-family: sans-serif;
}

code {
    margin-left: 3em;
}

code.covered {
    background-color: lightgreen;
}

code.uncovered {
    background-color: red;
}

div.code {
    background-color: #eee;
}
</style>
</head><body>';

        foreach ($coverageData as $srcFile => $coverageInfo) {
            $output .= '<h1>'.basename($srcFile).'</h1>';
            $output .= '<div class="code">';
            $fp = fopen($srcFile, 'r');
            $currentLine = 1;
            while (false !== $line = fgets($fp)) {
                if (array_key_exists($currentLine, $coverageInfo)) {
                    if (-1 === $coverageInfo[$currentLine]) {
                        $output .= $currentLine.' <code class="uncovered">'.str_replace(' ', '&nbsp;', htmlentities(str_replace("\t", '    ', $line))).'</code><br>';
                    } else {
                        $output .= $currentLine.' <code class="covered">'.str_replace(' ', '&nbsp;', htmlentities(str_replace("\t", '    ', $line))).'</code><br>';
                    }
                } else {
                    $output .= $currentLine.' <code>'.str_replace(' ', '&nbsp;', htmlentities(str_replace("\t", '    ', $line))).'</code><br>';
                }

                ++$currentLine;
            }
            $output .= '</div>';
        }

        $output .= '</body></html>';

        file_put_contents($outputFile, $output);
    }
}
