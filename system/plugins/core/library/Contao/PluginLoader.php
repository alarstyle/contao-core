<?php

namespace Contao;


/**
 * Loads plugins based on their plugin.json configuration
 *
 * Usage:
 *
 *     $arrPlugins = PluginLoader::getActive();
 *     $arrPlugins = PluginLoader::getDisabled();
 */
class PluginLoader
{

	/**
	 * Active plugins
	 * @var array
	 */
	protected static $active;

	/**
	 * Disabled plugins
	 * @var array
	 */
	protected static $disabled;


	/**
	 * Return the active plugins as array
	 *
	 * @return array An array of active plugins
	 */
	public static function getActive()
	{
		if (static::$active === null)
		{
			static::scanAndResolve();
		}

		return static::$active;
	}


	/**
	 * Return the disabled plugins as array
	 *
	 * @return array An array of disabled plugins
	 */
	public static function getDisabled()
	{
		if (static::$active === null)
		{
			static::scanAndResolve();
		}

		return static::$disabled;
	}


	/**
	 * Scan the plugins and resolve their dependencies
	 *
	 * @throws \UnresolvableDependenciesException If the dependencies cannot be resolved
	 */
	protected static function scanAndResolve()
	{
		$strCacheFile = TL_ROOT . '/system/cache/config/plugins.php';
		$strPluginsDir = TL_ROOT . '/system/plugins';

		// Try to load from cache
		if (!\Config::get('bypassCache') && file_exists($strCacheFile))
		{
			include $strCacheFile;
		}
		else
		{
			$load = [];

			static::$active = [];
			static::$disabled = [];

			// Ignore non-core plugins if the system runs in safe mode
			if (\Config::get('coreOnlyMode'))
			{
				$plugins = ['core', 'devtools', 'news', 'newsletter', 'repository'];
			}
			else
			{
				$plugins = scan($strPluginsDir);
				sort($plugins);

				// Filter dot resources and files
				foreach ($plugins as $k=>$strPluginName)
				{
					if (strncmp($strPluginName, '.', 1) === 0)
					{
						unset($plugins[$k]);
					}
					elseif (!is_dir($strPluginsDir . '/' . $strPluginName))
					{
						unset($plugins[$k]);
					}
				}

				// Load the "core" plugin first
				array_unshift($plugins, 'core');
				$plugins = array_unique($plugins);
			}

			// Filter disabled plugins
			foreach ($plugins as $k=>$v)
			{
				if (file_exists($strPluginsDir . '/' . $v . '/.skip'))
				{
					unset($plugins[$k]);
					static::$disabled[] = $v;
				}
			}

			// Walk through the plugins
			foreach ($plugins as $plugin)
			{
				$load[$plugin] = array();
				$path = $strPluginsDir . '/' . $plugin;

				// Read the plugin.json if any
				if (file_exists($path . '/plugin.json'))
				{
					$data = file_get_contents($path . '/plugin.json');
					$config = json_decode($data, true);
					$load[$plugin] = $config['require'] ?: [];

					foreach ($load[$plugin] as $k=>$v)
					{
						// Optional requirements (see #6835)
						if (strncmp($v, '*', 1) === 0)
						{
							$key = substr($v, 1);

							if (!in_array($key, $plugins))
							{
								unset($load[$plugin][$k]);
							}
							else
							{
								$load[$plugin][$k] = $key;
							}
						}
					}
				}
			}

			// Resolve the dependencies
			while (!empty($load))
			{
				$failed = true;

				foreach ($load as $name=>$requires)
				{
					if (empty($requires))
					{
						$resolved = true;
					}
					else
					{
						$resolved = count(array_diff($requires, static::$active)) === 0;
					}

					if ($resolved === true)
					{
						unset($load[$name]);
						static::$active[] = $name;
						$failed = false;
					}
				}

				// The dependencies cannot be resolved
				if ($failed === true)
				{
					ob_start();
					dump($load);
					$buffer = ob_get_contents();
					ob_end_clean();

					throw new \UnresolvableDependenciesException("The plugin dependencies could not be resolved.\n$buffer");
				}
			}
		}
	}
}
