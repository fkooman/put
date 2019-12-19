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

        pre {
            margin: 0;
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
<h1>Test Code Coverage</h1>
<h2>Summary</h2>
    <table>
        <tr><th>% Covered</th><th>File</th></tr>
<?php foreach ($templateData as $srcFile => $srcInfo): ?>
        <tr><td><?=$srcInfo['coveragePercent']; ?>%</td><td><a href="#<?=$srcFile; ?>"><?=$srcFile; ?></a></td></tr>
<?php endforeach; ?>
    </table>

<?php foreach ($templateData as $srcFile => $srcInfo): ?>
    <h2 id="<?=$srcFile; ?>"><?=$srcFile; ?></h2>
    <table>
<?php foreach ($srcInfo['srcCode'] as $srcLineNo => $srcLineText): ?>
        <tr>
            <th><?=$srcLineNo + 1; ?></th>
<?php if (array_key_exists($srcLineNo + 1, $srcInfo['coverageData'])): ?>
<?php if (1 === $srcInfo['coverageData'][$srcLineNo + 1]): ?>
            <td class="covered">
                <pre><?=$srcLineText; ?></pre>
            </td>
<?php else: ?>
            <td class="uncovered">
                <pre><?=$srcLineText; ?></pre>
            </td>
<?php endif; ?>
<?php else: ?>
            <td>
                <pre><?=$srcLineText; ?></pre>
            </td>
<?php endif; ?>
        </tr>
<?php endforeach; ?>
    </table>
<?php endforeach; ?>
</body>
</html>
