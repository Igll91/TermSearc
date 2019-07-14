# TermSearc

Date started: 07/2019

## API USAGE

#### Endpoint: GET score/{term}

Description: Uses github API to search number of matching issues with given **{term}** + " rocks" as positive result and
**{term}** + " sucks" as negative result. Score is calculated by dividing positive result with summary of positive
and negative result in a range from 0 to 10.


parameter **term** can be any string from 1 to 100 characters length.

***
######Example1: Successful API call

Link to example: http://localhost:8000/score/test

Output:
{
    "term": "test",
    "score": 2.92
}

***
######Example2: API call validation fail

Link to example: http://localhost:8000/score/VQCFumEfKvWZFJYJQFQcjVTVeqUgpeeepupvAYLkBSeDGncYPMrvpmbriPqAjLZFCxRVkYmcSjNWiZHdaaccCUJTaBcgukeDdReRnDTByHPVAGvLzueYGJkaJxPBgGucUamVADjfVVawAjXeSWKwfwaCKjPVaFrecuHJRznpUGexvUDmuEtMBwMEdkRzSjkxtNAxfhGfpuAPXEFFbwYyPQSerHGzFJDdKc

Output:
{
    "error": "Search value is too long. It should have 100 characters or less."
}

## Setup project locally

Steps to execute once you have cloned/downloaded project locally to your work station.

Note: you need to have PHP installed starting from version 7.1.3, also pdo_sqlite extension is used by the project.

***

###### STEP 1: Download symfony client

Download symfony client from following link: https://symfony.com/download.

We will use the client for easier local test environment setup.

###### STEP 2: Install composer (skip if you already have it installed)

Download composer: https://getcomposer.org/download/

Composer is a tool for dependency management in PHP. It allows you to declare the libraries your project depends on and it will manage (install/update) them for you.

###### STEP 3: Install dependencies

Go to the project **root** folder and execute: "*composer install*" command.

###### STEP 4: Update your local database with latest changes

Go to the project **root** folder and execute: "*php bin/console doctrine:migrations:migrate*"

To read more about symfony migrations bundle go to: https://symfony.com/doc/master/bundles/DoctrineMigrationsBundle/index.html

###### STEP 5: Start local server

Go to the project **root** folder and execute: "*symfony server:start*"

###### STEP 6: Open application in local server

Go to http://localhost:8000/score/test to test if server is up.