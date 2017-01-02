<?php

/*
 * This file is part of the PHP Translation package.
 *
 * (c) PHP Translation team <tobias.nyholm@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Translation\PlatformAdapter\Flysystem;

use League\Flysystem\Filesystem;
use Symfony\Component\Translation\MessageCatalogue;
use Symfony\Component\Translation\Loader\LoaderInterface;

/**
 * TranslationLoader loads translation messages from translation files.
 *
 * @author Tobias Nyholm <tobias.nyholm@gmail.com>
 */
class TranslationLoader implements \Translation\SymfonyStorage\TranslationLoader
{
    /**
     * Loaders used for import.
     *
     * @var LoaderInterface[]
     */
    private $loaders = [];

    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * @param Filesystem $filesystem
     */
    public function __construct(Filesystem $filesystem)
    {
        $this->filesystem = $filesystem;
    }

    /**
     * Adds a loader to the translation extractor.
     *
     * @param string          $format The format of the loader
     * @param LoaderInterface $loader
     */
    public function addLoader($format, LoaderInterface $loader)
    {
        $this->loaders[$format] = $loader;
    }

    /**
     * Loads translation messages from a directory to the catalogue.
     *
     * @param string           $directory the directory to look into
     * @param MessageCatalogue $catalogue the catalogue
     */
    public function loadMessages($directory, MessageCatalogue $catalogue)
    {
        if (!$this->filesystem->has($directory)) {
            return;
        }

        foreach ($this->loaders as $format => $loader) {
            // load any existing translation files
            $extension = $catalogue->getLocale().'.'.$format;
            $files = $this->filesystem->listFiles($directory, true);
            foreach ($files as $file) {
                $path = $file['path'];
                $filename = $this->basename($path);
                $domain = substr($filename, 0, -1 * strlen($extension) - 1);
                $catalogue->addCatalogue($loader->load($path, $catalogue->getLocale(), $domain));
            }
        }
    }

    /**
     * Gets the filename from a given path.
     *
     * PHP's basename() does not properly support streams or filenames beginning with a non-US-ASCII character.
     *
     * @author Drupal 8.2
     *
     * @param string $path
     *
     * @return string
     */
    private function basename($path)
    {
        $separators = '/';
        if (DIRECTORY_SEPARATOR != '/') {
            // For Windows OS add special separator.
            $separators .= DIRECTORY_SEPARATOR;
        }

        // Remove right-most slashes when $path points to directory.
        $path = rtrim($path, $separators);

        // Returns the trailing part of the $path starting after one of the directory separators.
        $filename = preg_match('@[^'.preg_quote($separators, '@').']+$@', $path, $matches) ? $matches[0] : '';

        return $filename;
    }
}
