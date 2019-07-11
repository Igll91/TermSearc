<?php


namespace App\Service\TermSearch\Github;


use App\Service\TermSearch\TermSearchInterface;
use App\Utility\Result\AbstractResult;
use App\Utility\Result\Failure;
use App\Utility\Result\Success;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\RequestException;


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

    public function getTermCount(string $term): AbstractResult
    {
        $client = new Client([
            "base_uri"  => self::GITHUB_API_BASE_URI,
            "timeout"   => $this->timeout,
            "headers"   => [
                "Accept"    => "application/vnd.github." . $this->apiVersion ."+json"
                ]
        ]);

        // handle errors

        try {
            $response = $client->get($this->getRelativeUri($term));
        } catch (ConnectException $ex) {
            //TODO: log exception
            return new Failure("Connection exception, please try again."); // TODO: translate error message
        } catch (RequestException $ex) {
            dump($ex);
            //TODO: log exception
            return new Failure($ex->getResponse()->getReasonPhrase());
        }

        $jsonBody = json_decode($response->getBody()->getContents(), true);

        dump($client->getConfig());

        dump($response->getStatusCode());
        dump($jsonBody);
        dump($response->getHeaders());

        // IF STATUS CODE 200 AND json decode passed

        //TODO: handle GITHUB curl
        return new Success((float)$jsonBody['total_count']);
    }

    /**
     * Return relative URI path for current configuration setup.
     * @param string $term Term to be searched.
     * @return string Relative URI path.
     */
    private function getRelativeUri(string $term): string {
        return $this->searchArea . "?q=" . $term;
    }
}