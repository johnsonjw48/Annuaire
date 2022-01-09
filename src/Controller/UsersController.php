<?php

namespace App\Controller;

use App\Entity\Avatar;
use App\Entity\User;
use App\Form\ProfileType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\PostRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;

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

     /**
     * @Route("/users/profil/modifier", name="profil_modifier")
     */
    public function editProfile(Request $request, EntityManagerInterface $entityManager)
    {
        $user = $this->getUser();
        
        $form = $this->createForm(ProfileType::class, $user);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

           
            $image= $form->get('avatar')->getData();
            if ($image !== null && $user->getAvatar() === null) {
                $fichier = md5(uniqid()) . '.' . $image->guessExtension();

                // On copie le fichier dans le dossier uploads
                $image->move(
                    $this->getParameter('images_directory'),
                    $fichier
                );

                // On stocke l'image dans la base de données (son nom)
                $img = new Avatar();
                $img->setAvatarName($fichier);
                $user->setAvatar($img);
            }

            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            // $this->addFlash('message', 'Profil mis à jour');
            return $this->redirectToRoute('users');
        }

        return $this->render('users/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/users/delete/avatar/{id}", name="profile_delete_avatar", methods={"DELETE"})
     */
    public function deleteImage(Avatar $avatar,EntityManagerInterface $entityManager, Request $request){
        
        $data = json_decode($request->getContent(), true);

        // On vérifie si le token est valide
        if($this->isCsrfTokenValid('delete'.$avatar->getId(), $data['_token'])){
            // On récupère le nom de l'image
            $nom = $avatar->getAvatarName();
            // On supprime le fichier
            unlink($this->getParameter('images_directory').'/'.$nom);

            // On supprime l'entrée de la base
            $entityManager->remove($avatar);
            $entityManager->flush();

            // On répond en json
            return new JsonResponse(['success' => 1]);
        }else{
            return new JsonResponse(['error' => 'Token Invalide'], 400);
        }
        
    }
}
