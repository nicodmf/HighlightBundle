<?php

namespace Highlight\Bundle\Extension;

class Node extends \Twig_Node
{
    public function __construct($capture, $params, \Twig_NodeInterface $source, $lineno, $tag = null)
    {
		 $this->params = $params;
        parent::__construct(array('source'=>$source), array('capture' => $capture), $lineno, $tag);
    }
    public function compile(\Twig_Compiler $compiler)
    {
		 /*
        $source = $this->getNode('source')->getAttribute('data');
        $providers = new \Highlight\Bundle\Providers\Providers($this->params["config"], $this->params["cacheDir"]);
        if($this->params["provider"]!=="")$providers->setNamedProvider($this->params["provider"]);
        $compiler->write('echo stripslashes(\''.addslashes($providers->getHtml("$source", $this->params["language"])).'\');');
        /* */$compiler
            ->addDebugInfo($this)
            ->write('ob_start();')
            ->subcompile($this->getNode('source'))
            ->write('$source = new Twig_Markup(ob_get_clean());'."\n")
            ->write('$providers = new \Highlight\Bundle\Providers\Providers(unserialize(stripslashes("'.addslashes(serialize($this->params["config"])).'")),"'.$this->params["cacheDir"].'");'."\n")
            ->write('if("'.$this->params["provider"].'"!=="")$providers->setNamedProvider("'.$this->params["provider"].'");'."\n")
				->write('echo $providers->getHtml("$source", "'.$this->params["language"].'");'."\n")
        ;/**/
    }
}
