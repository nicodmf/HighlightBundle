<?php

namespace Highlight\Bundle\Providers;
use Symfony\Component\Finder\Finder;

class Highlight extends AbstractProvider implements ProviderInterface
{
	protected static $instance = null;
	protected $highlighter = null;
	protected $path = null;
	protected $langsDir = null;
	protected $cachedLangsFile = null;
	protected $optionsTranslated = false;
	function __construct()
	{
		$this->path = "";
	}
	public function getLangs($forceReread = false)
	{
		$closure = function(){
			return preg_split("/;/",shell_exec('for i in '.
					"`highlight -p|awk -F: '{print $2}'| sed -e 's/(\|)//g'`".
					' ; do echo -n "$i;"; done'));
		};
		return parent::getLangsByClosure($closure, $forceReread);
	}
	function setOptions($options)
	{
		$opt = $options;
		$this->options['blockstyles'] = $opt['blockstyles'];
		$this->options['cssclass'] = $opt['cssclass'];
		$options = "";
		if($opt['linenos']==true){
			$options.=" -n ";
			$this->options['stripn'] = true;
		}
		$this->options['line'] = $options;
	}

	public function getCssUrl(){return "bundles/highlight/highlight.css";}

	public function getExtension($language){return $language;}
	
	public function getHtml($source, $language, $filename)
	{
		if(!file_exists($filename)) file_put_contents($filename,$source);
		$str = shell_exec("highlight ".$this->options['line']." --class-name='hl' -f -S $language -q -i $filename");
		$style = $this->options['blockstyles']!=""?"style=\"".$this->options['blockstyles']:"";
		if(isset($this->options['stripn']) and $this->options['stripn']===true)
			return "<pre $style class=\"".$this->options['cssclass']." ".$language."\">".preg_replace("/\n/", "", $str)."</pre>";
		else
			return "<pre $style class=\"".$this->options['cssclass']."\">".$str."</pre>";
	}
	
}
