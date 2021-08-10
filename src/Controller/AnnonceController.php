<?php

namespace App\Controller;

use App\Entity\Emploi;
use App\Entity\Annonce;
use App\Entity\Automobile;
use App\Entity\Immobilier;
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
        switch (ucfirst($category)) {
            case 'Emploi':
                $category = new Emploi();
                break;

            case 'Automobile':
                $category = new Automobile();
                if (!in_array('modele', array_keys($data)) || empty($data['modele'])) {
                    $response = new JsonResponse(['message' => 'Merci de renseigner au moins le modele du véhicule'], Response::HTTP_NOT_FOUND);
                } else {
                    $brandModel = $category->searchBrandFromModel($data['modele'], $params->get('automobile.brands'));
                    if ($brandModel==false) {
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

        if (!isset($response) || $response->getStatusCode() == 200) {
            $annonce->setCategory($category);
           
            $em = $this->getDoctrine()->getManager();
            $em->persist($annonce);
            $em->flush();
            $response =new JsonResponse(['message' => 'Annonce crée !'], Response::HTTP_CREATED);
        }
        return $response;
    }

    /**
     * @Route("/annonces/{id}", name="update_annonce", methods={"PUT"})
     */
    public function modifierAnnonce(int $id, Request $request, ParameterBagInterface $params): ?JsonResponse
    {
        $annonce = $this->getDoctrine()->getRepository(Annonce::class)->findOneById($id);
        
        if (is_null($annonce)) {
            $response = new JsonResponse(['message' => 'Aucune Annonce ne dispose de l\'identifiant '.$id], Response::HTTP_NOT_FOUND);
        } else {
            $data = json_decode($request->getContent(), true);
            empty($data['titre']) ? true : $annonce->setTitre($data['titre']);
            empty($data['contenu']) ? true : $annonce->setContenu($data['contenu']);
  
            //Selectionner la categorie adequate
            if (!empty($data['category'])) {
                switch (ucfirst($data['category'])) {
                    case 'Emploi':
                        $category = new Emploi();
                        break;

                    case 'Automobile':
                        $category = new Automobile();
                        if (!in_array('modele', array_keys($data)) || empty($data['modele'])) {
                            $response = new JsonResponse(['message' => 'Merci de renseigner au moins le modele du véhicule'], Response::HTTP_NOT_FOUND);
                        } else {
                            $brandModel = $category->searchBrandFromModel($data['modele'], $params->get('automobile.brands'));
                            if ($brandModel==false) {
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
            }

            if (!isset($response) || $response->getStatusCode() == 200) {
                $annonce->setCategory($category);

                $em = $this->getDoctrine()->getManager();
                $em->persist($annonce);
                $em->flush();
                $response = new JsonResponse(['message' => 'Mise à jour effectuée ! Annonce n°'.$id], Response::HTTP_OK);
            }
        }

        return $response;
    }

    /**
     * @Route("/annonces/{id}", name="get_one_annonce", methods={"GET"})
     */
    public function recupererAnnonce(int $id): JsonResponse
    {
        $annonce = $this->getDoctrine()->getRepository(Annonce::class)->findOneById($id);
        if (is_null($annonce)) {
            $response = new JsonResponse(['message' => 'Aucune Annonce ne dispose de l\'identifiant '.$id], Response::HTTP_NOT_FOUND);
        } else {
            $annonceCategory = $annonce->getCategory();
            $data = [
                'id' => $annonce->getId(),
                'titre' => $annonce->getTitre(),
                'contenu' => $annonce->getContenu(),
            ];
    
            //Recuperation des informations par categories correspondants
            switch (true) {
                case $annonceCategory instanceof Automobile:
                    $data['category'] = 'Automobile';
                    $data['marque'] = $annonceCategory->getMarque();
                    $data['modele'] = $annonceCategory->getModele();
                    break;
                case $annonceCategory instanceof Emploi:
                    $data['category'] = 'Emploi';
                    break;
    
                case $annonceCategory instanceof Immobilier:
                    $data['category'] = 'Immobilier';
                    break;
            }
            $response = new JsonResponse($data, Response::HTTP_OK);
        }

        return $response;
    }

    /**
     * @Route("/annonces/{id}", name="delete_annonce", methods={"DELETE"})
     */
    public function delete($id): JsonResponse
    {
        $annonce = $this->getDoctrine()->getRepository(Annonce::class)->findOneById($id);
        if (is_null($annonce)) {
            $response = new JsonResponse(['message' => 'Aucune Annonce ne dispose de l\'identifiant '.$id], Response::HTTP_NOT_FOUND);
        } else {
            $em = $this->getDoctrine()->getManager();
            $em->remove($annonce);
            $em->flush();
            $response = new JsonResponse(['status' => 'Annonce Supprimée !'], Response::HTTP_NO_CONTENT);
        }
        return $response;
    }
}
