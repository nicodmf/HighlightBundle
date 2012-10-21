<?php

namespace Highlight\Bundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Config\Definition\Processor;
use Highlight\Bundle\Post;
use Highlight\Bundle\Providers\Providers;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

class DefaultController extends Controller
{
	/**
	 * @Route("api")
	 */
	public function apiAction()
	{
		$configurations['vars'] = array_merge(
		$this->container->get('request')->request->all(),
		$this->container->get('request')->query->all()
		);
		try{
			$processor = new Processor();
			$configuration = new ApiConfiguration();
			$configuration = $processor->processConfiguration($configuration, $configurations);
			$this->providers = new Providers(
				$this->container->get('service_container')->getParameter('highlight'),
				$this->container->get('kernel')->getCacheDir(),
				$this->container->get('translator')
			);
			if($configuration['provider']!==null&&$configuration['provider']!=="")
				$this->providers->setNamedProvider($configuration['provider']);
			$content = $this->providers->getHtml($configuration['source'], $configuration['language']);
			return $this->render('HighlightBundle::default.empty.twig',array('content'=>$content));	   
		}catch(\Exception $e){
			return $this->render('HighlightBundle::default.html.twig',array('page'=>'api'));
		}
	}
	/**
	 * @Route("/")
	 * @Template("HighlightBundle::default.html.twig")
	 */
	public function indexAction()
	{
		 return array();
	}
	/**
	 * @Route("/test")
	 * @Template("HighlightBundle::default.html.twig")
	 */
	public function testAction()
	{
		 return array('page'=>'test');
	}
	/**
	 * @Route("/test/api")
	 * @Template("HighlightBundle::default.html.twig")
	 */
	public function testApiAction()
	{
		$code="
\$postdata = http_build_query(array(
	'language'=>'php',
	'source'=>'<?php echo \"hello world\";?>',
	'provider'=>'geshi',
	//'divstyles'=>\$this->options['cssclass'],
	//'linenos'=>\$this->options['linenos']
	));
\$opts = array('http' =>
	array(
		'method'  => 'POST',
		'header'  => 'Content-type: application/x-www-form-urlencoded',
		'content' => \$postdata
	)
);
\$context  = stream_context_create(\$opts);
\$result = file_get_contents('http://'.\$_SERVER['SERVER_NAME'].'".$this->get('router')->generate('highlight__default_api')."', false, \$context);";
		eval($code);
		$htmlCode = $this->container->get('highlight.twig.extension')->highlight("<?php\n".$code, 'php');
		return array('content'=>"Le résultat de <br>$htmlCode</pre> est $result");
	}
}
