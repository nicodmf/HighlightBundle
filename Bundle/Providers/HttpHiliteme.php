<?php

namespace Highlight\Bundle\Providers;
use Symfony\Component\Finder\Finder;

class HttpHiliteme extends AbstractProvider implements ProviderInterface
{
	protected static $instance = null;
	protected $highlighter = null;
	protected $path = null;
	protected $langsDir = null;
	protected $cachedLangsFile = null;
	function __construct()
	{
		$this->path = __DIR__ . "/../Vendors/pygmenthttp/";
	}
	public function getLangs($forceReread = false)
	{
		$closure = function($options){return unserialize(file_get_contents($options));};
		return parent::getLangsByClosure($closure, $forceReread, $this->path."languagelist.serialysed");
	}
	public function getCssUrl(){;}

	public function setOptions($opt)
	{
		$options['cssclass']= $opt['cssclass']." ".$opt['style']." hili";
		$options['linenos'] = $opt['linenos']===true?true:null;
		$options['style'] = $opt['style'];
		$options['blockstyles'] = $opt['blockstyles'];

		$this->options = $options;
	}
	public function getExtension($language){return trim(shell_exec("cat ".$this->path."languagelist.txt|grep ' $language '|awk -F'filenames ' '{print $2}'|awk -F, '{print $1}'|sed -e 's/*.//g'"));}
	public function getHtml($source, $language, $filename)
	{
		//echo "hiliteme";
		$postdata = http_build_query(array(
			'lexer'=>strtolower($language),
			'code'=>preg_replace('/\n/', "\r", $source),
			'style'=>$this->options['style'],
			//'divstyles'=>$this->options['divstyles'],
			'linenos'=>$this->options['linenos']
			));
		$opts = array('http' =>
			 array(
				  'method'  => 'POST',
				  'header'  => 'Content-type: application/x-www-form-urlencoded',
				  'content' => $postdata
			 )
		);
		$context  = stream_context_create($opts);

		$str = file_get_contents('http://www.hilite.me/api', false, $context);
		$str = substr($str, 0, strlen($str)-1);
		$style = $this->options['blockstyles']!=""?"style=\"".$this->options['blockstyles']."\"":"";
		return "<div $style class=\"".$this->options['cssclass']." ".$language."\">".$str."</div>";
	}
}
