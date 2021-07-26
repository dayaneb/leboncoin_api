<?php

namespace App\Controller;


use App\Entity\Annonce;
use JMS\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AnnonceController extends AbstractController
{
    /**
     * @Route("/annonces", name="annonce_create", methods={"POST"})
     */
    public function creerAnnonce(Request $request, SerializerInterface $serializerInterface): Response
    {
        $data = $request->getContent();
        $annonce = $serializerInterface->deserialize($data, Annonce::class, 'json');
        var_dump($annonce); die();

        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/AnnonceController.php',
        ]);
    }
}
