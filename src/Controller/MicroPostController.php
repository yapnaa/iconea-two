<?php
namespace App\Controller;
use App\Entity\MicroPost;
use App\Form\MicroPostType;
use App\Repository\MicroPostRepository;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

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
	 * @var FormFactoryInterface
	 */
	private $formFactory;
	/**
	 * @var EntityManagerInterface
	 */
	private $entityManager;
	/**
	 * @var RouterInterface
	 */
	private $router;
	/**
	 * @var FlashBagInterface
	 */
	private $flashBag;
	/**
	 * @var AuthorizationCheckerInterface
	 */
	private $authorizationChecker;

	/**
	 * @param \Twig_Environment $twig
	 * @param SessionInterface $session
	 * @param RouterInterface $router
	 * @param MicroPostRepository $microPostRepository
	 * @param FormFactoryInterface $formFactory
	 * @param EntityManagerInterface $entityManager
	 * @param FlashBagInterface $flashBag
	 * @param AuthorizationCheckerInterface $authorizationChecker
	 */
	public function __construct(
		\Twig_Environment $twig,
		MicroPostRepository $microPostRepository,
		FormFactoryInterface $formFactory,
		EntityManagerInterface $entityManager,
		RouterInterface $router,
		FlashBagInterface $flashBag,
		AuthorizationCheckerInterface $authorizationChecker
	)
	{
		$this->twig = $twig;
		$this->microPostRepository = $microPostRepository;
		$this->formFactory = $formFactory;
		$this->entityManager = $entityManager;
		$this->router = $router;
		$this->flashBag = $flashBag;
		$this->authorizationChecker = $authorizationChecker;
	}

	/**
	* @Route("/", name="micro_post_index")
	*/
	public function index()
	{
		$html = $this->twig->render(
			'micro-post/index.html.twig',
			[
				'posts' => $this->microPostRepository->findBy([], ['time' => 'DESC'])
			]
		);

		return new Response($html);
	}

	/**
	 * @Route("/edit/{id}", name="micro_post_edit")
	 * @Security("is_granted('edit', microPost)", message="Access denied")
	 */
	public function edit(MicroPost $microPost, Request $request)
	{
		#$this->denyUnlessGranted('edit', $microPost);
		$this->denyAccessUnlessGranted('edit', $microPost);

		if(!$this->authorizationChecker->isGranted('edit', $microPost)) {
			throw new UnauthorizedHttpException('Not authorized to edit post');
		}
		$form = $this->formFactory->create(MicroPostType::class, $microPost);
		$form->handleRequest($request);
		
		$microPost->setTime(new \DateTime());

		if($form->isSubmitted() && $form->isValid()) {
			# No need to call persist when making changes
			# $this->entityManager->persist($microPost);
			$this->entityManager->flush();
			$this->flashBag->add('notice', 'Micro Post was edited.');

			return new RedirectResponse($this->router->generate('micro_post_index'));
		}

		return new Response(
			$this->twig->render(
				'micro-post/add.html.twig',
				['form' => $form->createView()]
			)
		);
	}

	/**
	 * @Route("/delete/{id}", name="micro_post_delete")
	 * @Security("is_granted('delete', microPost)", message="Access denied")
	 */
	public function delete(MicroPost $microPost, Request $request)
	{
		$this->entityManager->remove($microPost);
		$this->entityManager->flush();

		$this->flashBag->add('notice', 'Micro Post was deleted.');

		return new RedirectResponse($this->router->generate('micro_post_index'));
	}

	/**
	 * @Route("/add", name="micro_post_add")
	 * @Security("is_granted('ROLE_USER')")
	 */
	public function add(Request $request, TokenStorageInterface $tokenStorage)
	{
		$user = $tokenStorage->getToken()->getUser();
		$microPost = new MicroPost();
		$microPost->setTime(new \DateTime());
		$microPost->setUser($user);

		$form = $this->formFactory->create(MicroPostType::class, $microPost);
		$form->handleRequest($request);

		if($form->isSubmitted() && $form->isValid()) {

			$this->entityManager->persist($microPost);
			$this->entityManager->flush();
			$this->flashBag->add('notice', 'Micro Post was added.');

			return new RedirectResponse($this->router->generate('micro_post_index'));
		}

		return new Response(
			$this->twig->render(
				'micro-post/add.html.twig',
				['form' => $form->createView()]
			)
		);
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

	/**
	* @Route("/{id}", name="micro_post_post")
	*/
	public function post(MicroPost $post)
	{
		#$post = $this->microPostRepository->find($id);

		return new Response(
			$this->twig->render(
				'micro-post/post.html.twig',
				[
					'post' => $post
				]
			)
		);
	}
}