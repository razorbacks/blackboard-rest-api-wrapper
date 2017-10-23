<?php

use PHPUnit\Framework\TestCase;
use razorbacks\blackboard\rest\Api;
use razorbacks\blackboard\rest\BadResponse;

class ApiTest extends TestCase
{
    protected function getApiCredentials()
    {
        return [
            getenv('BB_REST_API_SERVER'),
            getenv('BB_REST_API_APPLICATION_ID'),
            getenv('BB_REST_API_SECRET'),
        ];
    }

    public function test_can_get_auth_token()
    {
        $api = new Api(...$this->getApiCredentials());

        $this->assertNotEmpty($api->getToken());
    }

    /**
     * @expectedException Exception
     */
    public function test_can_invalidate_token()
    {
        $credentials = $this->getApiCredentials();
        $credentials[2] = 'badpass';

        $api = new Api(...$credentials);
    }

    public function test_can_get_course_members()
    {
        $courseId = getenv('BB_REST_API_COURSE_ID');

        $api = new Api(...$this->getApiCredentials());

        $results = $api->get("/courses/{$courseId}/users")['results'] ?? null;

        $this->assertNotEmpty($results);
    }

    /**
     * @expectedException razorbacks\blackboard\rest\BadResponse
     */
    public function test_can_invalidate_course()
    {
        $api = new Api(...$this->getApiCredentials());

        try {
            $api->get('/courses/_0_0');
        } catch (BadResponse $e) {
            $this->assertEquals(404, $e->json()['status']);
            throw $e;
        }
    }

    public function test_can_create_and_delete_grade_column()
    {
        $courseId = getenv('BB_REST_API_COURSE_ID');

        $api = new Api(...$this->getApiCredentials());

        $gradeColumn = [
            'name' => 'Test Column',
            'description' => 'This is a test column.',
            'score' => [
                'possible' => 10,
                'decimalPlaces' => 0,
            ],
            'availability' => [
                'available' => 'Yes',
            ],
        ];

        $response = $api->post("/courses/{$courseId}/gradebook/columns", $gradeColumn);

        $columnId = $response['id'] ?? null;

        $this->assertNotEmpty($columnId);

        $response = $api->delete("/courses/{$courseId}/gradebook/columns/{$columnId}");

        $this->assertEmpty($response);
    }
}
