<?php

namespace Tests\Feature;


use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\FeatureTestTrait;
use CodeIgniter\Test\DatabaseTestTrait;


class TestAuthController extends CIUnitTestCase
{
    use DatabaseTestTrait;
    use FeatureTestTrait;

    // For Migrations
    protected $migrate     = true;
    // protected $migrate     = false;
    protected $migrateOnce = false;
    protected $refresh     = true;
    // protected $namespace   = 'Tests\Support';
    protected $namespace   = 'App';


    // For Seeds
    protected $seedOnce = false; // gak berdampak
    // protected $seedOnce = true;
    // protected $seed     = 'All';
    // protected $basePath = 'tests/_support/Database';

    // bisa langsung ke nama file
    // protected $seed     = 'App\Database\Seeds\All';

    // atau sesuai dengan path nya
    protected $seed     = 'All';
    protected $basePath = 'App/Database';


    protected function setUp(): void
    {
        parent::setUp();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
    }

    public function testLogin()
    {
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

        putenv("accessToken=$accessToken");
        putenv("refreshToken=$refreshToken");
    }

    public function testRefreshToken()
    {

        $refreshToken = getenv('refreshToken');

        $response = $this->post('/api/refresh-token', [
            'refreshToken' => $refreshToken,
        ]);

        $response->assertStatus(200);
    }
}
