<?php

namespace Highlight\Bundle\Providers;

/**
 * Abstract provider implemente cache mecanisme
 * and singleton design for provider
 */
abstract class AbstractProvider
{
	protected static $instance = null;
	static function getInstance()
	{
		$class = get_called_class();
		if(self::$instance===null)
			self::$instance = new $class();
		return self::$instance;
	}
	function setOptions($options)
	{
		$this->options = $options;
	}
	function setOption($name, $value)
	{
		$this->options[$name] = $option;
	}
	public function setCachedDir($dir)
	{
		$dir.= strtolower(substr(get_class($this), strrpos(get_class($this),"\\")+1))."/";
		if( ! file_exists($dir) ) mkdir($dir);
		$this->cachedDir = $dir;
		$this->cachedLangsFile = $dir . "languagelist.cached";
	}	
	public function getLangsByClosure($closure, $forceReread = false, $options=null)
	{
		if(file_exists($this->cachedLangsFile) && $forceReread === false)
			$langs = unserialize(file_get_contents($this->cachedLangsFile));
		else
		{
			if($options!==null)$langs = $closure($options);
			else $langs = $closure();
			natsort($langs);
			file_put_contents($this->cachedLangsFile, serialize($langs));
		}
		return $langs;
	}
	function prepareAndGetHtml($source, $language)
	{
		$tohash=$source;
		foreach($this->options as $k=>$v)$tohash.=$k.$v;
		$md5 = md5($tohash.$source.$language);
		$filename = $this->cachedDir.$md5.".".$this->getExtension($language);
		$highfile = $this->cachedDir.$md5.".html";
		
		if(!file_exists($highfile))
		{
			file_put_contents($highfile, $this->getHtml($source, $language, $filename));
		}
		return file_get_contents($highfile);
	}
}
