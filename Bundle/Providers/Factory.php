<?php
namespace Highlight\Bundle\Providers;
class Factory{
	static function create($type, $options, $cachedDir)
	{
		switch($type)
		{			
			case "geshi" : $provider = Geshi::getInstance(); break;
			case "highlight" : $provider =  Highlight::getInstance(); break;
			case "pygment" : $provider =  Pygment::getInstance(); break;
			case "httpappspot" : $provider =  HttpAppspot::getInstance(); break;
			case "httphiliteme" : $provider =  HttpHiliteme::getInstance(); break;
			default : throw new \Exception("Highlighter <$type> not implemented");
		}	
		$opt = array();
		$options[$type]['cssclass'] .= " ".$options['globals']['cssclass'];
		foreach($options[$type] as $k=>$v){
			if($v!=null and $v!="")$opt[$k]=$v;
		}
		$opt = array_merge(array_diff($options['globals'], $opt), $opt);
		$provider->setOptions($opt);
		$provider->setCachedDir($cachedDir);
		return $provider;
	}
}
