<?php

/*
 * This file is part of the PHP Translation package.
 *
 * (c) PHP Translation team <tobias.nyholm@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Translation\PlatformAdapter\Flysystem\Dumper;

use Symfony\Component\Translation\Dumper\DumperInterface;
use Symfony\Component\Translation\Dumper\XliffFileDumper;
use Symfony\Component\Translation\MessageCatalogue;
use Symfony\Component\Translation\Exception\InvalidArgumentException;
use League\Flysystem\Filesystem;
use Symfony\Component\Translation\Exception\RuntimeException;

/**
 * FileDumper is an implementation of DumperInterface that dump a message catalogue to file(s).
 * Performs backup of already existing files.
 *
 * Options:
 * - path (mandatory): the directory where the files should be saved
 *
 * @author Michel Salib <michelsalib@hotmail.com>
 * @author Tobias Nyholm <tobias.nyholm@gmail.com>
 */
class FlysystemXliffDumper extends XliffFileDumper
{
    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * A template for the relative paths to files.
     *
     * @var string
     */
    protected $relativePathTemplate = '%domain%.%locale%.%extension%';

    /**
     * Make file backup before the dump.
     *
     * @var bool
     */
    private $backup = true;

    /**
     * @param Filesystem $filesystem
     */
    public function setFilesystem($filesystem)
    {
        $this->filesystem = $filesystem;
    }

    /**
     * Sets the template for the relative paths to files.
     *
     * @param string $relativePathTemplate A template for the relative paths to files
     */
    public function setRelativePathTemplate($relativePathTemplate)
    {
        $this->relativePathTemplate = $relativePathTemplate;
    }

    /**
     * Sets backup flag.
     *
     * @param bool
     */
    public function setBackup($backup)
    {
        $this->backup = $backup;
    }

    /**
     * {@inheritdoc}
     */
    public function dump(MessageCatalogue $messages, $options = [])
    {
        if (!array_key_exists('path', $options)) {
            throw new InvalidArgumentException('The file dumper needs a path option.');
        }

        // save a file for each domain
        foreach ($messages->getDomains() as $domain) {
            // backup
            $fullpath = $options['path'].'/'.$this->getRelativePath($domain, $messages->getLocale());
            if ($this->filesystem->has($fullpath)) {
                if ($this->backup) {
                    @trigger_error('Creating a backup while dumping a message catalogue is deprecated since version 3.1 and will be removed in 4.0. Use TranslationWriter::disableBackup() to disable the backup.', E_USER_DEPRECATED);
                    $this->filesystem->copy($fullpath, $fullpath.'~');
                }
            } else {
                $directory = dirname($fullpath);
                if (!$this->filesystem->has($directory) && !@mkdir($directory, 0777, true)) {
                    try {
                        $this->filesystem->createDir($directory);
                    } catch (\Exception $e) {
                        throw new RuntimeException(sprintf('Unable to create directory "%s".', $directory), 0, $e);
                    }
                }
            }
            // save file
            $this->filesystem->write($fullpath, $this->formatCatalogue($messages, $domain, $options));
        }
    }

    /**
     * Gets the relative file path using the template.
     *
     * @param string $domain The domain
     * @param string $locale The locale
     *
     * @return string The relative file path
     */
    private function getRelativePath($domain, $locale)
    {
        return strtr($this->relativePathTemplate, [
            '%domain%' => $domain,
            '%locale%' => $locale,
            '%extension%' => $this->getExtension(),
        ]);
    }
}
