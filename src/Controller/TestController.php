<?php


namespace App\Controller;

use App\Service\TermScore\TermScoreInterface;

use App\Service\TermSearch\Github\GithubTermSearchBuilder;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TestController
{
    /**
     * @Route("/test")
     */
    public function test(TermScoreInterface $termScore) {

        $builder = new GithubTermSearchBuilder();

//        $result = $builder->build()->getTermCount("php");
//        dump($result);

        $score = $termScore->getScore("php", $builder->build());

        dump($score);

        return new Response(
            '<html><body>Test</body></html>'
        );
    }
}