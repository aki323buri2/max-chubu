<?php
function nv($value, $backup = null)
{
	return isset($value) ? $value : $backup;
}
function dump($value)
{
	echo '<pre>'."\n";
	var_dump($value);
	echo '</pre>'."\n";
}
function extension($path)
{
	return preg_match('/([.][^.]+)?$/', $path, $match) ? $match[0] : '';
}
function extension_without_dot($path)
{
	return preg_replace('/^[.]/', '', extension($path));
}
function array_group_by($array, $function)
{
	$results = [];
	foreach ($array as $key => $value)
	{
		$keys = $function($value, $key);
		$keys = (array)$keys;

		foreach ($keys as $key)
		{
			if (!array_key_exists($key, $results))
			{
				$results[$key] = [];
			}
			$results[$key][] = $value;
		}
	}
	return $results;
}