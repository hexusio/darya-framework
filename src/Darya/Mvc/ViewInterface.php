<?php
namespace Darya\Mvc;

/**
 * Darya's interface for views.
 * 
 * @author Chris Andrew <chris@hexus.io>
 */
interface ViewInterface {
	
	/**
	 * Select a template and optionally assign variables and configuration.
	 * 
	 * @param string $file 	 The template file to be used
	 * @param array  $vars 	 [optional] Variables to assign to the template immediately
	 * @param array  $config [optional] Config variables for the view
	 */
	public function select($file, $vars = array(), $config = array());
	
	/**
	 * Get view configuration variables.
	 * 
	 * @return array
	 */
	public function getConfig();
	
	/**
	 * Set view configuration variables. This merges with any previously set.
	 * 
	 * @param array $config
	 */
	public function setConfig(array $config);
	
	/**
	 * Assign an array of key/value pairs to the template.
	 * 
	 * @param array $vars
	 */
	public function assign(array $vars);
	
	/**
	 * Get all variables or a particular variable assigned to the template.
	 * 
	 * @param string $key [optional] Key of the variable to return
	 * @return mixed The value of variable $key if set, all variables otherwise
	 */
	public function getAssigned($key = null);
	
	/**
	 * Render the view.
	 * 
	 * @return string The rendered view
	 */
	public function render();
	
}