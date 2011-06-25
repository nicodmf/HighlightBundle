<?php

namespace Highlight\Bundle\Extension;

class TokenParser extends \Twig_TokenParser
{
    public function parse(\Twig_Token $token)
    {
        $capture = true;
		$lineno = $token->getLine();
        $stream = $this->parser->getStream();
        
        if( ! $stream->test(\Twig_Token::BLOCK_END_TYPE) ){
            $language = $this->parser->getExpressionParser()->parseExpression();
        }
        
        if( ! $stream->test(\Twig_Token::BLOCK_END_TYPE) ){
            $provider = $this->parser->getExpressionParser()->parseExpression();
        }

        if( ! isset($language) ) throw new \Exception("Language must be defined at line $lineno.");
        if( ! isset($provider) ) $provider = new \Twig_Node_Expression_Constant(null, $lineno);

        $stream->expect(\Twig_Token::BLOCK_END_TYPE);
        $source = $this->parser->subparse(array($this, 'decideBlockEnd'), true);
        $stream->expect(\Twig_Token::BLOCK_END_TYPE);

        return new Node($capture, $language, $provider, $source, $lineno, $this->getTag());
    }
    public function decideBlockEnd(\Twig_Token $token)
    {
        return $token->test('end'.$this->getTag());
    }   

    public function getTag()
    {
        return 'highlight';
    }
}
