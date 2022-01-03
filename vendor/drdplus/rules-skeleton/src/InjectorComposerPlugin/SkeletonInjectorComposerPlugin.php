<?php declare(strict_types=1);

namespace DrdPlus\RulesSkeleton\InjectorComposerPlugin;

use Composer\Composer;
use Composer\DependencyResolver\Operation\InstallOperation;
use Composer\DependencyResolver\Operation\UpdateOperation;
use Composer\EventDispatcher\EventSubscriberInterface;
use Composer\Installer\PackageEvent;
use Composer\Installer\PackageEvents;
use Composer\IO\IOInterface;
use Composer\Plugin\PluginInterface;
use DrdPlus\RulesSkeleton\Configurations\Configuration;
use DrdPlus\RulesSkeleton\Configurations\Dirs;
use Granam\Strict\Object\StrictObject;
use Granam\WebContentBuilder\AssetsVersion;

class SkeletonInjectorComposerPlugin extends StrictObject implements PluginInterface, EventSubscriberInterface
{
    public const RULES_SKELETON_PACKAGE_NAME = 'drdplus/rules-skeleton';

    private ?\Composer\IO\IOInterface $io = null;
    private bool $alreadyInjected = false;
    private string $skeletonPackageName;

    public static function getSubscribedEvents(): array
    {
        return [
            PackageEvents::POST_PACKAGE_INSTALL => 'plugInSkeleton',
            PackageEvents::POST_PACKAGE_UPDATE => 'plugInSkeleton',
        ];
    }

    public function __construct()
    {
        $this->skeletonPackageName = static::RULES_SKELETON_PACKAGE_NAME;
    }

    public function activate(Composer $composer, IOInterface $io): void
    {
        $this->io = $io;
    }

    public function plugInSkeleton(PackageEvent $event)
    {
        if ($this->alreadyInjected || !$this->isThisPackageChanged($event)) {
            return;
        }
        $documentRoot = $GLOBALS['documentRoot'] ?? getcwd();
        $this->io->write("Injecting {$this->skeletonPackageName} using document root '$documentRoot'");
        $this->copyProjectConfig($documentRoot);
        $this->publishSkeletonCss($documentRoot);
        $this->publishSkeletonJs($documentRoot);
        $this->publishSkeletonImages($documentRoot);
        $this->copyFavicon($documentRoot);
        $this->addVersionsToAssets($documentRoot);
        $this->flushCache($documentRoot);
        $this->copyGoogleVerification($documentRoot);
        $this->copyGitignoreToCache($documentRoot);
        $this->populateRoutes($documentRoot);
        $this->populateIndex($documentRoot);
        $this->populateWebDir($documentRoot);
        $this->alreadyInjected = true;
        $this->io->write("Injection of {$this->skeletonPackageName} finished");
    }

    private function isThisPackageChanged(PackageEvent $event): bool
    {
        /** @var InstallOperation|UpdateOperation $operation */
        $operation = $event->getOperation();
        if ($operation instanceof InstallOperation) {
            $changedPackageName = $operation->getPackage()->getName();
        } elseif ($operation instanceof UpdateOperation) {
            $changedPackageName = $operation->getInitialPackage()->getName();
        } else {
            return false;
        }

        return $this->isChangedPackageThisOne($changedPackageName);
    }

    private function isChangedPackageThisOne(string $changedPackageName): bool
    {
        return $changedPackageName === $this->skeletonPackageName;
    }

    private function publishSkeletonImages(string $documentRoot): void
    {
        $this->passThrough(
            [
                'rm -fr ./images/generic/skeleton/',
                'mkdir --parents ./images/generic/skeleton/',
                "cp -r ./vendor/{$this->skeletonPackageName}/images/generic/skeleton/* ./images/generic/skeleton/",
                "cp --no-clobber ./vendor/{$this->skeletonPackageName}/images/main-background.png ./images/",
                "cp --no-clobber ./vendor/{$this->skeletonPackageName}/images/licence-background.png ./images/",
            ],
            $documentRoot
        );
    }

    private function passThrough(array $commands, string $workingDir = null): void
    {
        if ($workingDir !== null) {
            $escapedWorkingDir = \escapeshellarg($workingDir);
            \array_unshift($commands, 'cd ' . $escapedWorkingDir);
        }
        foreach ($commands as &$command) {
            $command .= ' 2>&1';
        }
        unset($command);
        $chain = \implode(' && ', $commands);
        \exec($chain, $output, $returnCode);
        if ($returnCode !== 0) {
            $this->io->writeError(
                "Failed injecting skeleton by command $chain\nGot return code $returnCode and output " . \implode("\n", $output)
            );

            return;
        }
        $this->io->write($chain);
        if ($output) {
            $this->io->write(' ' . \implode("\n", $output));
        }
    }

