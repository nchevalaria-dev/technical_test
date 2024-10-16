<?php

declare(strict_types=1);

namespace Tests\Feature;

use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class RepositoryControllerTest extends TestCase
{
    public function test_returns_bad_request_if_no_search_query_provided(): void
    {
        $this->getJson('/api/repository')->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function test_returns_bad_request_if_empty_search_query_provided(): void
    {
        $this->getJson('/api/repository?q=')->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function test_returns_bad_request_if_search_query_longer_than_256_chars(): void
    {
        $this->getJson('/api/repository?q='.str_repeat('a', 257))
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function test_returns_success_if_search_query_longer_equal_256_chars(): void
    {
        $this->getJson('/api/repository?q='.str_repeat('a', 256))
            ->assertStatus(Response::HTTP_OK);
    }

    public function test_returns_right_github_repositories_data(): void
    {
        $given = $this->getJson('/api/repository?q=linux')->assertStatus(Response::HTTP_OK)->json();
        $expected = $this->getExpected(__DIR__.'/../Expected/Repository/linux_github_search.json');

        foreach ($expected as $repository) {
            self::assertContains($repository, $given);
        }
    }

    public function test_returns_right_gitlab_repositories_data(): void
    {
        $given = $this->getJson('/api/repository?q=linux')->assertStatus(Response::HTTP_OK)->json();
        $expected = $this->getExpected(__DIR__.'/../Expected/Repository/linux_gitlab_search.json');

        foreach ($expected as $repository) {
            self::assertContains($repository, $given);
        }
    }

    public function test_returns_right_merged_repositories_data(): void
    {
        $response = $this->getJson('/api/repository?q=linux')->assertStatus(Response::HTTP_OK);

        $expectedGithub = $this->getExpected(__DIR__.'/../Expected/Repository/linux_github_search.json');
        $expectedGitlab = $this->getExpected(__DIR__.'/../Expected/Repository/linux_gitlab_search.json');

        self::assertEquals(array_merge($expectedGitlab, $expectedGithub), $response->json());
    }
}
