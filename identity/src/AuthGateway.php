<?php
/**
 * Created by PhpStorm.
 * User: Ouachhal
 * Date: 17/08/2017
 * Time: 09:27
 */

namespace JPuminate\Auth\Identity;


use Exception;
use Firebase\JWT\BeforeValidException;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\JWT;
use Firebase\JWT\SignatureInvalidException;
use Illuminate\Encryption\Encrypter;
use Illuminate\Http\Request;
use JPuminate\Auth\Identity\Exceptions\AuthGatewayException;
use Psy\Exception\RuntimeException;

class AuthGateway
{

    private $public_key;
    private $encrypter;

    public function __construct($public_key = null, Encrypter $encrypter = null)
    {
        $this->public_key = $public_key;
        $this->encrypter = $encrypter;
    }

    public function setPublicKey($public_key){
        $this->public_key = $public_key;
    }

    public function setEncrypter( Encrypter $encrypter){
        $this->encrypter = $encrypter;
    }

    public function validateAuthenticatedRequest(Request $request)
    {
        $jwt = $request->bearerToken();
        $token = $this->parseToken($jwt);
        $request->request->add([
            'oauth_access_token_id' => $token->getClaim('jti'),
            'oauth_client_id' => $token->getClaim('aud'),
            'oauth_user_id' => $token->getClaim('sub'),
            'oauth_scopes' => $token->getClaim('scopes')
        ]);
        $domain = $token->getClaim('sub') != "" ? AuthDomain::$USERS : AuthDomain::$CLIENTS;
        return array($token, $domain);
    }


    public function parseToken($jwt){
        try {
            $token = new OAuthToken(JWT::decode($jwt, $this->public_key, array('RS256')));
            return $token;
        }
        catch (ExpiredException $e){
            throw AuthGatewayException::expiredToken();
        }
        catch(Exception $e){
            throw AuthGatewayException::accessDenied('Error while decoding to JSON');
        }
    }


}