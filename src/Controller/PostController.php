<?php

namespace App\Controller;

use App\Entity\Post;
use App\Entity\Photo;
use App\Entity\User;
use DateTime;
use App\Form\PostType;
use App\Repository\PhotoRepository;
use App\Repository\PostRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

/**
 * @Route("/post")
 */
class PostController extends AbstractController
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
     * @Route("/", name="post_index", methods={"GET", "POST"})
     */
    public function new(Request $request, EntityManagerInterface $entityManager,PostRepository $postRepository): Response
    {
        
        $post = new Post();
        $post->setCreatedAt(new DateTime('NOW'));
        $post->setAutheur($this->security->getUser());
        // dd($post);

        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $images= $form->get('photos')->getData();
            foreach ($images as $image) {
                $fichier = md5(uniqid()) . '.' . $image->guessExtension();

                // On copie le fichier dans le dossier uploads
                $image->move(
                    $this->getParameter('images_directory'),
                    $fichier
                );

                // On stocke l'image dans la base de données (son nom)
                $img = new Photo();
                $img->setPhotoName($fichier);
                $post->addPhoto($img);
            }

            $entityManager->persist($post);
            $entityManager->flush();

            return $this->redirectToRoute('post_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('post/index.html.twig', [
            'post' => $post,
            'form' => $form,
            'posts' => $postRepository->findBy(array(), array('created_at'=>'DESC')),
        ]);
    }

      /**
     * @Route("/{id}/edit", name="post_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Post $post, EntityManagerInterface $entityManager): Response
    {
        $post->setCreatedAt(new DateTime('NOW'));

        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
   
            $photos = $form->get('photos')->getData();

            foreach($photos as $photo){
                
                $fichier = md5(uniqid()) . '.' . $photo->guessExtension();

                $photo->move(
                    $this->getParameter('images_directory'),
                    $fichier
                );

                $img = new Photo();
                $img->setPhotoName($fichier);
                $post->addPhoto($img);
            }
            
            $entityManager->flush();

            return $this->redirectToRoute('post_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('post/edit.html.twig', [
            'post' => $post,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/delete/photo/{id}", name="post_delete_photo", methods={"DELETE"})
     */
    public function deleteImage(Photo $photo, Request $request, EntityManagerInterface $entityManager){
        $data = json_decode($request->getContent(), true);

        
        if($this->isCsrfTokenValid('delete'.$photo->getId(), $data['_token'])){
           
            $nom = $photo->getPhotoName();
            // On supprime le fichier
            unlink($this->getParameter('images_directory').'/'.$nom);

            // On supprime l'entrée de la base
         
            $entityManager->remove($photo);
            $entityManager->flush();

            // On répond en json
            return new JsonResponse(['success' => 1]);
        }else{
            return new JsonResponse(['error' => 'Token Invalide'], 400);
        }
    }
  
    
}
