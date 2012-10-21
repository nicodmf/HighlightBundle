<?php

namespace Highlight\Bundle;

use Highlight\Bundle\DependencyInjection\HighlightConfig;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class HighlightBundle extends Bundle
{
	public function build(ContainerBuilder $container)
	{
		// register the extension(s) found in DependencyInjection/ directory
		parent::build($container);

		// register extensions that do not follow the conventions manually
		$container->registerExtension(new HighlightConfig());
	}
}
