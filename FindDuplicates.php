<?php

// TODO: remove 'restored' on '$ignoredFolders'
// TODO: try speedups for checks
// TODO: make output to csv, txt and simple echo to choose via option on run command

class FindDuplicates
{
    protected array $ignoredFiles = ['README.md', '.DS_Store', 'blocklist.txt'];
    protected array $ignoredFolders = ['.', '..', 'backup', 'restored'];
    protected array $ignoredLines = ['comments' => '/^\#/'];

    public function __construct()
    {
        $files = $this->recursiveScanDir('blacklists');
        $filesContent = $this->getFilesContents($files);

        $merged = [];
        foreach ($filesContent as $fileKey => $fileValue) {
            if (empty($merged)) {
                $merged[$fileKey] = $fileValue;
                continue;
            }

            foreach ($merged as $mergeKey => $mergeValue) {
                foreach ($fileValue as $fileContent) {
                    foreach ($mergeValue as $mergedContent) {
                        if (
                            empty($fileContent) ||
                            empty($mergedContent) ||
                            preg_match($this->ignoredLines['comments'], $fileContent) ||
                            preg_match($this->ignoredLines['comments'], $mergedContent)
                        ) {
                            continue;
                        }

                        if ($mergedContent === $fileContent) {
                            echo $fileKey . ' : ' . $fileContent . ' -> ' . $mergeKey . ' : ' . $mergedContent . PHP_EOL;
                        }
                    }
                }
            }

            $merged[$fileKey] = $fileValue;
        }
    }

    protected function recursiveScanDir($dir, &$results = []): array
    {
        $files = scandir($dir);

        foreach ($files as $key => $value) {
            if (in_array($value, $this->ignoredFiles) || in_array($value, $this->ignoredFolders)) {
                continue;
            }

            $path = realpath($dir . DIRECTORY_SEPARATOR . $value);
            if (!is_dir($path)) {
                $results[] = $path;
            } else {
                $this->recursiveScanDir($path, $results);
            }
        }

        return $results;
    }

    protected function getFilesContents(array $files): array
    {
        $filesContent = [];
        foreach ($files as $file) {
            if (in_array($file, $this->ignoredFiles) || in_array($file, $this->ignoredFolders)) {
                continue;
            }

            $content = [];
            $fileContent = file($file);
            foreach ($fileContent as $key => $value) {
                $content[$key] = rtrim($value);
            }

            $filesContent[$file] = $content;
        }

        return $filesContent;
    }
}

$findDuplicates = new FindDuplicates();
