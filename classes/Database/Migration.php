<?php

namespace Phoxx\Core\Database;

abstract class Migration
{
	abstract public function up(): bool;
	
	abstract public function down(): bool;
}