<?php

use function Illuminate\Filesystem\join_paths;

if (!function_exists('runtime_path')) {
	function runtime_path(string $path = ''): string
	{
		return app()->basePath(join_paths('.runtime', $path));
	}
}

/**
 * $firstNameAtBeginning = true: "Иван Иванов" -> ['Иван', 'Иванов']
 * $firstNameAtBeginning = false: "Иванов Иван" -> ['Иван', 'Иванов']
 */
function split_full_name(string $fullName, bool $firstNameAtBeginning = true): array
{
	$parts = preg_split('/\s+/', trim($fullName));

	$firstName = $parts[0] ?? '';
	$lastName = $parts[1] ?? '';

	return $firstNameAtBeginning ? [$firstName, $lastName] : [$lastName, $firstName];
}
