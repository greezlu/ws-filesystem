<?php
/** Copyright github.com/greezlu */

declare(strict_types=1);

namespace WebServer\Abstracts;

use WebServer\Exceptions\LocalizedException;

/**
 * @package greezlu/ws-filesystem
 */
abstract class FileManagerAbstract
{
    protected const DEFAULT_WORKING_DIR = null;

    protected const PERMISSION = 0775;

    /**
     * @var string 
     */
    protected string $dirPath;

    /**
     * @param string|null $dirPath
     * @throws LocalizedException
     */
    public function __construct(string $dirPath = null)
    {
        $this->dirPath = $dirPath ?? static::DEFAULT_WORKING_DIR;
        $this->createDir($this->dirPath);
    }

    /**
     * @param string $path
     * @return string
     */
    public function getFullPath(string $path): string
    {
        return $path === trim($this->dirPath, '/')
        || substr($path, 0, strlen($this->dirPath)) === $this->dirPath
            ? $path
            : $this->dirPath . '/'  . $path;
    }

    /**
     * @param string $filePath
     * @return bool
     */
    public function isFile(string $filePath): bool
    {
        $filePath = $this->getFullPath($filePath);
        return is_file($filePath);
    }

    /**
     * @param string $dirPath
     * @return bool
     */
    public function isDir(string $dirPath): bool
    {
        $dirPath = $this->getFullPath($dirPath);
        return is_dir($dirPath);
    }

    /**
     * @param string $dirPath
     * @return array
     * @throws LocalizedException
     */
    public function readDir(string $dirPath): array
    {
        $dirPath = $this->getFullPath($dirPath);

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
        $filePath = $this->getFullPath($filePath);

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
     * @return void
     * @throws LocalizedException
     */
    public function openFile(string $filePath): void
    {
        echo $this->readFile($filePath);
    }

    /**
     * @param string $filePath
     * @param string $fileContent
     * @return void
     * @throws LocalizedException
     */
    public function createFile(string $filePath, string $fileContent): void
    {
        $filePath = $this->getFullPath($filePath);

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
        $dirPath = $this->getFullPath($dirPath);

        if ($this->isDir($dirPath)) {
            return;
        }

        $success = mkdir($dirPath, static::PERMISSION, true);

        if ($success === false) {
            throw new LocalizedException('Unable to create dir: ' . $dirPath);
        }
    }
}
