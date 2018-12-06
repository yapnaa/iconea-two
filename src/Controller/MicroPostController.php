<?php
namespace App\Controller;
use App\Repository\MicroPostRepository;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;

/**
 * @Route("/micro-post")
 */
class MicroPostController extends AbstractController
{
	/**
	 * @var \Twig_Environment
	 */
	private $twig;
	/**
	 * @var MicroPostRepository
	 */
	private $microPostRepository;

	/**
	 * @param \Twig_Environment $twig
	 * @param SessionInterface $session
	 * @param RouterInterface $router
	 */
	public function __construct(
		\Twig_Environment $twig,
		MicroPostRepository $microPostRepository
	)
	{
		$this->twig = $twig;	
		$this->microPostRepository = $microPostRepository;	
	}

	/**
	* @Route("/", name="micro_post_index")
	*/
	public function index()
	{
		$html = $this->twig->render(
			'micro/index.html.twig',
			[
				'posts' => $this->microPostRepository->findAll()
			]
		);

		return new Response($html);
	}

	/**
	* @Route("/add", name="micro_post_add")
	*/
	public function add()
	{
		$posts = $this->session->get('posts');
		$posts[uniqid()] = [
			'title' => ' A random title - '.rand(1,1000),
			'text' => ' Some random text nr - '.rand(1,1000),
			'date' => new \DateTime(),
		];
		$this->session->set('posts', $posts);

		#return new RedirectResponse($this->router->generate('blog_index'));
	}

	/**
	* @Route("/show/{id}", name="micro_post_show")
	*/
	public function show($id)
	{
		$posts = $this->session->get('posts');

		if(!$posts || !isset($posts[$id])) {
			throw new NotFoundHttpException('Post not found');
		}

		$html = $this->twig->render(
			'blog/post.html.twig',
			[
				'id' => $id,
				'post' => $posts[$id],
			]
		);

		return new Response($html);
	}
}