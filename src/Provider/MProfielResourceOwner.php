<?php

namespace Tactics\OAuth2\Client\Provider;

use League\OAuth2\Client\Provider\ResourceOwnerInterface;

class MProfielResourceOwner implements ResourceOwnerInterface
{
    /**
     * @var array
     */
    protected $data;

    /**
     * MProfielResourceOwner constructor.
     * @param array $response
     */
    public function __construct(array $response)
    {
        $this->data = $response['data'];
    }

    /**
     * Returns the identifier of the authorized resource owner.
     *
     * @return string
     */
    public function getId()
    {
        return $this->getField('id');
    }

    /**
     * @return string
     */
    public function getDomain()
    {
        return $this->getField('domain');
    }

    /**
     * @return string
     */
    public function getUserName()
    {
        return $this->getField('userName');
    }

    /**
     * @return string
     */
    public function getFirstName()
    {
        return $this->getField('firstName');
    }

    /**
     * @return string
     */
    public function getLastName()
    {
        return $this->getField('lastName');
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->getField('emailPrimary');
    }

    /**
     * @return string
     */
    public function getPhone()
    {
        return $this->getField('phonePrimary');
    }

    /**
     * Return all of the owner details available as an array.
     *
     * @return array
     */
    public function toArray()
    {
        return $this->data;
    }

    /**
     * Returns a field from the Graph node data.
     *
     * @param $key
     * @return string
     */
    private function getField($key)
    {
        return isset($this->data[$key]) ? (string)$this->data[$key] : '';
    }
}