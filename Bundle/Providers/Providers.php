<?php
namespace Highlight\Bundle\Providers;
class Providers{
	private $namedProvidersList = array();
	private $providersList = array();
	private $actual = 0;
	private $choiced;
	private $provider = null;
	private $cachedDir;
	private $translator;
	private $vendorDir;
	public function __construct($configuration, $cachedDir, $translator=null)
	{
		$this->namedProvidersList = $configuration['providers'];
		$this->options = $configuration;
		$this->cachedDir = $cachedDir."/highlight/";
		if(!file_exists($this->cachedDir))mkdir($this->cachedDir);
		$this->translator = $translator;
	}
	public function getHtml($source, $language)
	{
		if($this->provider===null)$this->setProvider(0);
		return $this->realGetHtml($source, $language);
	}

	public function realGetHtml($source, $language)
	{		
		if( false === $this->arraySearchWithoutCase($language, $this->provider->getLangs()))
			$this->findProvider($language);
		$filename = $this->cachedDir.md5($this->providerName.serialize($this->options).serialize($this->provider).$source.$language);

		//echo "\n".$this->providerName."\n".$filename;
		
		if(!file_exists($filename))
		{
			file_put_contents($filename,$this->provider->prepareAndGetHtml($source, $language));
		}
		return file_get_contents($filename);
	}
	public function getCssUrl()
	{
		return $this->provider->getCssUrl();
	}
	public function setNamedProvider($providerName)
	{
		//echo "aaa".$providerName;
		$p = $this->provider;
		try{
			$this->provider = Factory::create($providerName, $this->options, $this->cachedDir);
			$this->providerName = $providerName;
		} catch (\Exception $e){
			throw $e;
			$this->provider = $p;
		}
	}
	private function arraySearchWithoutCase($needle, $haystack)
	{
		if(array_search($needle, $haystack))return true;
		array_walk($haystack,function(&$e,$k){$e=strtolower($e);});
		return array_search(strtolower($needle), $haystack);
	}
	private function setProvider($pos)
	{
		$this->providerName = $this->namedProvidersList[$pos];
		if(isset($this->providersList[$pos]))
		{
			$this->provider = $this->providersList[$pos];
			return;
		}
		$this->provider = Factory::create($this->namedProvidersList[$pos], $this->options, $this->cachedDir);
		$this->providersList[$pos] = $this->provider;

	}
	private function findProvider($language)
	{
		for($i=$this->actual+1; $i<count($this->namedProvidersList); $i++)
		{
			$this->setProvider($i);
			if( false !== array_search($language, $this->provider->getLangs()) ){
				$actual = $i;
				return "";
			}
		}
		$this->setProvider(0);
		return $this->translate("No provider can highlight this language");
	}
	private function translate($str)
	{
		if($this->transaltor===null)return $str;
		return $this->transaltor->trans($str);
	}
}
