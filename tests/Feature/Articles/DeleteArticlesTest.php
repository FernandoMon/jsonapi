<?php

namespace Tests\Feature\Articles;

use App\Models\Article;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Passport\Passport;
use Tests\TestCase;

class DeleteArticlesTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function guest_users_cannot_delete_articles()
    {
        $article = Article::factory()->create();

        $this->jsonApi()->delete(route('api.v1.articles.delete', $article))
        ->assertStatus(401);
    }

    /** @test */
    public function authenticated_users_can_delete_their_articles()
    {
        $article = Article::factory()->create();

        Passport::actingAs($article->user);

        $this->jsonApi()->delete(route('api.v1.articles.delete', $article))
        ->assertStatus(204);
    }

    /** @test */
    public function authenticated_users_cannot_delete_others_articles()
    {
        $article = Article::factory()->create();

        Passport::actingAs($user = User::factory()->create());

        $this->jsonApi()->delete(route('api.v1.articles.delete', $article))
        ->assertStatus(403);
    }
}
