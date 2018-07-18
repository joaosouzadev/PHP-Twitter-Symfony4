<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\MicroPost;
use App\Form\MicroPostType;
use App\Repository\MicroPostRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

	/**
	* @Route("/micro-post")
	*/
class MicroPostController{


	private $twig;

	private $microPostRepository;

	private $formFactory;

	private $entityManager;

	private $router;

	private $flashBag;

	private $authorizationChecker;

	public function __construct(
		\Twig_Environment $twig, 
		MicroPostRepository $microPostRepository, 
		FormFactoryInterface $formFactory, 
		EntityManagerInterface $entityManager, 
		RouterInterface $router, 
		FlashBagInterface $flashBag,
		AuthorizationCheckerInterface $authorizationChecker
	){

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
	public function index(){
		$html = $this->twig->render('micro-post/index.html.twig', [
			'posts' => $this->microPostRepository->findBy([], ['time' => 'DESC'])
		]);

		return new Response($html);
	}

	/**
	* @Route("/edit/{id}", name="micro_post_edit")
	* @Security("is_granted('edit', microPost)", message="Acesso negado!")
	*/
	public function edit(MicroPost $microPost, Request $request){

		// $this->denyUnlessGranted('edit', $microPost); outro jeito de validar autorização, caso a classe extenda Controller
		if (!$this->authorizationChecker->isGranted('edit', $microPost)) {
			throw new UnauthorizedHttpException("Acesso Negado");
		}

		$form = $this->formFactory->create(MicroPostType::class, $microPost);
		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()){
			$this->entityManager->flush();

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
	* @Security("is_granted('edit', microPost)", message="Acesso negado!")
	*/
	public function delete(MicroPost $microPost){
		
		$this->entityManager->remove($microPost);
		$this->entityManager->flush();

		$this->flashBag->add('notice', 'Post deletado!');

		return new RedirectResponse($this->router->generate('micro_post_index'));
	}

	/**
	* @Route("/add", name="micro_post_add")
	* @Security("is_granted('ROLE_USER')")
	*/
	public function add(Request $request, TokenStorageInterface $tokenStorage){

		$user = $tokenStorage->getToken()->getUser();

		$microPost = new MicroPost();
		$microPost->setUser($user);

		$form = $this->formFactory->create(MicroPostType::class, $microPost);
		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()){
			$this->entityManager->persist($microPost);
			$this->entityManager->flush();

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
	* @Route("/user/{username}", name="micro_post_user")
	*/
	public function userPosts(User $userWithPosts) {
		$html = $this->twig->render('micro-post/index.html.twig', [
			'posts' => $this->microPostRepository->findBy(
				['user' => $userWithPosts], 
				['time' => 'DESC'])
			// 'posts' => $userWithPosts->getPosts() /* LAZY LOADING, mas os resultados vem sorteados random *?
		]);

		return new Response($html);
	}

	/**
	* @Route("/{id}", name="micro_post_post")
	*/
	public function post(MicroPost $post){

		return new Response(
			$this->twig->render('micro-post/post.html.twig', [ 'post' => $post] )
		);
	}
}