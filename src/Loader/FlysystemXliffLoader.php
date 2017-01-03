<?php

/*
 * This file is part of the PHP Translation package.
 *
 * (c) PHP Translation team <tobias.nyholm@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Translation\PlatformAdapter\Flysystem\Loader;

use League\Flysystem\Filesystem;
use Symfony\Component\Translation\MessageCatalogue;
use Symfony\Component\Translation\Exception\NotFoundResourceException;
use Symfony\Component\Config\Resource\FileResource;
use Translation\SymfonyStorage\Loader\XliffLoader;

/**
 * XliffFileLoader loads translations from XLIFF files.
 *
 * @author Tobias Nyholm <tobias.nyholm@gmail.com>
 */
final class FlysystemXliffLoader extends XliffLoader
{
    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * @param Filesystem filesystem
     */
    public function __construct(Filesystem $filesystem)
    {
        $this->filesystem = $filesystem;
    }

    /**
     * {@inheritdoc}
     */
    public function load($resource, $locale, $domain = 'messages')
    {
        if (!$this->filesystem->has($resource)) {
            throw new NotFoundResourceException(sprintf('File "%s" not found.', $resource));
        }

        $catalogue = new MessageCatalogue($locale);
        $this->extractFromContent($this->filesystem->read($resource), $catalogue, $domain);

        if (class_exists('Symfony\Component\Config\Resource\FileResource')) {
            $catalogue->addResource(new FileResource($resource));
        }

        return $catalogue;
    }
}
