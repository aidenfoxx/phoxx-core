<?php

namespace Phoxx\Core\Session;

interface Session
{
    public function getValue(string $index);

    public function setValue(string $index, $value): void;

    public function removeValue(string $index): void;

    public function active(): bool;

    public function open(): void;

    public function close(): void;

    public function regenerate(): void;

    public function clear(): void;
}
