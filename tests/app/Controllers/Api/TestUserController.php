<?php

namespace Tests\Feature;


use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\FeatureTestTrait;
use CodeIgniter\Test\DatabaseTestTrait;


class TestUserController extends CIUnitTestCase
{
    // use DatabaseTestTrait; disabled ini jadi gak minta migrate dan seed
    use FeatureTestTrait;

    // protected $migrate     = false;

    // protected $seed     = false;


    private $accessToken;


    protected function setUp(): void
    {
        parent::setUp();

        $response = $this->post('/api/login', [
            'username' => 'admin',
            'password' => '123',
        ]);

        $response->assertStatus(200);

        // $response->assertJSONFragment(['data' => [
        //     'accessToken' => $accessToken,
        // ]]);

        $data = $response->getJSON();
        $data = json_decode($data, true);
        $accessToken = $data['data']['accessToken'];
        $refreshToken = $data['data']['refreshToken'];

        $this->accessToken = $accessToken;
    }

    protected function tearDown(): void
    {
        parent::tearDown();
    }

    public function testNoLogin()
    {
        $response = $this->get('/api/users');

        $response->assertStatus(401);
    }

    public function testCreate()
    {
        $accessToken = $this->accessToken;

        $headers = [
            'Authorization' => "Bearer $accessToken"
        ];

        $response = $this->withHeaders($headers)->post('api/users', [
            'name' => 'New User',
            'username' => 'newuser',

            'email' => 'newuser@example.com',
            'password' => 'password',
            'role_id' => '2',
        ]);

        $response->assertStatus(201);
        $response->assertJSONFragment(['message' => 'Successfully created.']);

        $data = $response->getJSON();
        $data = json_decode($data, true);

        $userId = $data['data']['id'];
        putenv("userId=$userId");
    }

    public function testIndex()
    {
        // $accessToken = getenv('accessToken');
        $accessToken = $this->accessToken;

        $headers = [
            'Authorization' => "Bearer $accessToken"
        ];

        $response = $this->withHeaders($headers)->get('/api/users');

        $response->assertStatus(200);
    }

    public function testShow()
    {
        $accessToken = $this->accessToken;
        $userId = getenv('userId');

        $headers = [
            'Authorization' => "Bearer $accessToken"
        ];

        $response = $this->withHeaders($headers)->get('/api/users/' . $userId);

        $response->assertStatus(200);
        $response->assertJSONFragment(['data' => [
            'id' => $userId
        ]]);
    }

    public function testUpdate()
    {
        $accessToken = $this->accessToken;

        $headers = [
            'Authorization' => "Bearer $accessToken"
        ];

        $data = [
            'name' => 'New User Updated',
            'username' => 'newuser',
            'email' => 'newuser@example.com',
            'password' => 'password',
            'role_id' => '2',
        ];

        $response = $this->withHeaders($headers)->withBodyFormat('json')->put('api/users/' . getenv('userId'), $data);

        $response->assertStatus(200);
        $response->assertJSONFragment(['message' => 'Successfully updated.']);
        $response->assertJSONFragment(['data' => [
            'name' => 'New User Updated'
        ]]);
    }

    public function testDelete()
    {
        $accessToken = $this->accessToken;

        $headers = [
            'Authorization' => "Bearer $accessToken"
        ];

        $response = $this->withHeaders($headers)->delete('api/users/' . getenv('userId'));

        $response->assertStatus(200);
        $response->assertJSONFragment(['message' => 'Successfully deleted.']);
        $response->assertJSONFragment(['data' => [
            'id' => getenv('userId')
        ]]);
    }

    public function testMemberIndex()
    {

        $response = $this->post('/api/login', [
            'username' => 'member',
            'password' => '123',
        ]);

        $response->assertStatus(200);

        // $response->assertJSONFragment(['data' => [
        //     'accessToken' => $accessToken,
        // ]]);

        $data = $response->getJSON();
        $data = json_decode($data, true);
        $accessToken = $data['data']['accessToken'];
        $refreshToken = $data['data']['refreshToken'];

        $headers = [
            'Authorization' => "Bearer $accessToken"
        ];

        $response = $this->withHeaders($headers)->get('/api/users');

        $response->assertStatus(403);
        $data = $response->getJSON();
        $data = json_decode($data, true);
    }

    public function testUploadImageNoLogin()
    {
        $response = $this->post('api/user/upload-image', [
            'image' => '',
        ]);

        $data = $response->getJSON();
        $data = json_decode($data, true);
        $response->assertStatus(401);
    }
}
