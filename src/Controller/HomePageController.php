<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\BooksRepository;

class HomePageController extends AbstractController
{
    #[Route('/', name: 'home_page')]
    public function index(BooksRepository $book): Response
    {
        return $this->render('books/index.html.twig', [
            'controller_name' => 'HomePageController',
            'books'=> $book->findAll(),
        ]);
    }
}
