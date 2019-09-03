<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class IndexController extends AbstractController
{
    /**
     * @Route("/", name="app_index")
     */
    public function index()
    {
        return $this->render('index/index.html.twig', [
            'controller_name' => 'IndexController',
        ]);
    }

    /**
     * @Route("/foo", name="app_foo")
     */
    public function foo()
    {
        return $this->render('index/index.html.twig', [
            'controller_name' => 'foo',
        ]);
    }

    /**
     * @Route("/bar", name="app_bar")
     */
    public function bar()
    {
        return $this->render('index/index.html.twig', [
            'controller_name' => 'bar',
        ]);
    }
}
