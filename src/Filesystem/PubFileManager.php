<?php
/** Copyright github.com/greezlu */

declare(strict_types=1);

namespace WebServer\Filesystem;

use WebServer\Abstracts\FileManagerAbstract;

/**
 * @package greezlu/ws-filesystem
 */
class PubFileManager extends FileManagerAbstract
{
    protected const DEFAULT_WORKING_DIR = './files';

    protected const PUBLIC_PATH = '/file';
}
