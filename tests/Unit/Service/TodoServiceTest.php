<?php

namespace Tests\Unit\Service;

use App\Services\TodoService;
use App\Todo;
use App\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

/**
 * Class TodoServiceTest
 * @package Tests\Unit\Service
 * @coversDefaultClass \App\Services\TodoService
 */
class TodoServiceTest extends TestCase
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

    /**
     * @var TodoService
     */
    private $service;

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

        Auth::shouldReceive('user')->andReturn($this->user1);
        $this->service = new TodoService();
    }

    /**
     * @covers ::getList
     */
    public function testGetList()
    {
        // Given
        // When
        $result = $this->service->getList();

        // Then
        $ids = $result->map(function (Todo $todo) {
           return $todo->id;
        });

        $this->assertContains($this->todo1ForUser1->id, $ids);
        $this->assertContains($this->todo2ForUser1->id, $ids);
        $this->assertNotContains($this->todo1ForUser2->id, $ids);
        $this->assertNotContains($this->todo2ForUser2->id, $ids);
    }

    /**
     * @covers ::register
     */
    public function testRegister()
    {
        // Given
        $params = [
            'name' => 'todoTesting!'
        ];

        // When
        $this->service->register($params);

        // Then
        $this->assertDatabaseHas('todos', [
            'user_id' => $this->user1->id,
            'name' => $params['name']
        ]);
    }

    /**
     * @covers ::toggle
     */
    public function testToggle()
    {
        // Given
        // When
        $this->service->toggle($this->todo1ForUser1->id);

        // Then
        $this->todo1ForUser1->refresh();
        $this->assertFalse($this->todo1ForUser1->is_activate);

        // When
        $this->service->toggle($this->todo1ForUser1->id);

        // Then
        $this->todo1ForUser1->refresh();
        $this->assertTrue($this->todo1ForUser1->is_activate);
    }

    /**
     * @covers ::toggle
     */
    public function testToggleExceptionWhenNotOwnTodo()
    {
        // Given
        // When && Then
        $this->expectException(ModelNotFoundException::class);
        $this->service->toggle($this->todo2ForUser2->id);
    }

    /**
     * @covers ::toggle
     */
    public function testDelete()
    {
        // Given
        // When
        $this->service->delete($this->todo1ForUser1->id);

        // Then
        $this->assertDatabaseMissing('todos', [
            'id' => $this->todo1ForUser1->id
        ]);
    }

    /**
     * @covers ::toggle
     */
    public function testDeleteExceptionWhenNotOwnTodo()
    {
        // Given
        // When && Then
        $this->expectException(ModelNotFoundException::class);
        $this->service->delete($this->todo1ForUser2->id);
    }
}
