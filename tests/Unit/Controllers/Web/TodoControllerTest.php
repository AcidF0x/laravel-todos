<?php

namespace Tests\Unit\Controllers\Web;

use App\Services\TodoService;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Tests\TestCase;

/**
 * Class TodoControllerTest
 * @package Tests\Unit\Controllers\Web
 * @coversDefaultClass
 */
class TodoControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @var User
     */
    private $user;

    /**
     * @var TodoService
     */
    private $mockService;


    protected function setUp(): void
    {
        parent::setUp();
        $this->user = factory(User::class)->create();

        $this->mockService = Mockery::mock(TodoService::class);
        $this->instance(TodoService::class, $this->mockService);
    }

    public function testShowTodoPage()
    {
        // Given
        $uri = route('web-view-todo-page');
        $this->mockService->shouldReceive('getList')
            ->andReturn([])
            ->once();

        // When
        $response = $this->actingAs($this->user)->get($uri);

        // Then
        $response->assertOk();
    }

    public function testFailShowTodoPageWhenNotLogged()
    {
        // Given
        $uri = route('web-view-todo-page');
        $this->mockService->shouldReceive('getList')
            ->never();

        // When
        $response = $this->get($uri);

        // Then
        $response->assertRedirect(route('web-view-login-page'));
    }


    public function testRegister()
    {
        // Given
        $uri = route('web-register-todo');
        $params = [
            'name' => '1234'
        ];

        $this->mockService->shouldReceive('register')
            ->with($params)
            ->once();

        // When
        $response = $this->actingAs($this->user)->post($uri, $params);

        // Then
        $response->assertRedirect(route('web-view-todo-page'));
    }

    public function testFailRegisterWhenWitoutParams()
    {
        // Given
        $uri = route('web-register-todo');
        $params = [];

        $this->mockService->shouldReceive('register')
            ->never();

        // When
        $response = $this->actingAs($this->user)->post($uri, $params);

        // Then
        $response->assertRedirect(route('web-view-todo-page'))
            ->assertSessionHasErrors('name');
    }

    public function testFailRegisterWhenNotLoggedUser()
    {
        // Given
        $uri = route('web-register-todo');
        $params = [
            'name' => '1234'
        ];

        $this->mockService->shouldReceive('register')
            ->never();

        // When
        $response = $this->post($uri, $params);

        // Then
        $response->assertRedirect(route('web-view-login-page'));
    }

    public function testToggle()
{
    // Given
    $mockId = 100;
    $uri = route('web-toggle-todo', $mockId);

    $this->mockService->shouldReceive('toggle')
        ->with($mockId)
        ->once();

    // When
    $response = $this->actingAs($this->user)->get($uri);

    // Then
    $response->assertRedirect(route('web-view-todo-page'));
}

    public function testFailToggleWhenNotLoggedUser()
    {
        // Given
        $mockId = 100;
        $uri = route('web-toggle-todo', $mockId);

        $this->mockService->shouldReceive('toggle')
            ->never();

        // When
        $response = $this->get($uri);

        // Then
        $response->assertRedirect(route('web-view-login-page'));
    }
    public function testDelete()
    {
        // Given
        $mockId = 100;
        $uri = route('web-delete-todo', $mockId);

        $this->mockService->shouldReceive('delete')
            ->with($mockId)
            ->once();

        // When
        $response = $this->actingAs($this->user)->get($uri);

        // Then
        $response->assertRedirect(route('web-view-todo-page'));
    }

    public function testFailDeleteWhenNotLoggedUser()
    {
        // Given
        $mockId = 100;
        $uri = route('web-delete-todo', $mockId);

        $this->mockService->shouldReceive('delete')
            ->never();

        // When
        $response = $this->get($uri);

        // Then
        $response->assertRedirect(route('web-view-login-page'));
    }
}
