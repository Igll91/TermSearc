<?php


namespace App\Service\TermSearch\Github;

class GithubTermSearchBuilder
{
    private $searchArea = GithubSearchArea::ISSUE;
    private $apiVersion = GithubApiVersion::V3;
    private $timeout    = 2.0; // In seconds

    public function setSearchArea(string $searchArea): GithubTermSearchBuilder
    {
        $this->searchArea = $searchArea;

        return $this;
    }

    public function setApiVersion(string $apiVersion): GithubTermSearchBuilder
    {
        $this->apiVersion = $apiVersion;

        return $this;
    }

    public function setTimeout(float $timeout): GithubTermSearchBuilder
    {
        $this->timeout = $timeout;

        return $this;
    }

    public function getSearchArea(): string
    {
        return $this->searchArea;
    }

    public function getApiVersion(): string
    {
        return $this->apiVersion;
    }

    public function getTimeout(): float
    {
        return $this->timeout;
    }

    public function build(): GithubTermSearch
    {
        return new GithubTermSearch($this);
    }
}
