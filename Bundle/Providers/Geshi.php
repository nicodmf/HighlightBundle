<?php

namespace Highlight\Bundle\Providers;

use Symfony\Component\Finder\Finder;

class Geshi extends AbstractProvider implements ProviderInterface
{
	protected static $instance = null;
	protected $highlighter = null;
	protected $path = null;
	protected $langsDir = null;
	protected $cachedLangsFile = null;
	function __construct()
	{
		$this->path = __DIR__ . "/../Vendors/geshi/";
		$this->langsDir = __DIR__ . "/../Vendors/geshi/geshi/";
	}
	function setHighlighter(){
		@require_once($this->path."geshi.php");
		$this->highlighter = new \GeSHi();
	}
	public function applyOptions($language){
		$opt = $this->options;
		$hl = $this->highlighter;

		//Flags : \GESHI_NORMAL_LINE_NUMBERS, \GESHI_FANCY_LINE_NUMBERS, \GESHI_NO_LINE_NUMBERS);
		if($opt['linestyle']=='fancy')$ls=\GESHI_FANCY_LINE_NUMBERS;
		else $ls=\GESHI_NORMAL_LINE_NUMBERS;
		if($opt['linenos']==true) $hl->enable_line_numbers($ls);
		else $hl->enable_line_numbers(\GESHI_NO_LINE_NUMBERS);
		if(isset($opt['blockstyles'])) $hl->set_overall_style($opt['blockstyles']);
		$hl->set_overall_class($opt['cssclass']." $language");
		$hl->enable_keyword_links($opt['enable_links']);
	}
	static public function getHighlighter()
	{
		$instance = self::getInstance();
		if($instance->highlighter === null ) $instance->setHighlighter();
		return $instance->highlighter;
	}
	static function getInstance($startHighlight = false)
	{
		if(self::$instance===null)
			self::$instance = new Geshi();
			if($startHighlight) self::$instance->setHighlighter();
		return self::$instance;
	}
	public function getLangs($forceReread = false)
	{
		$closure = function($options){
			$finder = new Finder();
			$finder->files()->name("*.php")->in($options);
			foreach ($finder as $file) {
				$langs[] = preg_replace("/\.php/", "", $file->getFilename());
			}
			return $langs;			
		};
		return parent::getLangsByClosure($closure, $forceReread, $this->langsDir);
	}
	public function getCssUrl(){return "bundles/highlight/geshi.css";}

	public function getExtension($language){return $language;}
	
	public function getHtml($source, $language, $filename)
	{
		$geshi = self::getInstance(true);
		$geshi->applyOptions($language);	 
		$geshi->highlighter->set_source($source);
		$geshi->highlighter->set_language($language);
		$geshi->highlighter->enable_classes(true);
		return $geshi->highlighter->parse_code();
	}
}
