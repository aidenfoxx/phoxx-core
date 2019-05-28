<?php 

namespace Phoxx\Core\Http\Session\Interfaces;

interface SessionDriver
{
	public function getValue(string $index);

	public function flashValue(string $index);

	public function setValue(string $index, $value): void;

	public function removeValue(string $index): void;

	public function active(): bool;

	public function open(): bool;

	public function close(): bool;

	public function clear(): void;
}