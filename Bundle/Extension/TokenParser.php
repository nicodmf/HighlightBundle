<?php

namespace Highlight\Bundle\Extension;

class TokenParser extends \Twig_TokenParser
{
	public function __construct($params){
		$this->params = $params;
	}
    public function parse(\Twig_Token $token)
    {
        $capture = true;
		  $lineno = $token->getLine();
        $stream = $this->parser->getStream();
        $this->params['language'] =  $this->parser->getStream()->next()->getValue();
        $this->params['provider'] = null;
        if ($this->parser->getStream()->test(\Twig_Token::NAME_TYPE)) {
            $this->params['provider'] = $this->parser->getStream()->next()->getValue();
        }
        //$provider = $this->parser->getExpressionParser()->parseAssignmentExpression();

        $stream->expect(\Twig_Token::BLOCK_END_TYPE);
        $source = $this->parser->subparse(array($this, 'decideBlockEnd'), true);
        $stream->expect(\Twig_Token::BLOCK_END_TYPE);

        return new Node($capture, $this->params, $source, $lineno, $this->getTag());
    }
    public function decideBlockEnd(\Twig_Token $token)
    {
        return $token->test('endhighlight');
    }   

    public function getTag()
    {
        return 'highlight';
    }
}
