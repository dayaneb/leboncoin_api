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
            if ($this->strposa($modeleFromRequest, $model, 1) !== false) {
                $matchingWord = $this->wordMatch($model, ucfirst(strtolower($modeleFromRequest)), strlen($modeleFromRequest));
                if (!empty($matchingWord)) {
                    return ['marque'=>$brand, 'modele'=>$matchingWord];
                }
            } else {
                return false;
            }
        }
    }
        
    /**
     * strposa
     *
     * @param  mixed $haystack
     * @param  mixed $needles
     * @param  mixed $offset
     * @return void
     */
    public function strposa($haystack, $needles=array(), $offset=0)
    {
        $chr = array();
        foreach ($needles as $needle) {
            $res = stripos($haystack, $needle, $offset);
            if ($res !== false) {
                $chr[$needle] = $res;
            }
        }
        if (empty($chr)) {
            return false;
        }
        return min($chr);
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
        foreach ($words as $word) {
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
        }
        
        if ($shortest <= $sensitivity) {
            return $closest;
        } else {
            return 0;
        }
    }
}
