<?php
namespace App\Controller;

use App\Service\Greeting;
use App\Service\VeryBadDesign;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class BlogController extends Controller
{
	/**
	* @var Greeting
	*/
	private $greeting;
	/**
	* @var VeryBadDesign
	*/
	private $badDesign;

	public function __construct(Greeting $greeting, VeryBadDesign $badDesign)
	{
		$this->greeting = $greeting;
		$this->badDesign = $badDesign;		
	}

	/**
	* @Route("/blog", name="blog_index")
	*/
	public function index(Request $request)
	{
		return $this->render('base.html.twig', ['message' => $this->greeting->greet($request->get('name')
		)]);
	}
}