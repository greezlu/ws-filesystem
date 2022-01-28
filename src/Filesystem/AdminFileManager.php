<?php
/** Copyright github.com/greezlu */

declare(strict_types=1);

namespace WebServer\Filesystem;

/**
 * @package greezlu/ws-filesystem
 */
class AdminFileManager extends FileManager
{
    protected const WORKING_DIR = '../';

    /**
     * @param string $path
     * @return string
     */
    public function getFullPath(string $path): string
    {
        return $path === '..'
        || substr($path, 0, strlen(static::WORKING_DIR)) === static::WORKING_DIR
            ? $path
            : static::WORKING_DIR . $path;
    }
}
