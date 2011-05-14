<?php

namespace Highlight\Bundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Config\Definition\Processor;
use Highlight\Bundle\Post;
use Highlight\Bundle\Providers\Providers;

class DefaultController extends Controller
{
	/**
	 * @extra:Route("api")
	 * @extra:Template("HighlightBundle::default.empty.twig")
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
			return $this->render('HighlightBundle::api.html.twig');
		}
    }
	/**
	 * @extra:Route("test")
	 * @extra:Template("HighlightBundle::index.html.twig")
	 */
    public function testAction()
    {
		 return array();
    }
	/**
	 * @extra:Route("testapi")
	 * @extra:Template("HighlightBundle::default.html.twig")
	 */
    public function testapiAction()
    {
		$postdata = http_build_query(array(
			'language'=>'php',
			'source'=>'<?php echo "hello world";?>',
			'provider'=>'geshi',
			//'divstyles'=>$this->options['cssclass'],
			//'linenos'=>$this->options['linenos']
			));
		$opts = array('http' =>
			 array(
				  'method'  => 'POST',
				  'header'  => 'Content-type: application/x-www-form-urlencoded',
				  'content' => $postdata
			 )
		);
		$context  = stream_context_create($opts);
		$result = file_get_contents('http://supervision.localhost.com/app_dev.php/highlight/api', false, $context);
		return array('content'=>$result);
    }
}
