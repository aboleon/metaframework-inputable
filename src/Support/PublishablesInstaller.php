<?php

namespace MetaFramework\Inputable\Support;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Throwable;

class PublishablesInstaller
{
    /**
     * @param  bool  $force  Overwrite existing publishables.
     * @return array{copied: string[], skipped: string[], failed: array<int, array{source: string, destination: string, error: string}>}
     */
    public static function install(bool $force = false): array
    {
        $packagePath = dirname(__DIR__, 2);

        $resources = [
            $packagePath . '/publishable/config/mfw-inputable.php' => config_path('mfw-inputable.php'),
            $packagePath . '/publishable/assets' => public_path('vendor/mfw-inputable'),
        ];

        $summary = [
            'copied' => [],
            'skipped' => [],
            'failed' => [],
        ];

        foreach ($resources as $source => $destination) {
            if (! File::exists($source)) {
                $summary['skipped'][] = $destination;
                continue;
            }

            try {
                if (File::exists($destination)) {
                    if (! $force) {
                        if (File::isDirectory($destination) && self::directoryIsEmpty($destination)) {
                            // fall through and refresh empty directory
                        } else {
                            $summary['skipped'][] = $destination;
                            continue;
                        }
                    }

                    if (File::isDirectory($destination)) {
                        File::deleteDirectory($destination);
                    } else {
                        File::delete($destination);
                    }
                }

                if (File::isDirectory($source)) {
                    File::ensureDirectoryExists(dirname($destination));
                    File::copyDirectory($source, $destination);
                } else {
                    File::ensureDirectoryExists(dirname($destination));
                    File::copy($source, $destination);
                }

                $summary['copied'][] = $destination;
            } catch (Throwable $exception) {
                Log::warning(
                    'Metaframework Inputable: unable to copy publishable resource.',
                    [
                        'source' => $source,
                        'destination' => $destination,
                        'error' => $exception->getMessage(),
                    ]
                );

                $summary['failed'][] = [
                    'source' => $source,
                    'destination' => $destination,
                    'error' => $exception->getMessage(),
                ];
            }
        }

        // Copy lang directory contents
        $langSource = $packagePath . '/publishable/lang';
        $langDestination = lang_path();

        if (File::exists($langSource) && File::isDirectory($langSource)) {
            $langResult = self::copyDirectoryContents($langSource, $langDestination, $force);
            $summary['copied'] = array_merge($summary['copied'], $langResult['copied']);
            $summary['skipped'] = array_merge($summary['skipped'], $langResult['skipped']);
            $summary['failed'] = array_merge($summary['failed'], $langResult['failed']);
        }

        return $summary;
    }

    /**
     * Copy directory contents (not the directory itself).
     */
    private static function copyDirectoryContents(string $source, string $destination, bool $force): array
    {
        $result = [
            'copied' => [],
            'skipped' => [],
            'failed' => [],
        ];

        File::ensureDirectoryExists($destination);

        try {
            $items = File::allFiles($source);

            foreach ($items as $item) {
                $relativePath = str_replace($source . DIRECTORY_SEPARATOR, '', $item->getPathname());
                $targetPath = $destination . DIRECTORY_SEPARATOR . $relativePath;

                if (File::exists($targetPath) && !$force) {
                    $result['skipped'][] = $targetPath;
                    continue;
                }

                File::ensureDirectoryExists(dirname($targetPath));
                File::copy($item->getPathname(), $targetPath);
                $result['copied'][] = $targetPath;
            }
        } catch (Throwable $exception) {
            Log::warning(
                'Metaframework Inputable: unable to copy directory contents.',
                [
                    'source' => $source,
                    'destination' => $destination,
                    'error' => $exception->getMessage(),
                ]
            );

            $result['failed'][] = [
                'source' => $source,
                'destination' => $destination,
                'error' => $exception->getMessage(),
            ];
        }

        return $result;
    }

    private static function directoryIsEmpty(string $path): bool
    {
        if (! File::isDirectory($path)) {
            return false;
        }

        return count(File::allFiles($path)) === 0 && count(File::directories($path)) === 0;
    }
}
