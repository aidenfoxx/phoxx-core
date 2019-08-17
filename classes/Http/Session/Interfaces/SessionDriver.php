<?php 

namespace Phoxx\Core\Http\Session\Interfaces;

interface SessionDriver
{
	public function getValue(string $index);

	public function flashValue(string $index);

	public function setValue(string $index, $value): void;

	public function removeValue(string $index): void;

	public function active(): bool;

	public function open(): void;

	public function close(): void;

	public function clear(): void;
}