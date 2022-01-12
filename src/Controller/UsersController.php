<?php

namespace App\Controller;

use App\Data\SearchData;
use App\Entity\Avatar;
use App\Entity\User;
use App\Entity\Post;
use DateTime;
use App\Form\PostType;
use App\Entity\Photo;
use App\Form\ProfileType;
use App\Form\SearchType;
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
    public function index(EntityManagerInterface $entityManager,PostRepository $postRepository, Request $request): Response
    {

        $user=$this->security->getUser();
        // dd($user);
        return $this->render('users/index.html.twig',  [
            'posts' => $postRepository->findByExampleField($user),
            
        ]);
    }

    /**
     * @Route("/users/all", name="users_all")
     */
    public function all(UserRepository $userRepository, Request $request): Response
    {
        $data = new SearchData();
        $form = $this->createForm(SearchType::class, $data);

        $form->handleRequest($request);

        // if ($form->isSubmitted() && $form->isValid()) {
        //     dd($userRepository->getSearchQuery($data));

        // }
       
        // $users= $userRepository->getSearchQuery($data);

        return $this->render('users/all.html.twig', [
            'users'=> $userRepository->getSearchQuery($data),
            'form'=>$form->createView()
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

            }elseif($user->getAvatar()!== null) {
                unlink($this->getParameter('images_directory').'/'.$user->getAvatar()->getAvatarName());

            
                $entityManager->remove($user->getAvatar());
                $entityManager->flush();

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

     
        if($this->isCsrfTokenValid('delete'.$avatar->getId(), $data['_token'])){
           
            $nom = $avatar->getAvatarName();
          
            unlink($this->getParameter('images_directory').'/'.$nom);

            
            $entityManager->remove($avatar);
            $entityManager->flush();

           
            return new JsonResponse(['success' => 1]);
        }else{
            return new JsonResponse(['error' => 'Token Invalide'], 400);
        }
        
    }
}
