<?php

namespace App\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use App\Repository\PostRepository;

class UsersController extends AbstractController
{
    /**
     * @var Security
     */
    private $security;

    public function __construct(Security $security)
    {
       $this->security = $security;
    }

    /**
     * @Route("/users", name="users")
     */
    public function index(PostRepository $postRepository): Response
    {
        $user=$this->security->getUser();
        // dd($user);
        return $this->render('users/index.html.twig',  [
            'posts' => $postRepository->findByExampleField($user),
        ]);;
    }
}
