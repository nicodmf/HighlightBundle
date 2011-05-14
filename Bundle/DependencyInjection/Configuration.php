<?php

namespace Highlight\Bundle\DependencyInjection;

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
class Configuration implements ConfigurationInterface
{
    /**
     * Generates the configuration tree.
     *
     * @return TreeBuilder
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('highlight');

        $rootNode
            ->children()
					->arrayNode('providers')
						 ->performNoDeepMerging()
						 ->addDefaultsIfNotSet()
						 ->prototype('scalar')->end()
						 ->defaultValue(array('geshi','pygment','highlight'))
					->end()
            ->end();
                        
        $this->addGlobalSection($rootNode);
        $this->addGeshiSection($rootNode);
        $this->addHighlightSection($rootNode);
        $this->addPygmentSection($rootNode);
        $this->addHttpAppspotSection($rootNode);
        $this->addHttpHilitemeSection($rootNode);
        
        return $treeBuilder;
    }
    private function addGlobalSection(ArrayNodeDefinition $node)
    {
        $node
            ->children()
               ->arrayNode('globals')->isRequired()->cannotBeEmpty()
						->addDefaultsIfNotSet()
						->children()
                     ->booleanNode('linenos')->defaultFalse()->end()
                     ->scalarNode('cssclass')->defaultValue("highlight")->end()
                     ->scalarNode('blockstyles')->defaultValue("")->end()
                  ->end()						
               ->end()
            ->end();
    }
    private function addGeshiSection(ArrayNodeDefinition $node)
    {
        $node
            ->children()
               ->arrayNode('geshi')->isRequired()->cannotBeEmpty()
						->addDefaultsIfNotSet()
						->children()
                     ->booleanNode('linenos')->defaultTrue()->end()
                     ->scalarNode('linestyle')->defaultValue("fancy")->end()
                     ->scalarNode('cssclass')->defaultValue("geshi")->end()
                  ->end()						
               ->end()
            ->end();
    }
    private function addHighlightSection(ArrayNodeDefinition $node)
    {
        $node
            ->children()
               ->arrayNode('highlight')->isRequired()->cannotBeEmpty()
						->addDefaultsIfNotSet()
						->children()
                     ->booleanNode('linenos')->defaultTrue()->end()
                     ->scalarNode('cssclass')->defaultValue("highlight")->end()
                     ->scalarNode('blockstyles')->defaultValue("")->end()
                  ->end()						
               ->end()
            ->end();
    }
    private function addPygmentSection(ArrayNodeDefinition $node)
    {
        $node
            ->children()
               ->arrayNode('pygment')->isRequired()->cannotBeEmpty()
						->addDefaultsIfNotSet()
						->children()
                     ->booleanNode('linenos')->defaultTrue()->end()
                     ->scalarNode('cssclass')->defaultValue("pygment")->end()
                     ->scalarNode('blockstyles')->defaultValue("")->end()
                  ->end()						
               ->end()
            ->end();
    }
    private function addHttpAppspotSection(ArrayNodeDefinition $node)
    {
        $node
            ->children()
               ->arrayNode('httpappspot')->isRequired()->cannotBeEmpty()
						->addDefaultsIfNotSet()
						->children()
                     ->scalarNode('cssclass')->defaultValue("pygment")->end()
                     ->scalarNode('blockstyles')->defaultValue("")->end()
                  ->end()						
               ->end()
            ->end();
    }
    private function addHttpHilitemeSection(ArrayNodeDefinition $node)
    {
        $node
            ->children()
               ->arrayNode('httphiliteme')->isRequired()->cannotBeEmpty()
						->addDefaultsIfNotSet()
						->children()
                     ->booleanNode('linenos')->defaultTrue()->end()
                     ->scalarNode('style')->defaultValue("native")->end()
                     ->scalarNode('cssclass')->defaultValue("pygment")->end()
                     ->scalarNode('blockstyles')->defaultValue("")->end()
                  ->end()						
               ->end()
            ->end();
    }

 }
