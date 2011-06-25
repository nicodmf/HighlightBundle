<?php
namespace Highlight\Bundle\DependencyInjection;

use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\Definition\Processor;

class HighlightConfig extends Extension
{
    public function load(array $configs, ContainerBuilder $container) 
    {
		$processor = new Processor();
        $configuration = new Configuration();

        $configuration = $processor->processConfiguration($configuration, $configs);
		$container->setParameter('highlight', $configuration);
	}
    public function getXsdValidationBasePath()
    {
        return __DIR__ . '/../Resources/config/';
    }

    public function getNamespace()
    {
        return 'http://www.example.com/symfony/schema/';
    }
    public function getAlias(){
		 return "highlight";
	 }
}
