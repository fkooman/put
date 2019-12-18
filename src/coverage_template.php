<!DOCTYPE html>

<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Test Code Coverage</title>
    <style>
        body {
            font-family: sans-serif;
        }

        table {
            width: 100%;
            background-color: #eee;
        }

        table th {
            background-color: #fff;
        }

        table td.covered {
            background-color: lightgreen;
        }

        table td.uncovered {
            background-color: lightcoral;
        }
    </style>
</head>
<body>
<?php foreach ($templateData as $srcFile => $srcInfo): ?>
    <h1><?=$srcFile; ?></h1>
    <table>
<?php foreach ($srcInfo['srcCode'] as $srcLineNo => $srcLineText): ?>
        <tr>
            <th><?=$srcLineNo + 1; ?></th>
<?php if (array_key_exists($srcLineNo + 1, $srcInfo['coverageData'])): ?>
<?php if (1 === $srcInfo['coverageData'][$srcLineNo + 1]): ?>
            <td class="covered">
                <code><?=$srcLineText; ?></code>
            </td>
<?php else: ?>
            <td class="uncovered">
                <code><?=$srcLineText; ?></code>
            </td>
<?php endif; ?>
<?php else: ?>
            <td>
                <code><?=$srcLineText; ?></code>
            </td>
<?php endif; ?>
        </tr>
<?php endforeach; ?>
    </table>
<?php endforeach; ?>
</body>
</html>
