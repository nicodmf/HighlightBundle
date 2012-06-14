<?php

namespace Highlight\Bundle\Providers;
use Symfony\Component\Finder\Finder;

class Pygment extends AbstractProvider implements ProviderInterface
{
	protected static $instance = null;
	protected $highlighter = null;
	protected $path = null;
	protected $langsDir = null;
	protected $cachedLangsFile = null;
	function __construct()
	{
		$this->path = "";
	}
	static function getInstance()
	{
		if(self::$instance===null)
			self::$instance = new Pygment();
		return self::$instance;
	}
	public function getLangs($forceReread = false)
	{
		$closure = function(){
			$langs = preg_split("/\n/",trim(shell_exec("pygmentize -L lexer|tail -n+5|grep -v ^*|awk -F'(' '{print $1}'")));
			foreach($langs as $k=>$v)$langs[$k]=trim($v);
			return $langs;
		};
		return parent::getLangsByClosure($closure, $forceReread);
	}
	public function getCssUrl(){return "bundles/highlight/pygment.css";}

	public function setOptions($opt)
	{
		$options['cssclass'] = $opt['cssclass'];
		$options['blockstyles'] = $opt['blockstyles'];

        if($opt['linenos']==true) {
            $to="linenos=1";
            $options['line'] = "-O ".$to;
        }
        else {
            $options['line'] = "";
        }

		$this->options = $options;
	}
	public function getExtension($language){return trim(shell_exec("pygmentize -L lexer|grep ' $language '|awk -F'filenames ' '{print $2}'|awk -F, '{print $1}'|sed -e 's/*.//g'"));}

	public function getHtml($source, $language, $filename)
	{
		if(!file_exists($filename)) file_put_contents($filename,$source);

        $str = shell_exec("pygmentize ".$this->options['line']." -f html -l '".strtolower($language)."' ".$filename);
        $style = $this->options['blockstyles']!=""?"style=\"".$this->options['blockstyles']:"";
        return "<pre $style class=\"".$this->options['cssclass']." ".$language."\">".preg_replace("//", "", $str)."</pre>";
	}
}
