<?php

namespace App\Tests;

use App\Controller\CoffeeController;
use Symfony\Component\HttpKernel\HttpKernelBrowser;
use Symfony\Component\HttpFoundation\Request;
use PHPUnit\Framework\TestCase;

use Symfony\Component\HttpClient\HttpClient;


class UserTest extends  TestCase
{
    /**
     * Check if the customer user is loged with username => customer, password => customer123
     */

    public function testLoginCheck()
    {

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => "http://docker.for.mac.localhost/api/login_check",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_POSTFIELDS => "{\n\t\"username\": \"customer\",\n\t\"password\": \"customer123\"\n}",
            CURLOPT_HTTPHEADER => array(
                "Content-Type: application/json"
            ),
        ));

        curl_exec($curl);

        $http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);

        curl_close($curl);

        $this->assertEquals(200, $http_code);
    }


}