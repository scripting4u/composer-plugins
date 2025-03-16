<?php

declare(strict_types=1);

namespace Scripting4U\ComposerPlugins;

use Composer\Composer;
use Composer\EventDispatcher\EventSubscriberInterface;
use Composer\Factory;
use Composer\IO\IOInterface;
use Composer\Plugin\PluginInterface;
use Composer\Script\ScriptEvents;

class CopyCsFiles implements PluginInterface, EventSubscriberInterface
{
    private IOInterface $io;
    private Composer $composer;

    public const CODING_STANDARD_PACKAGE = 'scripting4u/coding-standards';

    public array $filesToCopy = [
        'phpmd.xml',
        'phpstan.neon',
        'grumphp.yml'
    ];

    public function activate(Composer $composer, IOInterface $io)
    {
        $this->io       = $io;
        $this->composer = $composer;
    }

    public function deactivate(Composer $composer, IOInterface $io)
    {
        // do nothing
    }

    public function uninstall(Composer $composer, IOInterface $io)
    {
        // do nothing
    }

    public function copyCsFiles()
    {
        $localRepository = $this->composer
            ->getRepositoryManager()
            ->getLocalRepository();

        $installationManager = $this->composer->getInstallationManager();

        $packages              = $localRepository->getPackages();
        $codingStandardPackage = array_reduce(
            $packages,
            function ($carry, $package) {
                if ($package->getName() === self::CODING_STANDARD_PACKAGE) {
                    $carry = $package;
                }
                return $carry;
            }
        );

        $packagePath              = $installationManager->getInstallPath($codingStandardPackage);
        $fileToCopyFromPackageDir = scandir($packagePath);

        foreach ($fileToCopyFromPackageDir as $file) {
            if (!in_array($file, $this->filesToCopy)) {
                continue;
            }
            // Copies from vendor/coding-standards/<file>
            // to ./<file> to ensure latest version is present.
            copy(
                join(DIRECTORY_SEPARATOR, [$packagePath, DIRECTORY_SEPARATOR, $file]),
                join(DIRECTORY_SEPARATOR, [dirname(Factory::getComposerFile()), $file])
            );
            $this->io->write('Copied ' . $file);
        }
    }

    public static function getSubscribedEvents()
    {
        return [
            ScriptEvents::POST_INSTALL_CMD => 'copyCsFiles'
        ];
    }
}
