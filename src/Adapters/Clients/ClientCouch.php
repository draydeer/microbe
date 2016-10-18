<?php

namespace Microbe\Adapters\Clients;

/**
 * Class ClientCouch
 * @package Microbe\Adapters\ClientsExtensions
 */
class ClientCouch
{

    /*
     *
     */
    protected
        $_db,
        $_host,

        $_client,
        $_REST;

    /**
     *
     */
    public function __construct($param = null)
    {
        $this->client = curl_init();
        $this->db = $this->getParam('db', $param);
        $this->_host = $this->getParam('host', $param, 'localhost:5984');
        $this->_REST = $this->_host . '/' . $this->db . '/';
    }

    /**
     *
     */
    public function RESTDelete(
        $command,
        $value
    )
    {
        curl_setopt($this->client, CURLOPT_CUSTOMREQUEST, 'DELETE');
    }

    /**
     *
     */
    public function RESTGet(
        $command,
        $value = null
    )
    {
        curl_setopt($this->client, CURLOPT_CUSTOMREQUEST, 'GET');
        curl_setopt($this->client, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($this->client, CURLOPT_URL, $this->_REST . $command);

        return curl_exec($this->client);
    }

    /**
     *
     */
    public function RESTPost(
        $command,
        $value
    )
    {
        curl_setopt($this->client, CURLOPT_CUSTOMREQUEST, 'POST');
    }

    /**
     *
     */
    public function RESTPut(
        $command,
        $value
    )
    {
        curl_setopt($this->client, CURLOPT_CUSTOMREQUEST, 'PUT');
    }

    /**
     *
     */
    public function getAll()
    {
        var_dump($this->RESTGet('_all_docs'));die;

        $_result = json_decode($this->RESTGet('_all_docs'));

        if (isset($_result->rows)) {
            return $_result->rows;
        }

        return null;
    }
}
