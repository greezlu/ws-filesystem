<?php
/** Copyright github.com/greezlu */

declare(strict_types=1);

namespace WebServer\Filesystem;

use WebServer\Exceptions\LocalizedException;

/**
 * @package greezlu/ws-filesystem
 */
class FileManager
{
    protected const WORKING_DIR = './';

    protected const PERMISSION = 0775;

    /**
     * @param string|null $dirPath
     * @throws LocalizedException
     */
    public function __construct(string $dirPath = null)
    {
        $dirPath = $dirPath ?? static::WORKING_DIR;
        $this->createDir($dirPath);
    }

    /**
     * @param string $path
     * @return string
     */
    protected function setWorkingDir(string $path): string
    {
        return $path === '.'
        || substr($path, 0, strlen(static::WORKING_DIR)) === static::WORKING_DIR
            ? $path
            : static::WORKING_DIR . $path;
    }

    /**
     * @param string $filePath
     * @return bool
     */
    public function isFile(string $filePath): bool
    {
        $filePath = $this->setWorkingDir($filePath);
        return is_file($filePath);
    }

    /**
     * @param string $dirPath
     * @return bool
     */
    public function isDir(string $dirPath): bool
    {
        $dirPath = $this->setWorkingDir($dirPath);
        return is_dir($dirPath);
    }

    /**
     * @param string $dirPath
     * @return array
     * @throws LocalizedException
     */
    public function readDir(string $dirPath): array
    {
        $dirPath = $this->setWorkingDir($dirPath);

        if (!$this->isDir($dirPath)) {
            throw new LocalizedException('Unable to find dir: ' . $dirPath);
        }

        $dirContent = scandir($dirPath);

        if (!is_array($dirContent)) {
            throw new LocalizedException('Unable to read dir: ' . $dirPath);
        }

        return array_diff($dirContent, ['..', '.']);
    }

    /**
     * @param string $filePath
     * @return string
     * @throws LocalizedException
     */
    public function readFile(string $filePath): string
    {
        $filePath = $this->setWorkingDir($filePath);

        if (!$this->isFile($filePath)) {
            throw new LocalizedException('Unable to find file: ' . $filePath);
        }

        $fileContent = file_get_contents($filePath);

        if (!is_string($fileContent)) {
            throw new LocalizedException('Unable to read file: ' . $filePath);
        }

        return $fileContent;
    }

    /**
     * @param string $filePath
     * @param string $fileContent
     * @return void
     * @throws LocalizedException
     */
    public function createFile(string $filePath, string $fileContent): void
    {
        $filePath = $this->setWorkingDir($filePath);

        $pathInfo = pathinfo($filePath);

        if (!$this->isDir($pathInfo['dirname'])) {
            $this->createDir($pathInfo['dirname']);
        }

        $success = file_put_contents($filePath, $fileContent);

        if ($success === false) {
            throw new LocalizedException('Unable to write file: ' . $filePath);
        }
    }

    /**
     * @param string $dirPath
     * @return void
     * @throws LocalizedException
     */
    public function createDir(string $dirPath): void
    {
        $dirPath = $this->setWorkingDir($dirPath);

        if ($this->isDir($dirPath)) {
            return;
        }

        $success = mkdir($dirPath, static::PERMISSION, true);

        if ($success === false) {
            throw new LocalizedException('Unable to create dir: ' . $dirPath);
        }
    }

    /**
     * @param string $filePath
     * @param array|null $variableList
     * @return void
     */
    public function include(string $filePath, array $variableList = null): void
    {
        $filePath = $this->setWorkingDir($filePath);

        if (!$this->isFile($filePath)) {
            return;
        }

        if (is_array($variableList)) {
            extract($variableList);
        }

        include $filePath;
    }
}
