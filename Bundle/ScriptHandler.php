<?php

namespace Highlight\Bundle;

use Highlight\Bundle\DependencyInjection\HighlightConfig;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Output\StreamOutput;
use Symfony\Bundle\FrameworkBundle\Console\Application;

class ScriptHandler extends \Sensio\Bundle\GeneratorBundle\Command\GenerateBundleCommand
{
	static public function install($evt){
		$handler = new ScriptHandler();
		$handler->doUpdate();
	}
	public function doUpdate(){
		$args = preg_split("/ /", trim(""));
		array_unshift($args, "fakecommandline"); //To simulate the console's arguments 
		$app = $args[1];
		$input = new ArgvInput($args);

		//Prepare output
		$this->cacheDir = $this->container->get('kernel')->getCacheDir() . DIRECTORY_SEPARATOR . 'sf2genconsole' . DIRECTORY_SEPARATOR;
		if(file_exists($this->filename))
			unlink($this->filename);
		$this->filename = $filename = "{$this->cacheDir}".time()."_commands";
		$output = new StreamOutput(fopen($filename, 'w+'), StreamOutput::VERBOSITY_NORMAL, true, new OutputFormatterHtml());

		//Start a kernel/console and an application
		$env = $input->getParameterOption(array('--env', '-e'), 'dev');
		$debug = !$input->hasParameterOption(array('--no-debug', ''));
		$kernel = new \AppKernel($env, $debug);
		$kernel->boot();
		$application = new Application($kernel);
		
		$namespace = "Highlight\Bundle";
		$bundle = "HighlightBundle";
		$format = "yml";	
		
		$this->updateKernel($dialog, $input, $output, $kernel, $namespace, $bundle);
		$this->updateRouting($dialog, $input, $output, $bundle, $format);
		//foreach ($kernel->getBundles() as $bundle)
		 //   $bundle->registerCommands($application); //integrate all availables commands

		//Find, initialize and run the real command
		//$run = $application->find($app)->run($input, $output);

		//$output = file_get_contents($filename);
	}
	
	protected function updateKernel($dialog, \Symfony\Component\Console\Input\InputInterface $input, \Symfony\Component\Console\Output\OutputInterface $output, \Symfony\Component\HttpKernel\KernelInterface $kernel, $namespace, $bundle) {
		parent::updateKernel($dialog, $input, $output, $kernel, $namespace, $bundle);
	}
	protected function updateRouting($dialog, InputInterface $input, OutputInterface $output, $bundle, $format) {
		parent::updateRouting($dialog, $input, $output, $bundle, $format);
	}
}

use Symfony\Component\Console\Formatter\OutputFormatterStyleInterface;

/**
 * Formatter style class for defining styles.
 *
 * @author ndmf
 * @author Konstantin Kudryashov <ever.zet@gmail.com>
 *
 * @api
 */
class OutputFormatterStyleHtml implements OutputFormatterStyleInterface
{
	static protected $availableForegroundColors = array(
		'black'	 => 'color:black',
		'red'	   => 'color:red',
		'green'	 => 'color:#3C3',
		'yellow'	=> 'color:yellow',
		'blue'	  => 'color:blue',
		'magenta'   => 'color:magenta',
		'cyan'	  => 'color:cyan',
		'white'	 => 'color:white'
	);
	static protected $availableBackgroundColors = array(
		'black'	 => 'background-color:black',
		'red'	   => 'background-color:red',
		'green'	 => 'background-color:green',
		'yellow'	=> 'background-color:yellow',
		'blue'	  => 'background-color:blue',
		'magenta'   => 'background-color:magenta',
		'cyan'	  => 'background-color:cyan',
		'white'	 => 'background-color:white'
	);
	static protected $availableOptions = array(
		'bold'		  => 'font-weight:bold',
		'underscore'	=> 'text-decoration:underscore',
		'blink'		 => 'text-decoration: blink',
		//'reverse'	   => 7,
		//'conceal'	   => 8
	);
	protected $foreground;
	protected $background;
	protected $options = array();

