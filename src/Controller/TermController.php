<?php


namespace App\Controller;

use App\Entity\TermSearchResult;
use App\Service\TermScore\TermScoreInterface;
use App\Service\TermSearch\Github\GithubTermSearchBuilder;
use App\Utility\CacheValidationInterface;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use Symfony\Component\Validator\Validator\ValidatorInterface;

use FOS\RestBundle\Controller\Annotations\Get;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\Annotations\View;

class TermController extends AbstractFOSRestController
{
    private $cacheValidation;

    public function __construct(CacheValidationInterface $cacheValidation)
    {
        $this->cacheValidation = $cacheValidation;
    }

    /**
     * @Get("/score/{term}")
     * @View(serializerGroups={"essential"})
     */
    public function getScore(TermScoreInterface $termScore, ValidatorInterface $validator, $term)
    {
        // Validate search term length before using any other resources
        $validationResult = $validator->validatePropertyValue(TermSearchResult::class, 'term', $term);
        if ($validationResult->count() != 0) {
            return ['error' => $validationResult[0]->getMessage()];
        }

        $entityManager      = $this->getDoctrine()->getManager();
        $termSearchResult   = $entityManager->getRepository(TermSearchResult::class)->getOrCreateByTerm($term); /** @var $termSearchResult TermSearchResult */

        // Update element if new entry or cache time limit passed
        if ($termSearchResult->getId() == null || !$this->cacheValidation->isValid($termSearchResult)) {
            $builder        = new GithubTermSearchBuilder();
            $scoreInterface = $builder->build();
            $score          = $termScore->getScore($term, $scoreInterface);

            if ($score->isSuccessful()) {
                $termSearchResult->setScore($score->get());
                $termSearchResult->setScoreInterface(get_class($scoreInterface));
                $termSearchResult->setSearchInterface(get_class($termScore));

                // Newly created entity must be persisted before flush
                if (!$termSearchResult->getId()) {
                    $entityManager->persist($termSearchResult);
                }

                $entityManager->flush();
            } else {
                return ['error' => $score->get()];
            }
        }

        return $termSearchResult;
    }

    /**
     * @return CacheValidationInterface
     */
    public function getCacheValidation(): CacheValidationInterface
    {
        return $this->cacheValidation;
    }

    /**
     * @param CacheValidationInterface $cacheValidation
     */
    public function setCacheValidation(CacheValidationInterface $cacheValidation): void
    {
        $this->cacheValidation = $cacheValidation;
    }
}
