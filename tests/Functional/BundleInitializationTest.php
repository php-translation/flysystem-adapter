<?php

/*
 * This file is part of the PHP Translation package.
 *
 * (c) PHP Translation team <tobias.nyholm@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Translation\PlatformAdapter\Flysystem\Tests\Functional;

use Nyholm\BundleTest\BaseBundleTestCase;
use Translation\PlatformAdapter\Flysystem\Bridge\Symfony\TranslationAdapterFlysystemBundle;
use Translation\SymfonyStorage\FileStorage;

class BundleInitializationTest extends BaseBundleTestCase
{
    protected function getBundleClass()
    {
        return TranslationAdapterFlysystemBundle::class;
    }

    public function testRegisterBundle()
    {
        $kernel = $this->createKernel();
        $kernel->addConfigFile(__DIR__.'/config.yml');

        $this->bootKernel();
        $container = $this->getContainer();

        $this->assertTrue($container->has('php_translation.adapter.flysystem.foo'));
        $service = $container->get('php_translation.adapter.flysystem.foo');
        $this->assertInstanceOf(FileStorage::class, $service);
    }
}
