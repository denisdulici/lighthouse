<?php

namespace Tests;

use Illuminate\Cache\CacheManager;
use Illuminate\Cache\Repository;
use Illuminate\Contracts\Container\Container;
use Nuwave\Lighthouse\Subscriptions\Contracts\ContextSerializer;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

trait TestsSerialization
{
    protected function fakeContextSerializer(Container $app): void
    {
        $app->bind(ContextSerializer::class, function (): ContextSerializer {
            return new class implements ContextSerializer {
                public function serialize(GraphQLContext $context)
                {
                    return 'foo';
                }

                public function unserialize(string $context)
                {
                    return new class implements GraphQLContext {
                        public function user()
                        {
                            //
                        }

                        public function request()
                        {
                            //
                        }
                    };
                }
            };
        });
    }

    protected function useSerializingArrayStore(Container $app): void
    {
        /** @var \Illuminate\Contracts\Config\Repository $config */
        $config = $app['config'];

        /** @var \Illuminate\Cache\CacheManager $cache */
        $cache = $app->make(CacheManager::class);
        $cache->extend('serializing-array', function () {
            return new Repository(
                new SerializingArrayStore()
            );
        });
        $config->set('cache.stores.array.driver', 'serializing-array');
    }
}
