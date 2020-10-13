<?php

namespace Tests\Feature;

use App\Models\Article;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FilterArticleTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function can_filter_articles_by_title()
    {
        $this->withoutExceptionHandling();
        Article::factory()->create([
           'title'=> 'Aprende Laravel Desde Cero'
        ]);

        Article::factory()->create([
           'title'=> 'Other Article'
        ]);

        $url = route('api.v1.articles.index', ['filter[title]' => 'Laravel']);

        $this->jsonApi()->get($url)
            ->assertJsonCount(1, 'data')
            ->assertSee('Aprende Laravel Desde Cero')
            ->assertDontSee('Other Article');
    }

    /** @test */
    public function can_filter_articles_by_content()
    {
        $this->withoutExceptionHandling();
        Article::factory()->create([
           'content'=> '<div>Aprende Laravel Desde Cero</div>'
        ]);

        Article::factory()->create([
           'content'=> '<div>Other Article</div>'
        ]);

        $url = route('api.v1.articles.index', ['filter[content]' => 'Laravel']);

        $this->jsonApi()->get($url)
            ->assertJsonCount(1, 'data')
            ->assertSee('Aprende Laravel Desde Cero')
            ->assertDontSee('Other Article');
    }

    /** @test */
    public function can_filter_articles_by_year()
    {
        Article::factory()->create([
           'title'=> 'Article from 2020',
            'created_at' => now()->year(2020)
        ]);

        Article::factory()->create([
            'title'=> 'Article from 2021',
            'created_at' => now()->year(2021)
        ]);

        $url = route('api.v1.articles.index', ['filter[year]' => '2020']);

        $this->jsonApi()->get($url)
            ->assertJsonCount(1, 'data')
            ->assertSee('Article from 2020')
            ->assertDontSee('Article from 2021');
    }

    /** @test */
    public function can_filter_articles_by_month()
    {
        Article::factory()->create([
           'title'=> 'Article from February',
            'created_at' => now()->month(2)
        ]);

        Article::factory()->create([
           'title'=> 'Another Article from February',
            'created_at' => now()->month(2)
        ]);

        Article::factory()->create([
           'title'=> 'Article from January',
            'created_at' => now()->month(1)
        ]);

        $url = route('api.v1.articles.index', ['filter[month]' => '2']);

        $this->jsonApi()->get($url)
            ->assertJsonCount(2, 'data')
            ->assertSee('Article from February')
            ->assertSee('Another Article from February')
            ->assertDontSee('Article from January');
    }

    /** @test */
    public function cannot_filter_articles_by_unknown_filters()
    {
        Article::factory()->create();

        $url = route('api.v1.articles.index', ['filter[unknown]' => '2']);

        $this->jsonApi()->get($url)->assertStatus(400);
    }

    /** @test */
    public function can_search_article_by_title_and_content()
    {
        Article::factory()->create([
            'title' => 'Article from Aprendible',
            'content' => "content"
        ]);

        Article::factory()->create([
            'title' => 'Another Article from February',
            'content' => 'content Aprendible...'
        ]);

        Article::factory()->create([
            'title' => 'Title 2',
            'content' => 'content 2'
        ]);

        $url = route('api.v1.articles.index', ['filter[search]' => 'Aprendible']);

        $this->jsonApi()->get($url)
            ->assertJsonCount(2, 'data')
            ->assertSee('Article from Aprendible')
            ->assertSee('Another Article from February')
            ->assertDontSee('Title 2');
    }

    /** @test */
    public function can_search_article_by_title_and_content_with_multiple_terms()
    {
        Article::factory()->create([
            'title' => 'Article from Aprendible',
            'content' => "content"
        ]);

        Article::factory()->create([
            'title' => 'Another Article from February',
            'content' => 'content Aprendible...'
        ]);

        Article::factory()->create([
            'title' => 'Another Laravel Article from February',
            'content' => 'content...'
        ]);

        Article::factory()->create([
            'title' => 'Title 2',
            'content' => 'content 2'
        ]);

        $url = route('api.v1.articles.index', ['filter[search]' => 'Aprendible Laravel']);

        $this->jsonApi()->get($url)
            ->assertJsonCount(3, 'data')
            ->assertSee('Article from Aprendible')
            ->assertSee('Another Article from February')
            ->assertSee('Another Laravel Article from February')
            ->assertDontSee('Title 2');
    }
}
