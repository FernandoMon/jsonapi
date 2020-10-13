<?php

namespace Tests\Feature\Articles;

use App\Models\Article;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class IncludeAuthorsTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function can_include_authors()
    {
       /* $user = User::factory()->create(['name' => "Blaire D'Amore"]);
        $article = Article::factory()->create(['user_id' => $user->id]);*/


        $article = Article::factory()->create();

        //$url = route('api.v1.articles.read', $article).'?include=authors';

        $this->jsonApi()
            ->includePaths('authors')
            ->get(route('api.v1.articles.read', $article))
            ->assertSee($article->user->name)
            //->assertSee($article->user->name, false)
            ->assertJsonFragment([
                'related' => route('api.v1.articles.relationships.authors', $article)
            ])
            ->assertJsonFragment([
                'self' => route('api.v1.articles.relationships.authors.read', $article)
            ]);
    }

    /** @test */
    public function can_fetch_related_authors()
    {
        $article = Article::factory()->create();

        //$url = route('api.v1.articles.read', $article).'?include=authors';

        $this->jsonApi()
            ->get(route('api.v1.articles.relationships.authors', $article))
            ->assertSee($article->user->name);

        $this->jsonApi()
            ->get(route('api.v1.articles.relationships.authors.read', $article))
            ->assertSee($article->user->id);
    }
}
