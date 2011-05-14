<?php

namespace Highlight\Bundle\Controller;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This class contains the configuration information for the bundle
 *
 * This information is solely responsible for how the different configuration
 * sections are normalized, and merged.
 *
 * @author Ndm 
 */
class ApiConfiguration implements ConfigurationInterface
{
    /**
     * Generates the configuration tree.
     *
     * @return TreeBuilder
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('vars');

        $rootNode
				->addDefaultsIfNotSet()
            ->children()
					->scalarNode('language')->isRequired()->cannotBeEmpty()
					->end()
					->scalarNode('source')->isRequired()->cannotBeEmpty()
					->end()
					->scalarNode('provider')->defaultValue('pygment')
					->end()
            ->end();

        return $treeBuilder;            
    }
 }
