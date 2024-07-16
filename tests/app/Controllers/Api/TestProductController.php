<?php

namespace Tests\Feature;


use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\FeatureTestTrait;
use CodeIgniter\Test\DatabaseTestTrait;


class TestProductController extends CIUnitTestCase
{
    use FeatureTestTrait;

    private $accessToken;


    protected function setUp(): void
    {
        parent::setUp();

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

        $this->accessToken = $accessToken;
    }

    protected function tearDown(): void
    {
        parent::tearDown();
    }

    public function testCreate()
    {
        $accessToken = $this->accessToken;

        $headers = [
            'Authorization' => "Bearer $accessToken"
        ];

        $response = $this->withHeaders($headers)->post('api/products', [
            'name' => 'product1',
            'description' => 'product1',
            'price' => '1000',
        ]);

        $response->assertStatus(201);
        $response->assertJSONFragment(['message' => 'Successfully created.']);

        $data = $response->getJSON();
        $data = json_decode($data, true);

        $productId = $data['data']['id'];
        putenv("productId=$productId");
    }
}
