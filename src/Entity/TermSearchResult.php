<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

use JMS\Serializer\Annotation\Groups;
use JMS\Serializer\Annotation\Accessor;

use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\TermSearchResultRepository")
 * @UniqueEntity("term")
 */
class TermSearchResult
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=100, unique=true)
     * @Assert\NotBlank
     * @Assert\Length(
     *     min = 1,
     *     max = 100,
     *     minMessage = "Search value is too short. It should have {{ limit }} character or more.",
     *     maxMessage = "Search value is too long. It should have {{ limit }} characters or less."
     *     )
     *
     * @Groups("essential")
     */
    private $term;

    /**
     * @ORM\Column(type="float")
     * @Assert\NotBlank
     *
     * @Groups("essential")
     * @Accessor(getter="getScoreWithDefaultPrecision")
     */
    private $score;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank
     * @Assert\Length(min = 1, max = 255)
     */
    private $searchInterface;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank
     * @Assert\Length(min = 1, max = 255)
     */
    private $scoreInterface;

    /**
     * @var \DateTime $created
     *
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(type="datetime")
     */
    private $created;

    /**
     * @var \DateTime $updated
     *
     * @Gedmo\Timestampable(on="update")
     * @ORM\Column(type="datetime")
     */
    private $updated;

    public function __construct(string $term)
    {
        $this->term = $term;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTerm(): ?string
    {
        return $this->term;
    }

    public function setTerm(string $term): self
    {
        $this->term = $term;

        return $this;
    }

    public function getScore(): ?float
    {
        return $this->score;
    }

    public function setScore(float $score): self
    {
        $this->score = $score;

        return $this;
    }

    public function getSearchInterface(): ?string
    {
        return $this->searchInterface;
    }

    public function setSearchInterface(string $searchInterface): self
    {
        $this->searchInterface = $searchInterface;

        return $this;
    }

    public function getScoreInterface(): ?string
    {
        return $this->scoreInterface;
    }

    public function setScoreInterface(string $scoreInterface): self
    {
        $this->scoreInterface = $scoreInterface;

        return $this;
    }

    public function getCreated(): \DateTime
    {
        return $this->created;
    }

    public function setCreated(\DateTime $created): self
    {
        $this->created = $created;

        return $this;
    }

    public function getUpdated(): \DateTime
    {
        return $this->updated;
    }

    public function setUpdated(\DateTime $updated): self
    {
        $this->updated = $updated;

        return $this;
    }

    public function getScoreWithDefaultPrecision()
    {
        return number_format($this->score, 2);
    }
}
