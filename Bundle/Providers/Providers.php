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
	private function arraySearchWithoutCase($needle, $haystack){
		if(array_search($needle, $haystack))return true;
		array_walk($haystack,function(&$e,$k){$e=strtolower($e);});
		return array_search(strtolower($needle), $haystack);
	}
	public function getHtml($source, $language)
	{
		$filename = $this->cachedDir.md5(serialize($this->options).$source.$language);
		if(!file_exists($filename))
		{
			$this->setProvider(0);
			file_put_contents($filename,$this->realGetHtml($source, $language));
		}
		return file_get_contents($filename);
	}
	public function realGetHtml($source, $language)
	{
		if( false === $this->arraySearchWithoutCase($language, $this->provider->getLangs()))
			$this->findProvider($language);
		return $this->provider->prepareAndGetHtml($source, $language);
	}
	public function getCssUrl()
	{
		return $this->provider->getCssUrl();
	}
	public function setNamedProvider($providerName)
	{
		$p = $this->provider;
		try{
			$this->provider = Factory::create($providerName, $this->options, $this->cachedDir);
		} catch (\Exception $e){
			echo $e;
			$this->provider = $p;
		}
	}
	private function setProvider($pos)
	{
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
