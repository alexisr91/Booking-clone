<?php

namespace App\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Request;

class CookieController extends AbstractController
{
    /** Controller cookie 
     * 
     * @Route("/set-cookie", name="set_cookie")
     */
    public function setCookie(): Response
    {
        $response = new Response('Cookie set!');
        $cookie = new Cookie('cookie', 'cookie_value', strtotime('+1 day'),'/',null,false,false);
        $response->headers->setCookie($cookie);
        
        return $response;

    }

    /**
     * We get the cookie from the dependency injection HTTP Foundation
     * 
     * @Route("/get-cookie", name="get_cookie")
     */
    public function getCookie(Request $request)
    {
        $cookieValue= $request->cookies->get('cookie');
        return new Response('Cookie value: '. $cookieValue);

    }


}
