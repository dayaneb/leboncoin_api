<?php

namespace App\Entity;

use App\Repository\AutomobileRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=AutomobileRepository::class)
 */
class Automobile extends Category
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $marque;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $modele;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMarque(): ?string
    {
        return $this->marque;
    }

    public function setMarque(string $marque): self
    {
        $this->marque = $marque;

        return $this;
    }

    public function getModele(): ?string
    {
        return $this->modele;
    }

    public function setModele(string $modele): self
    {
        $this->modele = $modele;

        return $this;
    }
    
    /**
     * searchBrandFromModel
     *
     * @param  mixed $modeleFromRequest
     * @param  mixed $automobileBrandModel
     * @return void
     */
    public function searchBrandFromModel(string $modeleFromRequest, array $automobileBrandModel)
    {
        foreach ($automobileBrandModel as $brand => $model) {
            $matchingWord = $this->wordMatch($model, strtolower($modeleFromRequest), 1);
            if (!empty($matchingWord)) {
                return ['marque'=>$brand, 'modele'=>$matchingWord];
            }

        }
    }
    
    /**
     * wordMatch
     *
     * @param  mixed $words
     * @param  mixed $input
     * @param  mixed $sensitivity
     * @return void
     */
    public function wordMatch($words, $input, $sensitivity)
    {
        $shortest = -1;
        $closest = 0;
        foreach ($words as $word) {
            if (is_int(stripos($input, $word)) ) {
                $lev = levenshtein($input, $word);
            
                if ($lev == 0) {
                    $closest = $word;
                    $shortest = 0;
                    break;
                }
                if ($lev <= $shortest || $shortest < 0) {
                    $closest  = $word;
                    $shortest = $lev;
                }
            } else {
                continue;
            }
        }
        if ($shortest <= $sensitivity) {
            return $closest;
        } else {
            return $closest;
        }
    }
}
