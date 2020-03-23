<?php

namespace Tests\Feature\Web;

use App\Todo;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WebTest extends TestCase
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
     * 로그인이 안된 사용자는 로그인 페이지에 접속 할 수 있다.
     */
    public function testAccessLoginPageWithGuest()
    {
        // Given
        $uri = route('web-view-login-page');

        // When
        $response = $this->get($uri);

        // Then
        $response->assertOk();
    }

    /**
     * 로그인된 사용자는 로그인 페이지에 접속 할 수 없다.
     */
    public function testRedirectLoginPageWithLoggedUser()
    {
        // Given
        $uri = route('web-view-login-page');

        // When
        $response = $this->actingAs($this->user1)->get($uri);

        // Then
        $response->assertRedirect(route('web-view-todo-page'));
    }

    /**
     * Name, Password로 로그인 할 수 있다.
     */
    public function testLogin()
    {
        // Given
        $uri = route('web-login');
        $params = [
            'name' => $this->user1->name,
            'password' => 'pw.1234'
        ];

        // When
        $response = $this->post($uri, $params);

        // Then
        $response->assertRedirect(route('web-view-todo-page'));
        $this->assertAuthenticatedAs($this->user1);
    }

    /**
     * Name, Password 없이 로그인 할 수 없다.
     */
    public function testFailLoginWhenWithoutParams()
    {
        // Given
        $uri = route('web-login');
        $params = [];

        // When
        $response = $this->post($uri, $params);

        // Then
        $response->assertRedirect(route('web-view-login-page'))
            ->assertSessionHasErrors('login');
        $this->assertGuest();
    }

    /**
     * Name, Password가 등록 되지 않은 경우 로그인 할 수 없다.
     */
    public function testFailLoginWhenInvalidParams()
    {
        // Given
        $uri = route('web-login');
        $params = [
            'name' => $this->user1->name,
            'password' => '????????????'
        ];

        // When
        $response = $this->post($uri, $params);

        // Then
        $response->assertRedirect(route('web-view-login-page'))
            ->assertSessionHasErrors('login');
        $this->assertGuest();

        // When
        $params = [
            'name' => $this->user1->name . '?',
            'password' => 'pw.1234'
        ];
        $response = $this->post($uri, $params);

        // Then
        $response->assertRedirect(route('web-view-login-page'))
            ->assertSessionHasErrors('login');
        $this->assertGuest();
    }

    /**
     * 로그인이 안된 사용자는 회원가입 페이지에 접속 할 수 있다.
     */
    public function testAccessRegisterPageWithGuest()
    {
        // Given
        $uri = route('web-view-register-page');

        // When
        $response = $this->get($uri);

        // Then
        $response->assertOk();
    }

    /**
     * 로그인된 사용자는 회원가입 페이지에 접속 할 수 없다.
     */
    public function testRedirectRegisterPageWithLoggedUser()
    {
        // Given
        $uri = route('web-view-register-page');

        // When
        $response = $this->actingAs($this->user1)->get($uri);

        // Then
        $response->assertRedirect(route('web-view-todo-page'));
    }

    /**
     * 로그인 안된 사용자는 회원 가입 할 수 있다.
     */
    public function testRegister()
    {
        // Given
        $uri = route('web-register');
        $params = [
            'name' => 'registertest1234',
            'password' => 'this_is_password',
            'password_confirmation' => 'this_is_password',
        ];

        // When
        $response = $this->post($uri, $params);

        // Then
        $response->assertRedirect(route('web-view-todo-page'));
        $this->assertAuthenticated();
        $this->assertDatabaseHas('users', [
            'name' => $params['name']
        ]);
    }

    /**
     * 로그인 된 사용자는 회원 가입 할 수 없다.
     */
    public function testFailRegisterWithLoggedUser()
    {
        // Given
        $uri = route('web-register');
        $params = [
            'name' => 'registertest1234',
            'password' => 'this_is_password',
            'password_confirmation' => 'this_is_password',
        ];

        // When
        $response = $this->actingAs($this->user1)->post($uri, $params);

        // Then
        $response->assertRedirect(route('web-view-todo-page'));
        $this->assertDatabaseMissing('users', [
            'name' => $params['name']
        ]);
    }

    /**
     * 필요한 정보 없이 회원가입 할 수 없다.
     */
    public function testFailRegisterWhenNoRequiredParams()
    {
        // Given
        $uri = route('web-register');
        $params = [
            'name' => 'registertest1234',
            'password' => 'this_is_password',
            'password_confirmation' => 'this_is_password',
        ];

        foreach ($params as $key => $param ) {
            $copiedParams = $params;
            unset($copiedParams[$key]);

            // When
            $response = $this->post($uri, $copiedParams);

            // Then
            $response->assertRedirect();
            $this->assertGuest();
        }
    }

    /**
     * 로그인 된 사용자는 로그아웃 할 수 있다.
     */
    public function testLogout()
    {
        // Given
        $uri = route('web-logout');

        // When
        $response = $this->actingAs($this->user1)->get($uri);

        // Then
        $response->assertRedirect(route('web-view-login-page'));
        $this->assertGuest();
    }

    /**
     * 로그인 안된 사용자는 로그아웃 할 수 없다.
     */
    public function testFailLogoutWhenNotLoggedUser()
    {
        // Given
        $uri = route('web-logout');

        // When
        $response = $this->get($uri);

        // Then
        $response->assertRedirect(route('web-view-login-page'));
    }

    /**
     * 로그인 된 사용자는 투두 페이지에 접속이 가능하다.
     */
    public function testAccessTodoPage()
    {
        // Given
        $uri = route('web-view-todo-page');

        // When
        $response = $this->actingAs($this->user1)->get($uri);

        // Then
        $response->assertOk();
    }

    /**
     * 로그인 안된 사용자는 투두 페이지에 접속이 불가능하다.
     */
    public function testFailAccessTodoPageWhenNotLoggedUser()
    {
        // Given
        $uri = route('web-view-todo-page');

        // When
        $response = $this->get($uri);

        // Then
        $response->assertRedirect(route('web-view-login-page'));
    }

    /**
     * 로그인 된 사용자는 투두를 등록 할 수 있다.
     */
    public function testRegisterTodo()
    {
        // Given
        $uri = route('web-register-todo');
        $params = [
            'name' => 'this is Todo'
        ];

        // When
        $response = $this->actingAs($this->user1)->post($uri, $params);

        // Then
        $response->assertRedirect(route('web-view-todo-page'));
        $this->assertDatabaseHas('todos', [
            'name' => $params['name']
        ]);
    }

    /**
     * 로그인 안된 사용자는 투두를 등록 할 수 없다.
     */
    public function testFailRegisterTodoWhenNotLoggedUser()
    {
        // Given
        $uri = route('web-register-todo');
        $params = [
            'name' => 'this is Todo'
        ];

        // When
        $response = $this->post($uri, $params);

        // Then
        $response->assertRedirect(route('web-view-login-page'));
        $this->assertDatabaseMissing('todos', [
            'name' => $params['name']
        ]);
    }

    /**
     * name 없이 사용자는 투두를 등록 할 수 없다.
     */
    public function testFailRegisterTodoWhenWithoutParams()
    {
        // Given
        $uri = route('web-register-todo');
        $params = [];

        // When
        $response = $this->actingAs($this->user1)->post($uri, $params);

        // Then
        $response->assertRedirect(route('web-view-todo-page'))
            ->assertSessionHasErrors('name');
    }

    /**
     * 로그인 된 사용자는 투두 상태를 토글 할 수 있다.
     */
    public function testToggle()
    {
        // Given
        $uri = route('web-toggle-todo', $this->todo1ForUser1->id);

        // When
        $response = $this->actingAs($this->user1)->get($uri);

        // Then
        $response->assertRedirect(route('web-view-todo-page'));
        $this->assertDatabaseHas('todos', [
            'id' => $this->todo1ForUser1->id,
            'is_activate' => false
        ]);

        // When
        $response = $this->actingAs($this->user1)->get($uri);

        // Then
        $response->assertRedirect(route('web-view-todo-page'));
        $this->assertDatabaseHas('todos', [
            'id' => $this->todo1ForUser1->id,
            'is_activate' => true
        ]);
    }

    /**
     * 로그인 안된 사용자는 투두 상태를 토글 할 수 없다.
     */
    public function testFailToggleWhen()
    {
        // Given
        $uri = route('web-toggle-todo', $this->todo1ForUser1->id);

        // When
        $response = $this->get($uri);

        // Then
        $response->assertRedirect(route('web-view-login-page'));
        $this->assertDatabaseHas('todos', [
            'id' => $this->todo1ForUser1->id,
            'is_activate' => true
        ]);
    }

    /**
     * 다른 사용자의 투두는 토글 할 수 없다.
     */
    public function testFailToggleWhenNotOwnTodo()
    {
        // Given
        $uri = route('web-toggle-todo', $this->todo1ForUser2->id);

        // When
        $response = $this->actingAs($this->user1)->get($uri);

        // Then
        $response->assertStatus(404);
    }

    /**
     * 로그인 된 사용자는 투두를 삭제 할 수 있다.
     */
    public function testDelete()
    {
        // Given
        $uri = route('web-delete-todo', $this->todo1ForUser1->id);

        // When
        $response = $this->actingAs($this->user1)->get($uri);

        // Then
        $response->assertRedirect(route('web-view-todo-page'));
        $this->assertDatabaseMissing('todos', [
            'id' => $this->todo1ForUser1->id,
        ]);
    }

    /**
     * 로그인 안된 사용자는 투두를 삭제 할 수 없다.
     */
    public function testFailDeleteWhenNotLoggedUser()
    {
        // Given
        $uri = route('web-delete-todo', $this->todo1ForUser1->id);

        // When
        $response = $this->get($uri);

        // Then
        $response->assertRedirect(route('web-login'));
        $this->assertDatabaseHas('todos', [
            'id' => $this->todo1ForUser1->id,
        ]);
    }

    /**
     * 다른 사용자의 투두는 삭제 할 수 없다
     */
    public function testFailDeleteWhenNotOwnTodo()
    {
        // Given
        $uri = route('web-delete-todo', $this->todo2ForUser2->id);

        // When
        $response = $this->actingAs($this->user1)->get($uri);

        // Then
        $response->assertStatus(404);
    }

}
