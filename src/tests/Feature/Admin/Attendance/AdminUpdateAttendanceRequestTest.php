<?php

namespace Tests\Feature\Admin\Attendance;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

// 追加
use App\Http\Requests\Admin\Attendance\AdminUpdateAttendanceRequest;
use Illuminate\Support\Facades\Validator;

class AdminUpdateAttendanceRequestTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_example()
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    public function test_valid_data_passes()
    {
        $data = [
            'clock_in' => '09:00',
            'clock_out' => '18:00',
            'note' => '勤怠修正です',
            'breaks' => [
                ['start' => '12:00', 'end' => '13:00'],
                ['start' => '', 'end' => ''], // 空の休憩行はOK
            ],
        ];

        $validator = $this->getValidator($data);

        $this->assertTrue($validator->passes());
    }

    public function test_missing_required_fields_fail()
    {
        $data = [
            'clock_in' => '',
            'clock_out' => '',
            'note' => '',
        ];

        $validator = $this->getValidator($data);

        $this->assertFalse($validator->passes());
        $this->assertArrayHasKey('clock_in', $validator->errors()->toArray());
        $this->assertArrayHasKey('clock_out', $validator->errors()->toArray());
        $this->assertArrayHasKey('note', $validator->errors()->toArray());
    }

    public function test_invalid_time_format_fails()
    {
        $data = [
            'clock_in' => '9時',
            'clock_out' => '18:00',
            'note' => 'フォーマットミス',
        ];

        $validator = $this->getValidator($data);

        $this->assertFalse($validator->passes());
        $this->assertArrayHasKey('clock_in', $validator->errors()->toArray());
    }

    public function test_clock_out_before_clock_in_fails()
    {
        $data = [
            'clock_in' => '18:00',
            'clock_out' => '09:00',
            'note' => '順序ミス',
        ];

        $validator = $this->getValidator($data);

        $this->assertFalse($validator->passes());
        $this->assertArrayHasKey('clock_out', $validator->errors()->toArray());
    }

    public function test_break_outside_work_time_fails()
    {
        $data = [
            'clock_in' => '09:00',
            'clock_out' => '18:00',
            'note' => '休憩時間チェック',
            'breaks' => [
                ['start' => '08:00', 'end' => '09:30'], // start が出勤前
                ['start' => '17:30', 'end' => '18:30'], // end が退勤後
            ],
        ];

        $validator = $this->getValidator($data);

        $this->assertFalse($validator->passes());
        $this->assertArrayHasKey('breaks.0.start', $validator->errors()->toArray());
        $this->assertArrayHasKey('breaks.1.end', $validator->errors()->toArray());
    }
}
