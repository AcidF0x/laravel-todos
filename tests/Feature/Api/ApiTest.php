<?php

namespace Tests\Feature\Api;

use App\Todo;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApiTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @var User
     */
    private $user1, $user2;

    /**
     * @var Todo
     */
    private $todo1ForUser1, $todo2ForUser1, $todo1ForUser2, $todo2ForUser2;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user1 = factory(User::class)->create();
        $this->todo1ForUser1 = factory(Todo::class)->create();
        $this->todo2ForUser1 = factory(Todo::class)->create();
        $this->user1->todos()->saveMany([$this->todo1ForUser1, $this->todo2ForUser1]);

        $this->user2 = factory(User::class)->create();
        $this->todo1ForUser2 = factory(Todo::class)->create();
        $this->todo2ForUser2 = factory(Todo::class)->create();
        $this->user2->todos()->saveMany([$this->todo1ForUser2, $this->todo2ForUser2]);
    }

    /**
     * 정상 토큰으로 접속시 투두 리스트를 확인 할 수 있다.
     */
    public function testGetListWithToken()
    {
        // Given
        $queryString = '?api_token=' . $this->user1->api_token;
        $uri = route('api-todo-get');

        // When
        $response = $this->getJson($uri . $queryString);

        // Then
        $response->assertOk()
            ->assertExactJson([
                [
                    'id' => $this->todo1ForUser1->id,
                    'name' => $this->todo1ForUser1->name,
                    'is_activate' => $this->todo1ForUser1->is_activate
                ],
                [
                  'id' => $this->todo2ForUser1->id,
                  'name' => $this->todo2ForUser1->name,
                  'is_activate' => $this->todo2ForUser1->is_activate
                ]
            ]);
    }

    /**
     * 토큰없이 투두 리스트를 확인 할 수 없다.
     */
    public function testFailGetListWithoutToken()
    {
        // Given
        $uri = route('api-todo-get');

        // When
        $response = $this->getJson($uri);

        // Then
        $response->assertStatus(401);
    }

    /**
     * 투두를 등록 할 수 있다.
     */
    public function testRegisterTodo()
    {
        // Given
        $queryString = '?api_token=' . $this->user1->api_token;
        $uri = route('api-todo-register');
        $params = [
            'name' => '새로운 투두 입니다'
        ];

        // When
        $response = $this->postJson($uri . $queryString, $params);

        // Then
        $response->assertOk();
        $this->assertDatabaseHas('todos', [
            'name' => $params['name']
        ]);
    }

    /**
     * 토큰 없이 투두를 등록 할 수 없다
     */
    public function testFailReigsterTodoWithoutToken()
    {
        // Given
        $queryString = '?api_token=';
        $uri = route('api-todo-register');
        $params = [
            'name' => '새로운 투두 입니다'
        ];

        // When
        $response = $this->postJson($uri . $queryString, $params);

        // Then
        $response->assertStatus(401);
        $this->assertDatabaseMissing('todos', [
            'name' => $params['name']
        ]);
    }

    /**
     * name 없이 투두를 등록 할 수 없다
     */
    public function testFailReigsterTodoWithoutName()
    {
        // Given
        $queryString = '?api_token=' . $this->user1->api_token;
        $uri = route('api-todo-register');
        $params = [];

        // When
        $response = $this->postJson($uri . $queryString, $params);

        // Then
        $response->assertStatus(422);
    }

    /**
     * 투두를 삭제 할 수 있다.
     */
    public function testDelete()
    {
        // Given
        $queryString = '?api_token=' . $this->user1->api_token;
        $uri = route('api-todo-delete', $this->todo2ForUser1->id);

        // When
        $response = $this->deleteJson($uri . $queryString);

        // Then
        $response->assertOk();
        $this->assertDatabaseMissing('todos', [
            'id' => $this->todo2ForUser1->id
        ]);
    }

    /**
     * 토큰없이 투두를 삭제할수 없다.
     */
    public function testFailDeleteWithoutToken()
    {
        // Given
        $queryString = '?api_token=';
        $uri = route('api-todo-delete', $this->todo1ForUser1->id);

        // When
        $response = $this->deleteJson($uri . $queryString);

        // Then
        $response->assertStatus(401);
        $this->assertDatabaseHas('todos', [
            'id' => $this->todo1ForUser1->id
        ]);
    }

    /**
     * 타 사용자의 투두를 삭제 할 수 없다
     */
    public function testFailDeleteNotOwnTodo()
    {
        // Given
        $queryString = '?api_token=' . $this->user1->api_token;
        $uri = route('api-todo-delete', $this->todo1ForUser2->id);

        // When
        $response = $this->deleteJson($uri . $queryString);

        // Then
        $response->assertStatus(404);
        $this->assertDatabaseHas('todos', [
            'id' => $this->todo1ForUser2->id
        ]);
    }

    /**
     * 투두 상태를 토글 할 수 있다.
     */
    public function testToggle()
    {
        // Given
        $queryString = '?api_token=' . $this->user1->api_token;
        $uri = route('api-todo-toggle', $this->todo1ForUser1->id);

        // When
        $response = $this->put($uri . $queryString);

        // Then
        $response->assertStatus(200);
        $this->assertDatabaseHas('todos', [
            'id' => $this->todo1ForUser1->id,
            'is_activate' => false
        ]);

        // When
        $response = $this->putJson($uri . $queryString);

        // Then
        $response->assertStatus(200);
        $this->assertDatabaseHas('todos', [
            'id' => $this->todo1ForUser1->id,
            'is_activate' => true
        ]);
    }

    /**
     * 토큰없이 상태를 토글 할 수 없다.
     */
    public function testFailToggleWithoutToken()
    {
        // Given
        $queryString = '?api_token=';
        $uri = route('api-todo-toggle', $this->todo1ForUser1->id);

        // When
        $response = $this->putJson($uri . $queryString);

        // Then
        $response->assertStatus(401);

        $this->assertDatabaseHas('todos', [
            'id' => $this->todo1ForUser1->id,
            'is_activate' => true
        ]);
    }

    /**
     * 다른 사용자의 투두 상태를 토글 할 수 없다.
     */
    public function testFailToggleNotOwnTodo()
    {
        // Given
        $queryString = '?api_token=' . $this->user1->api_token;
        $uri = route('api-todo-toggle', $this->todo2ForUser2->id);

        // When
        $response = $this->putJson($uri . $queryString);

        // Then
        $response->assertStatus(404);
    }
}
