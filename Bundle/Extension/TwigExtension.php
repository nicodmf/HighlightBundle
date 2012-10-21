<?php

namespace Highlight\Bundle\Extension;

use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Component\DependencyInjection\Container;
use Highlight\Bundle\Providers\Providers;

class TwigExtension extends \Twig_Extension {

	public function __construct(TranslatorInterface $translator, \AppKernel $kernel, $configuration)
	{
		$this->params = array('config'=>$configuration->getParameter('highlight'), 'cacheDir'=>$kernel->getCacheDir());
		$this->providers = new Providers(
			$this->params['config'],
			$this->params['cacheDir'],
			$translator
		);
	}
	
	public function getFilters()
	{
		return array(
			'highlight'  => new \Twig_Filter_Method($this, 'highlight', array('is_safe' => array('html'))),
			'hl'  => new \Twig_Filter_Method($this, 'highlight', array('is_safe' => array('html'))),
		);
	}
	
	public function getFunctions()
	{
		return array(
			'highlight'  => new \Twig_Function_Method($this, 'highlight', array('is_safe' => array('html'))),
			'hl'  => new \Twig_Function_Method($this, 'highlight', array('is_safe' => array('html'))),
		);
	}
	
	public function getTokenParsers()
	{
		return array(
			new TokenParser(),
		);
	}
	
	public function highlight($source, $language, $provider=null)
	{
		if($provider!==null)$this->providers->setNamedProvider($provider);
		return $this->providers->getHtml($source, $language);
	}
	
	public function getName()
	{
		return 'twig.extension.highlight';
	}
	
}
