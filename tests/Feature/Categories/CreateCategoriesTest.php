<?php

namespace Tests\Feature\Categories;

use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Passport\Passport;
use Tests\TestCase;

class CreateCategoriesTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function guest_users_cannot_create_categories()
    {

        $category = Category::factory()->raw();

        $this->jsonApi()->withData([
            'type' => 'categories',
            'attributes' => $category
        ])->post(route('api.v1.categories.create'))
            ->assertStatus(401);

        $this->assertDatabaseMissing('categories', $category);
    }

    /** @test */
    public function authenticated_user_can_create_articles()
    {
        $user = User::factory()->create();

        $category = Category::factory()->raw();

        $this->assertDatabaseMissing('categories', $category);

        Passport::actingAs($user);

        $this->jsonApi()->withData([
            'type' => 'categories',
            'attributes' => $category
        ])->post(route('api.v1.categories.create'))->assertCreated();

        $this->assertDatabaseHas('categories', [
            'name' => $category['name'],
            'slug' => $category['slug'],
        ]);
    }

    /** @test */
    public function name_is_required()
    {
        $category = Category::factory()->raw(['name' => '']);

        Passport::actingAs(User::factory()->create());

        $this->jsonApi()->withData([
            'type' => 'categories',
            'attributes' => $category
        ])->post(route('api.v1.categories.create'))
            ->assertStatus(422)
            ->assertSee('data\/attributes\/name');

        $this->assertDatabaseMissing('categories', $category);
    }

    /** @test */
    public function slug_is_required()
    {
        $category = Category::factory()->raw(['slug' => '']);

        Passport::actingAs(User::factory()->create());

        $this->jsonApi()->withData([
            'type' => 'categories',
            'attributes' => $category
        ])->post(route('api.v1.categories.create'))
            ->assertStatus(422)
            ->assertSee('data\/attributes\/slug');

        $this->assertDatabaseMissing('categories', $category);
    }

    /** @test */
    public function slug_must_be_unique()
    {
        Category::factory()->create(['slug' => 'same-slug']);

        $category = Category::factory()->raw(['slug' => 'same-slug']);

        Passport::actingAs(User::factory()->create());

        $this->jsonApi()->withData([
            'type' => 'categories',
            'attributes' => $category
        ])->post(route('api.v1.categories.create'))
            ->assertStatus(422)
            ->assertSee('data\/attributes\/slug');

        $this->assertDatabaseMissing('categories', $category);
    }

    /** @test */
    public function slug_must_must_only_contain_letters_numbers_and_dashes()
    {
        $category = Category::factory()->raw(['slug' => '#$^^%$']);

        Passport::actingAs(User::factory()->create());

        $this->jsonApi()->withData([
            'type' => 'categories',
            'attributes' => $category
        ])->post(route('api.v1.categories.create'))
            ->assertStatus(422)
            ->assertSee('data\/attributes\/slug')
        ;

        $this->assertDatabaseMissing('categories', $category);
    }

    /** @test */
    public function slug_must_must_not_contain_underscores()
    {
        $category = Category::factory()->raw(['slug' => 'with_underscores']);

        Passport::actingAs(User::factory()->create());

        $this->jsonApi()->withData([
            'type' => 'categories',
            'attributes' => $category
        ])->post(route('api.v1.categories.create'))
            ->assertSee(trans('validation.no_underscores', ['attribute' => 'slug']))
            ->assertStatus(422)
            ->assertSee('data\/attributes\/slug')
        ;

        $this->assertDatabaseMissing('categories', $category);
    }

    /** @test */
    public function slug_must_must_not_start_with_dashes()
    {
        $category = Category::factory()->raw(['slug' => '-starts-with-dash']);

        Passport::actingAs(User::factory()->create());

        $this->jsonApi()->withData([
            'type' => 'categories',
            'attributes' => $category
        ])->post(route('api.v1.categories.create'))
            ->assertSee(trans('validation.no_starting_dashes', ['attribute' => 'slug']))
            ->assertStatus(422)
            ->assertSee('data\/attributes\/slug')
        ;

        $this->assertDatabaseMissing('categories', $category);
    }

    /** @test */
    public function slug_must_must_not_end_with_dashes()
    {
        $category = Category::factory()->raw(['slug' => 'ends-with-dash-']);

        Passport::actingAs(User::factory()->create());

        $this->jsonApi()->withData([
            'type' => 'categories',
            'attributes' => $category
        ])->post(route('api.v1.categories.create'))
            ->assertSee(trans('validation.no_ending_dashes', ['attribute' => 'slug']))
            ->assertStatus(422)
            ->assertSee('data\/attributes\/slug')
        ;

        $this->assertDatabaseMissing('categories', $category);
    }
}
