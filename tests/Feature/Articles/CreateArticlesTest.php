<?php

namespace Tests\Feature\Articles;

use App\Models\Article;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Passport\Passport;
use Tests\TestCase;

class CreateArticlesTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function guest_user_cannot_create_articles()
    {

        $article = array_filter(Article::factory()->raw(['user_id' => null]));

        $this->jsonApi()->withData([
            'type' => 'articles',
            'attributes' => $article
        ])->post(route('api.v1.articles.create'))->assertStatus(401);

        $this->assertDatabaseMissing('articles', [
            'title' => $article['title'],
            'slug' => $article['slug'],
            'content' => $article['content'],
        ]);
    }

    /** @test */
    public function authenticated_user_can_create_articles()
    {
        $user = User::factory()->create();

        $article = array_filter(Article::factory()->raw(['user_id' => null]));

        $this->assertDatabaseMissing('articles', $article);

        Passport::actingAs($user);

        $this->jsonApi()->withData([
            'type' => 'articles',
            'attributes' => $article
        ])->post(route('api.v1.articles.create'))->assertCreated();

        $this->assertDatabaseHas('articles', [
            'user_id' => $user->id,
            'title' => $article['title'],
            'slug' => $article['slug'],
            'content' => $article['content'],
        ]);
    }

    /** @test */
    public function title_is_required()
    {
        $article = Article::factory()->raw(['title' => '']);

        Passport::actingAs(User::factory()->create());

        $this->jsonApi()->withData([
            'type' => 'articles',
            'attributes' => $article
        ])->post(route('api.v1.articles.create'))
        ->assertStatus(422)
        ->assertSee('data\/attributes\/title');

        $this->assertDatabaseMissing('articles', $article);
    }

    /** @test */
    public function content_is_required()
    {
        $article = Article::factory()->raw(['content' => '']);

        Passport::actingAs(User::factory()->create());

        $this->jsonApi()->withData([
            'type' => 'articles',
            'attributes' => $article
        ])->post(route('api.v1.articles.create'))
            ->assertStatus(422)
            ->assertSee('data\/attributes\/content');

        $this->assertDatabaseMissing('articles', $article);
    }

    /** @test */
    public function slug_is_required()
    {
        $article = Article::factory()->raw(['slug' => '']);

        Passport::actingAs(User::factory()->create());

        $this->jsonApi()->withData([
            'type' => 'articles',
            'attributes' => $article
        ])->post(route('api.v1.articles.create'))
            ->assertStatus(422)
            ->assertSee('data\/attributes\/slug');

        $this->assertDatabaseMissing('articles', $article);
    }

    /** @test */
    public function slug_is_unique()
    {
        Article::factory()->create(['slug' => 'same-slug']);

        $article = Article::factory()->raw(['slug' => 'same-slug']);

        Passport::actingAs(User::factory()->create());

        $this->jsonApi()->withData([
            'type' => 'articles',
            'attributes' => $article
        ])->post(route('api.v1.articles.create'))
            ->assertStatus(422)
            ->assertSee('data\/attributes\/slug');

        $this->assertDatabaseMissing('articles', $article);
    }

    /** @test */
    public function slug_must_only_contain_letters_numbers_and_dashes()
    {
        $article = Article::factory()->raw(['slug' => '$&$%&$%']);

        Passport::actingAs(User::factory()->create());

        $this->jsonApi()->withData([
            'type' => 'articles',
            'attributes' => $article
        ])->post(route('api.v1.articles.create'))
            ->assertStatus(422)
            ->assertSee('data\/attributes\/slug');

        $this->assertDatabaseMissing('articles', $article);
    }

    /** @test */
    public function slug_must_not_contain_underscores()
    {
        $article = Article::factory()->raw(['slug' => 'with_underscores']);

        Passport::actingAs(User::factory()->create());

        $this->jsonApi()->withData([
            'type' => 'articles',
            'attributes' => $article
        ])->post(route('api.v1.articles.create'))
            ->assertSee(trans('validation.no_underscores', ['attribute' => 'slug']))
            ->assertStatus(422)
            ->assertSee('data\/attributes\/slug')->dump();

        $this->assertDatabaseMissing('articles', $article);
    }

    /** @test */
    public function slug_must_not_start_with_dashes()
    {
        $article = Article::factory()->raw(['slug' => '-start-with-dash']);

        Passport::actingAs(User::factory()->create());

        $this->jsonApi()->withData([
            'type' => 'articles',
            'attributes' => $article
        ])->post(route('api.v1.articles.create'))
            ->assertSee(trans('validation.no_starting_dashes', ['attribute' => 'slug']))
            ->assertStatus(422)
            ->assertSee('data\/attributes\/slug')->dump();

        $this->assertDatabaseMissing('articles', $article);
    }

    /** @test */
    public function slug_must_not_end_with_dashes()
    {
        $article = Article::factory()->raw(['slug' => 'ends-with-dash-']);

        Passport::actingAs(User::factory()->create());

        $this->jsonApi()->withData([
            'type' => 'articles',
            'attributes' => $article
        ])->post(route('api.v1.articles.create'))
            ->assertSee(trans('validation.no_ending_dashes', ['attribute' => 'slug']))
            ->assertStatus(422)
            ->assertSee('data\/attributes\/slug')->dump();

        $this->assertDatabaseMissing('articles', $article);
    }
}
