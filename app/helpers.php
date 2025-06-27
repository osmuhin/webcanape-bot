<?php

use function Illuminate\Filesystem\join_paths;

if (!function_exists('runtime_path')) {
	function runtime_path(string $path = ''): string
	{
		return app()->basePath(join_paths('.runtime', $path));
	}
}
