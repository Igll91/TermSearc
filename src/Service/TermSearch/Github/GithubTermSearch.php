<?php


namespace App\Service\TermSearch\Github;

use App\Service\TermSearch\TermSearchInterface;
use App\Utility\Result\AbstractResult;
use App\Utility\Result\Failure;
use App\Utility\Result\Success;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\RequestException;
use Symfony\Component\HttpFoundation\Response;

class GithubTermSearch implements TermSearchInterface
{
    private const GITHUB_API_BASE_URI = "https://api.github.com/search/";

    /**
     * @var GithubSearchArea
     */
    private $searchArea;

    /**
     * @var GithubApiVersion
     */
    private $apiVersion;

    /**
     * @var int
     */
    private $timeout;

    public function __construct(GithubTermSearchBuilder $builder)
    {
        $this->apiVersion = $builder->getApiVersion();
        $this->searchArea = $builder->getSearchArea();
        $this->timeout    = $builder->getTimeout();
    }

    /**
     * @inheritDoc
     */
    public function getTermCount(string $term): AbstractResult
    {
        $client = new Client([
            "base_uri"  => self::GITHUB_API_BASE_URI,
            "timeout"   => $this->timeout,
            "headers"   => [
                "Accept"    => "application/vnd.github." . $this->apiVersion ."+json"
                ]
        ]);

        try {
            $response = $client->get($this->getRelativeUri($term));

            if ($response->getStatusCode() == Response::HTTP_OK) {
                $jsonBody = json_decode($response->getBody()->getContents(), true);

                return new Success((float)$jsonBody['total_count']);
            } else {
                return new Failure($response->getStatusCode());
            }
        } catch (ConnectException $ex) {
            return new Failure("Connection exception, please try again.");
        } catch (RequestException $ex) {
            return new Failure($ex->getResponse()->getReasonPhrase());
        }
    }

    /**
     * Return relative URI path for current configuration setup.
     * @param string $term Term to be searched.
     * @return string Relative URI path.
     */
    private function getRelativeUri(string $term): string
    {
        return $this->searchArea . "?q=" . $term;
    }
}
