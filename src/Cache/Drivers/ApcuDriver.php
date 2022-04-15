<?php

declare(strict_types=1);

namespace Phoxx\Core\Cache\Drivers;

use Phoxx\Core\Cache\Cache;

class ApcuDriver implements Cache
{
    public function getValue(string $index)
    {
        $value = apcu_fetch($index, $sucess);
        return $sucess ? $value : null;
    }

    public function setValue(string $index, $value, int $lifetime = 0): void
    {
        apcu_store($index, $value, $lifetime);
    }

    public function removeValue(string $index): void
    {
        apcu_delete($index);
    }

    public function clear(): void
    {
        apcu_clear_cache();
    }
}
