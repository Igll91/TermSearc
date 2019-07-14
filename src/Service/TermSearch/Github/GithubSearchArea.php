<?php


namespace App\Service\TermSearch\Github;

use MyCLabs\Enum\Enum;

/**
 * Class GithubSearchArea
 *
 * Enum containing Github API searchable items.
 *
 * @package App\Service\TermSearch\Github
 */
class GithubSearchArea extends Enum
{
    const ISSUE         = "issues";
    const COMMIT        = "commits";
    const REPOSITORY    = "repository";
    const CODE          = "code";
    const USER          = "users";
}
