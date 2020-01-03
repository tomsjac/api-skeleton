<?php

namespace App\controller\auth;

use App\controller\AbstractController;
use App\services\tools\auth\TokenJwt;
use DateTime;
use Exception;
use Interop\Container\Exception\ContainerException;
use Slim\Http\Response;

/**
 * Authentication management JWT
 *
 * @author thomas
 */
class Jwt extends AbstractController
{
    /**
     * Get token / no token verification : passthrough in Conf
     * Method : POST
     * @return bool|Response
     * @throws ContainerException
     * @throws Exception
     */
    public function token()
    {
        //Check Access
        if (($error = $this->checkAccess(null, ['POST'])) !== true) {
            return $error;
        }

        //Limit Rate (100 requests in 60 minutes)
        if (
            $this->getContainer('settings')['mode'] == 'production'
            && ($error = $this->checkRateLimit(100, 60)) !== true
        ) {
            return $error;
        }


        //Connect BDD / Model


        // Check Data Query clientId / clientSecret with $request->getParams()
        $data  = [];
        $scope = [];

        $token = $this->generateToken($data, $scope);

        //Reponse
        return $this->getApiResponse()
                ->setStatus(201)
                ->writeRaw(['token' => $token]);
    }

    /**
     * Refresh token with a new token
     * Method : PUT
     * @throws ContainerException
     * @throws Exception
     */
    public function refresh()
    {
        //Check Access
        if (($error = $this->checkAccess(null, ['PUT'])) !== true) {
            return $error;
        }

        //Refresh  => New Token
        $token    = $this->getContainer('token');
        $newToken = $this->generateToken($token->getData(), $token->getScope());

        return $this->getApiResponse()
                ->setStatus(201)
                ->writeRaw(['token' => $newToken]);
    }

    /**
     * Return info of the token
     * Method : GET
     * @return Response
     * @throws ContainerException
     */
    public function info()
    {
        //Check Access
        if (($error = $this->checkAccess(null, ['GET'])) !== true) {
            return $error;
        }

        //object Token
        $token = $this->getContainer('token');

        if ($token->isInit() == true) {
            return $this->getApiResponse()
                    ->setStatus(201)
                    ->write([
                        'data' => $token->getData(),
                        'scope' => $token->getScope(),
                        'expires' => $token->getExpired()
            ]);
        }
    }

    /**
     * Generate Token
     * @param array $data
     * @param array $scope
     * @return string
     * @throws Exception
     */
    private function generateToken($data, $scope)
    {
        $optionJwt = $this->container['settings']['jwt'];

        $future = new DateTime("now +".$optionJwt['expire']." second");
        $secret = $optionJwt['secret'];

        return TokenJwt::generate($data, $scope, $future->getTimeStamp(), $secret);
    }
}
