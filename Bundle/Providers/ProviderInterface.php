<?php


namespace Highlight\Bundle\Providers;
use Symfony\Component\HttpKernel\Bundle\Bundle;
/*
 * A provider can extend abstract provider
 * an abstract class wich provide cache mecanismes
 * and simplification 
 */
interface ProviderInterface
{
	/*
	 * function can implemente cached mecanisme with the function
	 * getLangsByClosure presents in the abstract provider
	 */
	function getLangs();
	function getCssUrl();
	function getHtml($code, $lang, $filename);
	function getExtension($lang);
	function setCachedDir($dir);
}
