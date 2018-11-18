<?php

use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\TableNode;
use Chatter\Models\Message;

/**
 * Defines application features from the specific context.
 */
class FeatureContext implements Context
{

    protected $header = [];

    protected $response = null;

    protected $client = null;



    /**
     * Initializes context.
     *
     * Every scenario gets its own context instance.
     * You can also pass arbitrary arguments to the
     * context constructor through behat.yml.
     */
    public function __construct($header_name, $header_value)
    {
        $this->header[$header_name] = $header_value;
        $this->header['Content-Typ'] = 'application/json';
    }


    /**
     * @Given I am an authenticated user
     */

    public function iAmAnauthenticatedUser()
    {
        $this->client = new GuzzleHttp\Client(['base_uri' => 'http://slim.gk', 'headers' => $this->header]);
        $this->response = $this->client->get('/');
       $this->iExpectAResponseCode(200);
    }

    /**
     * @When I use api message method :arg1 :arg2
     */
    public function iUseApiMessageMethod($arg1, $arg2)
    {
        if($arg1 == "GET") {
            $this->response = $this->client->$arg1($arg2);

            $this->iExpectAResponseCode(200);
        }
        elseif ($arg1 == "DELETE") {
            $this->response = $this->client->$arg1($arg2);

            $this->iExpectAResponseCode(204);
        }
        elseif ($arg1 == "POST") {
            $this->createPostData();
        }
        else {

        }
    }

    /**
     * @Then I expect a :arg1 response code
     */
    public function iExpectAResponseCode($arg1)
    {
        $responce_code = $this->response->getStatusCode();
        if($responce_code <> $arg1) {
            throw new Exception("It not work. I expect $arg1 status but it is: " . $responce_code);
        }
    }

    /**
     * @Then I expect at least :arg1 result
     */
    public function iExpectAtLeastResult($arg1)
    {
        $data = $this->getBodyAsJson();

        if(count($data) < $arg1) {
            throw new Exception("We expected at least $arg1 result but found, but found " . count($data));
        }
    }

    protected function getBodyAsJson()
    {
        return json_decode($this->response->getBody(), true);
    }

    protected function createPostData()
    {

        $this->response  = $this->client->post('/messages', ['form_params' => ['message' => 'test message']]);

        $this->iExpectAResponseCode(201);
    }


    /**
     * @Given This is my table
     */
    public function thisIsMyTable(TableNode $table)
    {

        $this->table = $table->getRows();

        array_shift($this->table); // remove header
        foreach($this->table as $value) {
            echo $value[0] . ' ' . $value[1];
        }
        return true;
    }



}
