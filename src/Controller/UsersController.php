<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use App\Repository\PostRepository;
use App\Repository\UserRepository;

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

    /**
     * @Route("/users/all", name="users_all")
     */
    public function all(UserRepository $userRepository): Response
    {

        return $this->render('users/all.html.twig', [
            'users'=> $userRepository->findAll()
        ]);
    }

      /**
     * @Route("/users/{id}", name="user_show", methods={"GET"})
     */
    public function show(User $user, PostRepository $postRepository): Response
    {
        
        return $this->render('users/show.html.twig', [
            'user' => $user,
            'posts' => $postRepository->findByExampleField($user)
        ]);
    }
}
