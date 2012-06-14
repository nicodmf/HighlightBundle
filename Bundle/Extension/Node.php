<?php

namespace Highlight\Bundle\Extension;

class Node extends \Twig_Node
{
    public function __construct($capture, \Twig_NodeInterface $language, \Twig_NodeInterface $provider, \Twig_NodeInterface $source, $lineno, $tag = null)
    {
        parent::__construct(
            array('source'=>$source, 'language'=>$language, 'provider'=>$provider),
            array('capture' => $capture), $lineno, $tag
        );
    }
    public function compile(\Twig_Compiler $compiler)
    {
        $rand = rand().time();
        //print_r($this->getNode('language'));
        $compiler
            ->addDebugInfo($this)
            ->write('ob_start();')
            ->subcompile($this->getNode('source'))
            ->write("\$context['source_$rand'] = new Twig_Markup(ob_get_clean(), \$this->env->getCharset());\n")
            ->write("\$context['language_$rand'] = ")->subcompile($this->getNode('language'))->raw(";\n")
            ->write("\$context['provider_$rand'] = ")->subcompile($this->getNode('provider'))->raw(";\n")
            ->write("echo \$this->env->getExtension('twig.extension.highlight')->highlight(\n")
            ->write("   \$this->getContext(\$context, 'source_$rand'),\n")
            ->write("   \$this->getContext(\$context, 'language_$rand'),\n")
            ->write("   \$this->getContext(\$context, 'provider_$rand') \n")
            ->write(");\n");
        ;/**/
    }
}