    private function copyGoogleVerification(string $documentRoot): void
    {
        $this->passThrough(["cp ./vendor/{$this->skeletonPackageName}/google8d8724e0c2818dfc.html ."], $documentRoot);
    }

    private function addVersionsToAssets(string $documentRoot)
    {
        $assetsVersion = new AssetsVersion(true, false);
        $changedFiles = $assetsVersion->addVersionsToAssetLinks($documentRoot, ['css'], [], [], false);
        if ($changedFiles) {
            $this->io->write('Those assets got versions to asset links: ' . \implode(', ', $changedFiles));
        }
    }

    private function flushCache(string $documentRoot): void
    {
        $this->passThrough(
            [
                'mkdir --parents ./cache',
                'chmod 0775 ./cache',
                'find ./cache -mindepth 2 -type f -exec rm {} +',
            ],
            $documentRoot
        );
    }

    private function publishSkeletonCss(string $documentRoot): void
    {
        $this->passThrough(
            [
                'rm -fr ./css/generic/skeleton/',
                'mkdir --parents ./css/generic/skeleton/',
                "cp -r ./vendor/{$this->skeletonPackageName}/css/generic/skeleton/* ./css/generic/skeleton/",
            ],
            $documentRoot
        );
        $this->removeFilesIfHasFreeAccess(['./css/generic/skeleton/rules-gateway.css'], $documentRoot);
    }

    private function removeFilesIfHasFreeAccess(array $filesToRemove, string $documentRoot)
    {
        $dirs = $this->getDirs($documentRoot);
        $configuration = $this->getConfiguration($dirs);
        if ($configuration && $configuration->getGatewayConfiguration()->hasProtectedAccess()) {
            return;
        }
        $this->passThrough(
            array_map(
                static fn(string $fileToRemove) => 'rm ' . escapeshellarg($fileToRemove),
                $filesToRemove
            ),
            $documentRoot
        );
    }

    protected function getConfiguration(Dirs $dirs): ?Configuration
    {
        if (!Configuration::canCreateFromYml($dirs)) {
            return null;
        }
        return Configuration::createFromYml($dirs);
    }

    protected function getDirs(string $documentRoot)
    {
        return new Dirs($documentRoot);
    }

    private function publishSkeletonJs(string $documentRoot): void
    {
        $this->passThrough(
            [
                'rm -fr ./js/generic/skeleton/',
                'mkdir --parents ./js/generic/skeleton/',
                "cp -r ./vendor/{$this->skeletonPackageName}/js/generic/skeleton/* ./js/generic/skeleton/",
            ],
            $documentRoot
        );
        $this->removeFilesIfHasFreeAccess(
            [
                './js/generic/skeleton/rules-gateway-pass-expiration.js',
                './js/generic/skeleton/rules-gateway-pass-hash.js',
            ],
            $documentRoot
        );
    }

    private function copyProjectConfig(string $documentRoot): void
    {
        if (!\file_exists('config.distribution.yml')) {
            $this->passThrough(["cp --no-clobber ./vendor/{$this->skeletonPackageName}/config.distribution.yml ."], $documentRoot);

            return;
        }
        $rulesSkeletonConfigContent = \file_get_contents("vendor/{$this->skeletonPackageName}/config.distribution.yml");
        if (\file_get_contents('config.distribution.yml') !== $rulesSkeletonConfigContent) {
            return;
        }
        $this->passThrough(["cp ./vendor/$this->skeletonPackageName/config.distribution.yml ."], $documentRoot);
    }

    private function copyGitignoreToCache(string $documentRoot): void
    {
        $this->passThrough(
            [
                'mkdir --parents ./cache',
                'chmod 0775 ./cache',
                "cp ./vendor/{$this->skeletonPackageName}/cache/.gitignore ./cache/.gitignore",
            ],
            $documentRoot
        );
    }

    private function copyFavicon(string $documentRoot): void
    {
        $this->passThrough(
            [
                "cp ./vendor/{$this->skeletonPackageName}/favicon.ico .",
            ],
            $documentRoot
        );
    }

    private function populateRoutes(string $documentRoot): void
    {
        $this->passThrough(
            [
                "cp --no-clobber ./vendor/{$this->skeletonPackageName}/routes.yml .",
            ],
            $documentRoot
        );
    }

    private function populateIndex(string $documentRoot): void
    {
        $this->passThrough(
            [
                sprintf('cp --no-clobber %s ./index.php', escapeshellarg(__DIR__ . '/files/pattern_index.php')),
            ],
            $documentRoot
        );
    }

    private function populateWebDir(string $documentRoot): void
    {
        $this->passThrough(
            [
                'mkdir --parents ./web',
            ],
            $documentRoot
        );
    }

    public function deactivate(Composer $composer, IOInterface $io)
    {
        // nothing to do here
    }

    public function uninstall(Composer $composer, IOInterface $io)
    {
        // nothing to do here
    }

}
