<?php

namespace App\Controller;

use App\Entity\Emploi;
use App\Entity\Annonce;
use App\Entity\Category;
use App\Entity\Automobile;
use JMS\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class AnnonceController extends AbstractController
{
    /**
     * @Route("/annonces", name="annonce_create", methods={"POST"})
     */
    public function creerAnnonce(Request $request, SerializerInterface $serializerInterface, ParameterBagInterface $params): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $category = $data['category'];
        $annonce = $serializerInterface->deserialize($request->getContent(), Annonce::class, 'json');

        //Selectionner la categorie adequate
        switch ($category) {
            case 'Emploi':
                $category = new Emploi();
                break;

            case 'Automobile':
                $category = new Automobile();
                if (!in_array('modele', array_keys($data)) || empty($data['modele'])) {
                    $response = new JsonResponse(['message' => 'Merci de renseigner au moins le modele du véhicule'], Response::HTTP_NOT_FOUND);
                }else{
                    $brandModel = $category->searchBrandFromModel($data['modele'], $params->get('automobile.brands'));
                    if($brandModel==false) {
                        $response= new JsonResponse(['message' => 'Modèle non trouvé !'], Response::HTTP_NOT_FOUND);
                    } else {
                        $category->setMarque($brandModel['marque'])->setModele($brandModel['modele']);
                    }
                }
                

                break;
            
            case 'Immobilier':
                $category = new Automobile();
                break;
            
            default:
                $response = new JsonResponse(['message' => 'Les seules catégories définies sont: Emploi, Automobile, Immobilier'], Response::HTTP_NOT_FOUND);
                break;
        }

        if ($response->getStatusCode() == 200){
            $annonce->setCategory($category);

            $em = $this->getDoctrine()->getManager();
            $em->persist($annonce);
            $em->flush();
            $response =new JsonResponse(['message' => 'Annonce crée !'], Response::HTTP_CREATED);
        } 
        return $response;
    }


}
