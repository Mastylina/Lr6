<?php

namespace App\Controller;

use App\Entity\Books;
use App\Form\BooksType;
use App\Repository\BooksRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\FileUploader;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/books')]
class BooksController extends AbstractController
{
    #[Route('/', name: 'books_index', methods: ['GET'])]
    public function index(BooksRepository $booksRepository): Response
    {
        return $this->render('books/index.html.twig', [
            'books' => $booksRepository->_findBy(),
        ]);
    }

    #[Route('/new', name: 'books_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $book = new Books();
        $form = $this->createForm(BooksType::class, $book);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $book->setDate();
            //image
            /** @var Symfony\Component\HttpFoundation\File\UploadedFile $file */
            $file = $form->get('cover')->getData();
            $path = $this->getParameter('kernel.project_dir') . '/public/uploads/img';
            if($file) {
                $fileName = $this->generateUniqueFileName() . '.' . $file->guessExtension();
                // перемещает файл в каталог
                $file->move(
                    $path,
                    $fileName
                );
                $book->setCover($fileName);
            }
            $file = $form->get('file')->getData();
            $path = $this->getParameter('kernel.project_dir') . '/public/uploads/file';  
            if($file) {
                $fileName = $this->generateUniqueFileName() . '.' . $file->guessExtension();
                // перемещает файл в каталог
                $file->move(
                    $path,
                    $fileName
                );
                $book->setFile($fileName);
            }

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($book);
            $entityManager->flush();

            return $this->redirectToRoute('books_index');
        }

        return $this->render('books/new.html.twig', [
            'book' => $book,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'books_show', methods: ['GET'])]
    public function show(Books $book): Response
    {
        return $this->render('books/show.html.twig', [
            'book' => $book,
        ]);
    }

    #[Route('/books/{id}/edit', name: 'books_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Books $book, SluggerInterface $slugger): Response
    {
        $form = $this->createForm(BooksType::class, $book);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $cover=$form->get('cover')->getData();
            $coverTargetDirectory= $this->getParameter('kernel.project_dir') . '/public/uploads/img';
            $coverFileUploader = new FileUploader($coverTargetDirectory, $slugger);

            $bookFile=$form->get('file')->getData();
            $fileTargetDirectory= $this->getParameter('kernel.project_dir') . '/public/uploads/file';
            $fileUploader = new FileUploader($fileTargetDirectory, $slugger);

            if ($book->getCover()) {
                $coverFileUploader->remove($book->getCover());
                $book->setCover("-");
            }

            if ($book->getFile()) {
                $fileUploader->remove($book->getfile());
                $book->setfile("-");
            }

            if ($cover) {
                $coverNewFilename = $coverFileUploader->upload($cover);
                $book->setCover($coverNewFilename);
            }

            if ($bookFile) {
                $fileNewFilename = $fileUploader->upload($bookFile);
                $book->setFile($fileNewFilename);
            }

            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('books_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('books/edit.html.twig', [
            'book' => $book,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'books_delete', methods: ['POST'])]
    public function delete(Request $request, Books $book): Response
    {
        if ($this->isCsrfTokenValid('delete'.$book->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($book);
            $entityManager->flush();
        }

        return $this->redirectToRoute('books_index', [], Response::HTTP_SEE_OTHER);
    }
    private function generateUniqueFileName()
    {
        return md5(uniqid());
    } 
}
