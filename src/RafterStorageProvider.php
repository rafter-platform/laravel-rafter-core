<?php

namespace Rafter;

use Illuminate\Support\Arr;
use League\Flysystem\Filesystem;
use League\Flysystem\AdapterInterface;
use Illuminate\Support\ServiceProvider;
use Google\Cloud\Storage\StorageClient;
use Superbalist\Flysystem\GoogleStorage\GoogleStorageAdapter;

class RafterStorageProvider extends ServiceProvider
{
    /**
     * Create a Filesystem instance with the given adapter.
     *
     * @param  \League\Flysystem\AdapterInterface $adapter
     * @param  array $config
     * @return \League\Flysystem\FlysystemInterfaceAdapterInterface
     */
    protected function createFilesystem(AdapterInterface $adapter, array $config)
    {
        $config = Arr::only($config, ['visibility', 'disable_asserts', 'url']);

        return new Filesystem($adapter, count($config) > 0 ? $config : null);
    }

    /**
     * Perform post-registration booting of services.
     */
    public function boot()
    {
        $factory = $this->app->make('filesystem');
        /* @var FilesystemManager $factory */
        $factory->extend('gcs', function ($app, $config) {
            $storageClient = $this->createClient($config);

            $bucket = $storageClient->bucket($config['bucket']);
            $pathPrefix = Arr::get($config, 'path_prefix');
            $storageApiUri = Arr::get($config, 'storage_api_uri');

            $adapter = new GoogleStorageAdapter($storageClient, $bucket, $pathPrefix, $storageApiUri);

            return $this->createFilesystem($adapter, $config);
        });
    }

    /**
     * Create a new StorageClient
     *
     * @param  mixed $config
     * @return \Google\Cloud\Storage\StorageClient
     */
    private function createClient($config)
    {
        return new StorageClient();
    }

    /**
     * Register bindings in the container.
     */
    public function register()
    {
        //
    }
}
