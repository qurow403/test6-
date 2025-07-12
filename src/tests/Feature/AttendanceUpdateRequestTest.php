<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AttendanceUpdateRequestTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */

    use RefreshDatabase;

    protected function validData(array $overrides = [])
    {
        return array_merge([
            'clock_in' => '09:00',
            'clock_out' => '18:00',
            'note' => 'テスト備考',
            'breaks' => [
                ['start' => '12:00', 'end' => '13:00'],
                ['start' => '', 'end' => ''],
            ]
        ], $overrides);
    }

     /** @test */
    public function 出勤時間が退勤時間より後ならエラー()
    {
        $response = $this->from('/attendance/1')->put('/attendance/1', $this->validData([
            'clock_in' => '19:00',
            'clock_out' => '18:00'
        ]));

        $response->assertSessionHasErrors(['clock_out']);
        $response->assertSee('出勤時間もしくは退勤時間が不適切な値です');
    }

     /** @test */
    public function 休憩開始が退勤後ならエラー()
    {
        $response = $this->from('/attendance/1')->put('/attendance/1', $this->validData([
            'breaks' => [
                ['start' => '19:00', 'end' => '19:30']
            ]
        ]));

        $response->assertSessionHasErrors([
            'breaks.0.start' => '休憩時間が不適切な値です'
        ]);
    }

     /** @test */
    public function 休憩終了が退勤後ならエラー()
    {
        $response = $this->from('/attendance/1')->put('/attendance/1', $this->validData([
            'breaks' => [
                ['start' => '17:30', 'end' => '19:00']
            ]
        ]));

        $response->assertSessionHasErrors([
            'breaks.0.end' => '休憩時間が不適切な値です'
        ]);
    }

     /** @test */
    public function 備考未入力はエラー()
    {
        $response = $this->from('/attendance/1')->put('/attendance/1', $this->validData([
            'note' => ''
        ]));

        $response->assertSessionHasErrors([
            'note' => '備考を記入してください'
        ]);
    }
}
