<?php


namespace App\Tests\Controller;

use App\Controller\TermController;
use App\Entity\TermSearchResult;
use App\Service\TermScore\UndabotTermScore;
use App\Utility\TermSearchResultCacheValidation;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class TermControllerTest extends WebTestCase
{
    protected static $application;

    protected static function runCommand($command)
    {
        return self::getApplication()->run(new StringInput($command));
    }

    protected static function getApplication()
    {
        if (null === self::$application) {
            $client = static::createClient();

            self::$application = new Application($client->getKernel());
            self::$application->setAutoExit(false);
        }

        return self::$application;
    }

    /**
     * Test getScore term parameter length validation.
     */
    public function testGetScoreSearchTermValidation() {
        $searchTerm     = "VQCFumEfKvWZFJYJQFQcjVTVeqUgpeeepupvAYLkBSeDGncYPMrvpmbriPqAjLZFCxRVkYmcSjNWiZHdaaccCUJTaBcgukeDdReRnDTByHPVAGvLzueYGJkaJxPBgGucUamVADjfVVawAjXeSWKwfwaCKjPVaFrecuHJRznpUGexvUDmuEtMBwMEdkRzSjkxtNAxfhGfpuAPXEFFbwYyPQSerHGzFJDdKc";
        $client         = static::createClient();
        $result         =  $client->request('GET', '/score/' . $searchTerm);
        $wantedError    = "{\"error\":\"Search value is too long. It should have 100 characters or less.\"}";

        $this->assertEquals($wantedError, $client->getResponse()->getContent());
    }

    public function testGetScore() {
        $client     = static::createClient();
        $searchTerm = "Symfony";
        $doctrine   = self::$container->get('doctrine');

        /**
         * STEP 0
         *
         * Clear testing database
         */
        self::runCommand('doctrine:database:drop --force');
        self::runCommand('doctrine:database:create');
        self::runCommand('doctrine:schema:create');

        /**
         * STEP 1
         *
         * verify that DB does not contain TermSearchResult for wanted term.
         */
        $termSearchResult   = $doctrine->getRepository(TermSearchResult::class)->getOrCreateByTerm($searchTerm); /** @var $termSearchResult TermSearchResult */
        $this->assertEquals(null, $termSearchResult->getId());

        /**
         * STEP 2
         *
         * Verify that TermController score API search saved result in DB.
         */
        $client->request('GET', '/score/' . $searchTerm);
        $termSearchResult   = $doctrine->getRepository(TermSearchResult::class)->getOrCreateByTerm($searchTerm); /** @var $termSearchResult TermSearchResult */
        $this->assertNotNull($termSearchResult->getId());
        $this->assertEquals($searchTerm, $termSearchResult->getTerm());
        $this->assertNotNull($termSearchResult->getUpdated());
        $firstApiCallUpdateDateTime = $termSearchResult->getUpdated();
        
        /**
         * STEP 3
         *
         * Verify that call to the same API search will not trigger DB update due to cache
         */
        $client->request('GET', '/score/' . $searchTerm);
        $termSearchResult   = $doctrine->getRepository(TermSearchResult::class)->getOrCreateByTerm($searchTerm);
        $this->assertNotNull($termSearchResult->getUpdated());
        $this->assertEquals($firstApiCallUpdateDateTime, $termSearchResult->getUpdated());
    }

    public function testGetScoreCache() {
        $client                     = static::createClient();
        $searchTerm                 = "Symfony";
        $doctrine                   = self::$container->get('doctrine');
        $termSearchResult           = $doctrine->getRepository(TermSearchResult::class)->getOrCreateByTerm($searchTerm); /** @var $termSearchResult TermSearchResult */
        $firstApiCallUpdateDateTime = $termSearchResult->getUpdated();

        /**
         * STEP 1
         *
         * Create TermSearchResultCacheValidation cache validation object which will always return that cache is invalid
         * since it will compare current time with future time.
         */
        $undabotTermScore   = self::$container->get(UndabotTermScore::class);
        $validatorInterface = self::$container->get(ValidatorInterface::class);
        $fakeTermController = new TermController(new TermSearchResultCacheValidation("+2 hour"));
        $fakeTermController->setContainer(self::$container);

        /**
         * STEP 2
         *
         * Verify that TermController score API search will now update searched term in DB.
         */
        $fakeTermController->getScore($undabotTermScore, $validatorInterface, $searchTerm);
        $termSearchResult   = $doctrine->getRepository(TermSearchResult::class)->getOrCreateByTerm($searchTerm);

        $this->assertNotNull($termSearchResult->getId());
        $this->assertNotEquals($firstApiCallUpdateDateTime, $termSearchResult->getUpdated());
    }
}