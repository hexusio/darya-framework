<?php
namespace Darya\Common;

/**
 * Darya's class autoloader.
 * 
 * TODO: Simplify Autoloader::load.
 * 
 * @author Chris Andrew <chris@hexus.io>
 */
class Autoloader {
	
	/**
	 * @var array Common subdirectories used as a last resort when autoloading
	 */
	private $commonSubdirs = array('Common', 'Classes', 'Controllers', 'Models', 'Tests');
	
	/**
	 * @var array Map of namespaces to paths to use when autoloading
	 */
	private $registeredNamespaces = array();
	
	/**
	 * @var string Base path to use when autoloading
	 */
	private $basePath;
	
	/**
	 * Instantiate an autoloader.
	 * 
	 * @param string $basePath   Base directory path to load from
	 * @param array  $namespaces Namespace to directory mappings to register with the autoloader
	 */
	public function __construct($basePath = null, array $namespaces = array()) {
		$this->setBasePath($basePath);
		$this->registerNamespaces($namespaces);
	}
	
	/**
	 * Return the base name of a namespaced class.
	 * 
	 * @param string $class Fully qualified (namespaced) class name
	 * @return string
	 */
	public static function classBaseName($class) {
		return basename(str_replace('\\', '/', $class));
	}
	
	/**
	 * Set the base directory to load from.
	 * 
	 * @param string $basePath
	 */
	public function setBasePath($basePath = null) {
		$this->basePath = $basePath ?: realpath(__DIR__.'/../../');
	}
	
	/**
	 * Get the base directory to load from.
	 * 
	 * @return string
	 */
	public function getBasePath() {
		return $this->basePath;
	}
	
	/**
	 * Register this autoloader.
	 * 
	 * @return bool
	 */
	public function register() {
		return spl_autoload_register(array($this, 'load'));
	}
	
	/**
	 * Register namespace to directory mappings to attempt before the
	 * autoloader's default behaviour.
	 * 
	 * @param array $namespaces Namespace keys and directory values
	 */
	public function registerNamespaces(array $namespaces = array()) {
		foreach ($namespaces as $ns => $paths) {
			foreach ((array) $paths as $path) {
				$this->registeredNamespaces[] = array($ns, $path);
			}
		}
	}

	/**
	 * Attempt to load the class at the given path.
	 * 
	 * @param string $path
	 * @return bool
	 */
	public function attempt($path) {
		if (is_file($path)) {
			require_once $path;
			return true;
		}
		
		return false;
	}

	/**
	 * Load a class assuming the namespace is a path.
	 * 
	 * Checks common subdirectory names as a last resort if nothing is found.
	 * 
	 * @param string $class Class name
	 * @return bool
	 */
	public function load($class) {
		// Separate the class name and its namespace
		$parts = explode('\\', $class);
		$className = array_pop($parts);
		$dir = implode('/', $parts);
		$paths = array();
		
		// Test for potential registered namespace to directory mappings
		foreach ($this->registeredNamespaces as $registered) {
			list($ns, $nsPaths) = $registered;
			
			foreach ((array) $nsPaths as $nsPath) {
				// Try without and with the autoloader's base path
				$nsBasePaths = array('');
				
				if ($this->basePath) {
					$nsBasePaths[] = $this->basePath . '/';
				}
				
				foreach ($nsBasePaths as $nsBasePath) {
					if ($class == $ns) {
						array_push($paths, "$nsBasePath$nsPath");
						array_push($paths, "$nsBasePath$nsPath/$className.php");
						array_push($paths, "$nsBasePath" . strtolower($nsPath) . "/$className.php");
					}
					
					if(strpos($class, $ns) == 0){
						array_push($paths, "$nsBasePath$nsPath/$dir/$className.php");
						array_push($paths, "$nsBasePath" . strtolower("$nsPath/$dir") . "/$className.php");
						
						$nsRemain = str_replace('\\', '/', substr($class, strlen($ns)));
						array_push($paths, "$nsBasePath$nsPath/$nsRemain.php");
						array_push($paths, "$nsPath/$nsRemain.php");
					}
				}
			}
		}
		
		// Try using the namespace as an exact directory mapping
		array_push($paths, $this->basePath . "$dir/$className.php");
		
		// Try using the namespace in lowercase as a directory mapping, with
		// only the class name in its original case
		$dirLowercase = strtolower($dir);
		array_push($paths, $this->basePath . "$dirLowercase/$className.php");
		
		// Last try using the last part of the namespace as a subdirectory, with
		// and without a trailing 's', as well as any common subdirectory names
		$subdirs = array_merge($this->commonSubdirs, array(
			$className,
			$className . 's',
		));
		
		foreach($subdirs as $subdir) {
			array_push($paths, $this->basePath . "/$dir/$subdir/$className.php");
			
			$subdirLowercase = strtolower($subdir);
			array_push($paths, $this->basePath . "/$dirLowercase/$subdirLowercase/$className.php");
		}
		
		// Finally, attempt to find the class
		foreach ($paths as $path) {
			if ($this->attempt($path)) {
				return true;
			}
		}
		
		return false;
	}
	
}