	/**
	 * Initializes output formatter style.
	 *
	 * @param   string  $foreground	 style foreground color name
	 * @param   string  $background	 style background color name
	 * @param   array   $options		style options
	 *
	 * @api
	 */
	public function __construct($foreground = null, $background = null, array $options = array())
	{
		if (null !== $foreground) {
			$this->setForeground($foreground);
		}
		if (null !== $background) {
			$this->setBackground($background);
		}
		if (count($options)) {
			$this->setOptions($options);
		}
	}

	/**
	 * Sets style foreground color.
	 *
	 * @param   string  $color  color name
	 *
	 * @api
	 */
	public function setForeground($color = null)
	{
		$class = get_called_class();
		if (null === $color) {
			$this->foreground = null;

			return;
		}

		if (!isset($class::$availableForegroundColors[$color])) {
			throw new \InvalidArgumentException(sprintf(
				'Invalid foreground color specified: "%s". Expected one of (%s)',
				$color,
				implode(', ', array_keys($class::$availableForegroundColors))
			));
		}

		$this->foreground = $class::$availableForegroundColors[$color];
	}

	/**
	 * Sets style background color.
	 *
	 * @param   string  $color  color name
	 *
	 * @api
	 */
	public function setBackground($color = null)
	{
		$class = get_called_class();
		if (null === $color) {
			$this->background = null;

			return;
		}

		if (!isset($class::$availableBackgroundColors[$color])) {
			throw new \InvalidArgumentException(sprintf(
				'Invalid background color specified: "%s". Expected one of (%s)',
				$color,
				implode(', ', array_keys($class::$availableBackgroundColors))
			));
		}

		$this->background = $class::$availableBackgroundColors[$color];
	}

	/**
	 * Sets some specific style option.
	 *
	 * @param   string  $option	 option name
	 *
	 * @api
	 */
	public function setOption($option)
	{
		$class = get_called_class();
		if (!isset($class::$availableOptions[$option])) {
			throw new \InvalidArgumentException(sprintf(
				'Invalid option specified: "%s". Expected one of (%s)',
				$option,
				implode(', ', array_keys($class::$availableOptions))
			));
		}

		if (false === array_search($class::$availableOptions[$option], $this->options)) {
			$this->options[] = $class::$availableOptions[$option];
		}
	}

	/**
	 * Unsets some specific style option.
	 *
	 * @param   string  $option	 option name
	 */
	public function unsetOption($option)
	{
		if (!isset($class::$availableOptions[$option])) {
			throw new \InvalidArgumentException(sprintf(
				'Invalid option specified: "%s". Expected one of (%s)',
				$option,
				implode(', ', array_keys($class::$availableOptions))
			));
		}

		$pos = array_search($class::$availableOptions[$option], $this->options);
		if (false !== $pos) {
			unset($this->options[$pos]);
		}
	}

	/**
	 * Set multiple style options at once.
	 *
	 * @param   array   $options
	 */
	public function setOptions(array $options)
	{
		$this->options = array();

		foreach ($options as $option) {
			$this->setOption($option);
		}
	}


	/**
	 * Applies the style to a given text.
	 *
	 * @param string $text The text to style
	 *
	 * @return string
	 */
	public function apply($text)
	{
		$codes = array();
		if (null !== $this->foreground) {
			$codes[] = $this->foreground;
		}
		if (null !== $this->background) {
			$codes[] = $this->background;
		}
		if (count($this->options)) {
			$codes = array_merge($codes, $this->options);
		}

		return sprintf("<span style=\"%s\">%s</span>", implode(';', $codes), $text);
	}
}

use Symfony\Component\Console\Formatter\OutputFormatterStyleInterface;

/**
 * Formatter style class for defining styles.
 *
 * @author ndmf
 * @author Konstantin Kudryashov <ever.zet@gmail.com>
 *
 * @api
 */
