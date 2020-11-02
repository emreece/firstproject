<?php
namespace App\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class IndexController extends AbstractController {
  /**
   * @Route("/", name="index")
   * Method ({"GET", "POST"})
   */
  public function index(){
    return $this->render('index.html.twig', array
    ());
  }
}