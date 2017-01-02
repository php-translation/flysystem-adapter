<?php

/*
 * This file is part of the PHP Translation package.
 *
 * (c) PHP Translation team <tobias.nyholm@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Translation\PlatformAdapter\Flysystem\Bridge\Symfony\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\Translation\Writer\TranslationWriter;
use Translation\PlatformAdapter\Flysystem\Dumper\XliffFileDumper;
use Translation\PlatformAdapter\Flysystem\Flysystem;
use Translation\PlatformAdapter\Flysystem\Loader\XliffFileLoader;
use Translation\PlatformAdapter\Flysystem\TranslationLoader;
use Translation\SymfonyStorage\FileStorage;

/**
 * @author Tobias Nyholm <tobias.nyholm@gmail.com>
 */
class TranslationAdapterFlysystemExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration($container);
        $config = $this->processConfiguration($configuration, $configs);

        foreach ($config['filesystems'] as $data) {
            $baseServiceId = 'php_translation.adapter.flysystem.'.$data['name'];
            $flysytemServiceId = $data['flysystem_service'];

            $dumperDef = $container->register($baseServiceId.'.dumper', XliffFileDumper::class);
            $dumperDef->setPublic(false)->addMethodCall('setFilesystem', [new Reference($flysytemServiceId)]);
            $writerDef = $container->register($baseServiceId.'.writer', TranslationWriter::class);
            $writerDef->setPublic(false)->addMethodCall('addDumper', ['xlf', $dumperDef]);

            $xlfLoaderDef = $container->register($baseServiceId.'.xlf_loader', XliffFileLoader::class);
            $xlfLoaderDef->setPublic(false)->addArgument(new Reference($flysytemServiceId));
            $loaderDef = $container->register($baseServiceId.'.loader', TranslationLoader::class);
            $loaderDef->setPublic(false)->addMethodCall('addLoader', ['xlf', $xlfLoaderDef]);

            // Register our file storage.
            $fileStorageDef = $container->register($baseServiceId, FileStorage::class);
            $fileStorageDef
                ->addArgument($writerDef)
                ->addArgument($loaderDef)
                ->addArgument($data['path']);
        }
    }
}
