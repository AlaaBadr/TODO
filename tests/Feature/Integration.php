<?php

namespace Tests\Feature;

use App\Task;
use App\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class Integration extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function followTest()
    {
        //Given
        $user = factory(User::class)->create();

        $task = factory(Task::class)->create();
        $task->private = 0;
        $task->save();

        //When
        $this->actingAs($user);
        $this->post(route('follow-task'), ['name' => $task->name])->assertStatus(200);

        //Then
        $this->assertDatabaseHas('followers', ['user_id' => $user->id, 'task_id' => $task->id]);
    }

    /** @test */
    public function createTaskTest()
    {
        //Given
        $user = factory(User::class)->create();

        //When
        $this->actingAs($user);
        $this->post(route('create-task'),['name' => 'Finish task', 'description' => 'Todo api about to be finished', 'deadline' => '03/08/2017'])->assertStatus(200);

        //Then
        $this->assertDatabaseHas('tasks', ['name' => 'Finish task']);
    }

    /** @test */
    public function registerTest()
    {
        //When
        $this->post(route('register-user'), ['name' => 'Douby AbdelSalam ElWa7sh', 'username' => 'elwa7sh', 'email' => 'douby@elwa7sh.com', 'password' => 'DoubyelWa7sh']);

        //Then
        $this->assertDatabaseHas('users', ['username' => 'elwa7sh']);
    }

    /** @test */
    public function markPrivateTest()
    {
        //Given
        $user = factory(User::class)->create();

        $task = factory(Task::class)->create();

        $task->private = 0;
        $task->user_id = $user->id;
        $task->Save();

        //When
        $this->actingAs($user);
        $this->post(route('mark-private-task'), ['id' => $task->id])->assertStatus(200);

        //Then
        $this->assertDatabaseHas('tasks', ['name' => $task->name, 'private' => 1]);
    }

    /** @test */
    public function avatarTest()
    {
        //Given
        $user = factory(User::class)->create();

        //When
        $this->actingAs($user);
        $this->post(route('upload-avatar'), ['avatar' => \Illuminate\Http\UploadedFile::fake()->image('     qavatar.jpeg')]);

        //Then
        $this->assertFileExists(storage_path()."\\app\\avatars\\".$user->username.'\avatar.jpeg');
    }

    /** @test */
    public function toggleTest()
    {
        //Given
        $user = factory(User::class)->create();

        $task = factory(Task::class)->create();

        $before = $task->completed;
        var_dump($before);
        $task->user_id = $user->id;
        $task->Save();

        //When
        $this->actingAs($user);
        $this->post(route('toggle-task'), ['id' => $task->id])->assertStatus(200);

        //Then
        $this->assertDatabaseHas('tasks', ['name' => $task->name, 'completed' => !$before]);
    }

    /** @test */
    public function changePasswordTest()
    {
        //Given
        $user = factory(User::class)->create();

        //When
        $this->actingAs($user);
        $this->post(route('change-password'), ['oldpassword' => 'secret', 'newpassword' => 'sosecret'])->assertStatus(200);

        //Then
        $this->assertTrue(password_verify('sosecret', User::find($user->id)->password));
        //$this->assertDatabaseHas('users', ['username' => $user->username, 'password' => 'sosecret']);
    }

    /** @test */
    public function loginTest()
    {
        //Given
        $user = factory(User::class)->create();

        //When
        $response = $this->post(route('get-authenticated-user'), ['email' => $user->email, 'password' => 'secret']);
        $response->assertStatus(200);

        //Then
        $json = json_decode($response->getContent(), TRUE);
        $this->assertArrayHasKey('token', $json, null);
    }

    /** @test */
    public function searchUsernameTest()
    {
        //Given
        $user1 = factory(User::class)->create();
        $user2 = factory(User::class)->create();

        //When
        $this->actingAs($user1);
        $response = $this->get(route('search-username', ['username' => $user2->username]));


        //Then
        $json = json_decode($response->getContent(), TRUE);
        $this->assertEquals($user2->id, $json['id']);
    }

    /** @test */
    public function searchNameTest()
    {
        //Given
        $user1 = factory(User::class)->create();
        $user2 = factory(User::class)->create();

        //When
        $this->actingAs($user1);
        $response = $this->get(route('search-name', ['name' => $user2->name]));

        //Then
        $json = json_decode($response->getContent(), TRUE);
        $this->assertEquals($user2->id, $json['id']);
    }

    /** @test */
    public function searchEmailTest()
    {
        //Given
        $user1 = factory(User::class)->create();
        $user2 = factory(User::class)->create();

        //When
        $this->actingAs($user1);
        $response = $this->get(route('search-email', ['email' => $user2->email]));

        //Then
        $json = json_decode($response->getContent(), TRUE);
        $this->assertEquals($user2->id, $json['id']);
    }

    /** @test */
    public function taskDestroyTest()
    {
        //Given
        $user = factory(User::class)->create();

        $task = factory(Task::class)->create();
        $task->user_id = $user->id;
        $task->save();

        //When
        $this->actingAs($user);
        $this->delete(route('delete-task', ['id' => $task->id]))->assertStatus(200);

        //Then
        $this->assertDatabaseMissing('tasks', ['name' => $task->name]);
    }

    /** @test */
    public function feedTest()
    {

    }
}