class OutputFormatterStyleHtml implements OutputFormatterStyleInterface
{
	static protected $availableForegroundColors = array(
		'black'	 => 'color:black',
		'red'	   => 'color:red',
		'green'	 => 'color:#3C3',
		'yellow'	=> 'color:yellow',
		'blue'	  => 'color:blue',
		'magenta'   => 'color:magenta',
		'cyan'	  => 'color:cyan',
		'white'	 => 'color:white'
	);
	static protected $availableBackgroundColors = array(
		'black'	 => 'background-color:black',
		'red'	   => 'background-color:red',
		'green'	 => 'background-color:green',
		'yellow'	=> 'background-color:yellow',
		'blue'	  => 'background-color:blue',
		'magenta'   => 'background-color:magenta',
		'cyan'	  => 'background-color:cyan',
		'white'	 => 'background-color:white'
	);
	static protected $availableOptions = array(
		'bold'		  => 'font-weight:bold',
		'underscore'	=> 'text-decoration:underscore',
		'blink'		 => 'text-decoration: blink',
		//'reverse'	   => 7,
		//'conceal'	   => 8
	);
	protected $foreground;
	protected $background;
	protected $options = array();

	/**
	 * Initializes output formatter style.
	 *
	 * @param   string  $foreground	 style foreground color name
	 * @param   string  $background	 style background color name
	 * @param   array   $options		style options
	 *
	 * @api
	 */
	public function __construct($foreground = null, $background = null, array $options = array())
	{
		if (null !== $foreground) {
			$this->setForeground($foreground);
		}
		if (null !== $background) {
			$this->setBackground($background);
		}
		if (count($options)) {
			$this->setOptions($options);
		}
	}

	/**
	 * Sets style foreground color.
	 *
	 * @param   string  $color  color name
	 *
	 * @api
	 */
	public function setForeground($color = null)
	{
		$class = get_called_class();
		if (null === $color) {
			$this->foreground = null;

			return;
		}

		if (!isset($class::$availableForegroundColors[$color])) {
			throw new \InvalidArgumentException(sprintf(
				'Invalid foreground color specified: "%s". Expected one of (%s)',
				$color,
				implode(', ', array_keys($class::$availableForegroundColors))
			));
		}

		$this->foreground = $class::$availableForegroundColors[$color];
	}

	/**
	 * Sets style background color.
	 *
	 * @param   string  $color  color name
	 *
	 * @api
	 */
	public function setBackground($color = null)
	{
		$class = get_called_class();
		if (null === $color) {
			$this->background = null;

			return;
		}

		if (!isset($class::$availableBackgroundColors[$color])) {
			throw new \InvalidArgumentException(sprintf(
				'Invalid background color specified: "%s". Expected one of (%s)',
				$color,
				implode(', ', array_keys($class::$availableBackgroundColors))
			));
		}

		$this->background = $class::$availableBackgroundColors[$color];
	}

	/**
	 * Sets some specific style option.
	 *
	 * @param   string  $option	 option name
	 *
	 * @api
	 */
	public function setOption($option)
	{
		$class = get_called_class();
		if (!isset($class::$availableOptions[$option])) {
			throw new \InvalidArgumentException(sprintf(
				'Invalid option specified: "%s". Expected one of (%s)',
				$option,
				implode(', ', array_keys($class::$availableOptions))
			));
		}

		if (false === array_search($class::$availableOptions[$option], $this->options)) {
			$this->options[] = $class::$availableOptions[$option];
		}
	}

	/**
	 * Unsets some specific style option.
	 *
	 * @param   string  $option	 option name
	 */
	public function unsetOption($option)
	{
		if (!isset($class::$availableOptions[$option])) {
			throw new \InvalidArgumentException(sprintf(
				'Invalid option specified: "%s". Expected one of (%s)',
				$option,
				implode(', ', array_keys($class::$availableOptions))
			));
		}

		$pos = array_search($class::$availableOptions[$option], $this->options);
		if (false !== $pos) {
			unset($this->options[$pos]);
		}
	}

	/**
	 * Set multiple style options at once.
	 *
	 * @param   array   $options
	 */
	public function setOptions(array $options)
	{
		$this->options = array();

		foreach ($options as $option) {
			$this->setOption($option);
		}
	}


	/**
	 * Applies the style to a given text.
	 *
	 * @param string $text The text to style
	 *
	 * @return string
	 */
	public function apply($text)
	{
		$codes = array();
		if (null !== $this->foreground) {
			$codes[] = $this->foreground;
		}
		if (null !== $this->background) {
			$codes[] = $this->background;
		}
		if (count($this->options)) {
			$codes = array_merge($codes, $this->options);
		}

		return sprintf("<span style=\"%s\">%s</span>", implode(';', $codes), $text);
	}
}
