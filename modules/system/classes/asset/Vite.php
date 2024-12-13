<?php

namespace System\Classes\Asset;

use Illuminate\Foundation\Vite as LaravelVite;
use Illuminate\Support\Facades\App;
use Illuminate\Support\HtmlString;
use Winter\Storm\Exception\SystemException;

class Vite extends LaravelVite
{
    /**
     * Generate Vite tags for an entrypoint(s).
     *
     * @param string|array $entrypoints The list of entry points for Vite
     * @param string|null $package The package name of the plugin or theme
     * @param string|null $buildDirectory The Vite build directory
     *
     * @return HtmlString
     *
     * @throws SystemException
     */
    public function __invoke($entrypoints, $package = null, $buildDirectory = null)
    {
        if (!$package) {
            throw new \InvalidArgumentException('A package must be passed');
        }

        // Normalise the package name
        $package = strtolower($package);

        if (!($compilableAssetPackage = PackageManager::instance()->getPackages('vite')[$package] ?? null)) {
            throw new SystemException('Unable to resolve package: ' . $package);
        }

        $this->useHotFile(base_path($compilableAssetPackage['path'] . '/assets/dist/hot'));
        return parent::__invoke($entrypoints, $compilableAssetPackage['path'] . ($buildDirectory ?? '/assets/dist'));
    }

    /**
     * Helper method to generate Vite tags for an entrypoint(s).
     *
     * @param string|array $entrypoints The list of entry points for Vite
     * @param string $package The package name of the plugin or theme
     * @param string|null $buildDirectory The Vite build directory
     *
     * @throws SystemException
     */
    public static function tags(array|string $entrypoints, string $package, ?string $buildDirectory = null): HtmlString
    {
        return App::make(\Illuminate\Foundation\Vite::class)($entrypoints, $package, $buildDirectory);
    }
}
