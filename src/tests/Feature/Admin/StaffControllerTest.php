<?php

namespace Tests\Feature\Admin;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Admin;

class StaffControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic feature test example.
     *
     * @return void
     */

     /** @test */
    public function 一般ユーザーのみスタッフ一覧に表示される()
    {
        // 一般ユーザー2人作成
        $user1 = User::factory()->create([
            'name' => '一般ユーザーA',
            'email' => 'userA@example.com',
            'role' => 'user',
        ]);

        $user2 = User::factory()->create([
            'name' => '一般ユーザーB',
            'email' => 'userB@example.com',
            'role' => 'user',
        ]);

        // 管理者ユーザー作成
        $admin = Admin::factory()->create([
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
        ]);

        // 管理者でログイン（adminガード）
        $this->actingAs($admin, 'admin');

        // スタッフ一覧ページにアクセス
        $response = $this->get(route('admin.staff.index'));

        $response->assertStatus(200)
            ->assertSee($user1->name)
            ->assertSee($user1->email)
            ->assertSee($user2->name)
            ->assertSee($user2->email)
            ->assertDontSee($admin->email);
    }

     /** @test */
    public function 詳細リンクが各スタッフに表示される()
    {
        $user = User::factory()->create([
            'role' => 'user',
        ]);

        $admin = User::factory()->create([
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
        ]);

        // 管理者でログイン（adminガード）
        $this->actingAs($admin, 'admin');

        $response = $this->get(route('admin.staff.index'));

        $response->assertStatus(200);

        // 詳細リンクのURLが正しいことを確認
        $response->assertSee(route('admin.staff_attendance.index', ['id' => $user->id]));
    }
}
