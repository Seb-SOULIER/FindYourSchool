<?php

namespace App\Controller;

use App\Repository\StudentRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="app_home")
     */
    public function index(StudentRepository $studentRepository): Response
    {
        if(!isset($_COOKIE["userMemory"])){
            $userMemory = $studentRepository->findOneBy([],['userMemory'=>'DESC'])->getUserMemory()+1;
            setcookie("userMemory",$userMemory,time()+ 3600*24*30);
        }else{
            $userMemory = $_COOKIE["userMemory"];
            $res = new Response();
            $res->headers->clearCookie('userMemory');
            $res->send();
            setcookie("userMemory",$userMemory,time()+ 3600*24*30);
        }

        return $this->render('home/index.html.twig');
    }
}
