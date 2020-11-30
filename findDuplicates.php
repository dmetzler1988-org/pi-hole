<?php

$ignoredFiles = ['.', '..', 'README.md', '.DS_Store'];

function recursiveScanDir($dir, &$results = [])
{
    $files = scandir($dir);

    foreach ($files as $key => $value) {
        if (in_array($value, ['.', '..', 'README.md', '.DS_Store'])) {
            continue;
        }

        // TODO: add folder ignore check.

        $path = realpath($dir . DIRECTORY_SEPARATOR . $value);
        if (!is_dir($path)) {
            $results[] = $path;
        } else {
            recursiveScanDir($path, $results);
        }
    }

    return $results;
}

//$files = scandir('blacklists/sorted');
$files = recursiveScanDir('blacklists');
$filesContent = [];
foreach ($files as $file) {
    if (in_array($file, ['.', '..', 'README.md', '.DS_Store'])) {
        continue;
    }

    $content = [];
    //$fileContent = file('sorted/' . $file);
    $fileContent = file($file);
    foreach ($fileContent as $key => $value) {
        $content[$key] = rtrim($value);
    }

    $filesContent[$file] = $content;
}

$merged = [];
foreach ($filesContent as $fileKey => $fileValue) {
    if (empty($merged)) {
        $merged[$fileKey] = $fileValue;
        continue;
    }

    foreach ($merged as $mergeKey => $mergeValue) {
        foreach ($fileValue as $fileContent) {
            foreach ($mergeValue as $mergedContent) {
                if ($mergedContent === $fileContent) {
                    echo $fileKey . ' : ' . $fileContent . ' -> ' . $mergeKey . ' : ' . $mergedContent . PHP_EOL;
                }
            }
        }
    }

    $merged[$fileKey] = $fileValue;
}
