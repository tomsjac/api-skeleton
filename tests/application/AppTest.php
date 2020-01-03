<?php
namespace Tests\application;

class AppTest extends BaseTestCase
{
    
    /**
     * Test If the JSON response is correct
     *
     */
    public function testResponseIsJson()
    {
        $response = $this->runApp('GET', '/', null);
        $this->assertTrue(is_object(json_decode((string)$response->getBody())));
    }


    /**
     * Test if the homepage without Auth is ok
     *
     */
    public function testGetHomepageWithoutAuth()
    {
        $response = $this->runApp('GET', '/', null, false);
        
        $this->assertEquals(200, $response->getStatusCode());

        $content = json_decode((string)$response->getBody());
        $this->assertContains('Hello World : Welcome to the API', $content->response);
    }
}
