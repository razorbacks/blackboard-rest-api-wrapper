<?php

namespace razorbacks\blackboard\rest;

use Exception;

class BadResponse extends Exception
{
    protected $json;

    public function json(string $json = null)
    {
        if (is_null($json)) {
            return json_decode($this->json, true);
        }

        $this->json = $json;
    }
}
