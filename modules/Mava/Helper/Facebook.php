<?php
class Mava_Helper_Facebook
{
    /**
     * @param string $url
     * @param string $code
     * @return array|false Array of info (may be error); false if Facebook integration not active
     */
    public static function getAccessToken($url, $code){
        $options = Mava_Application::get('options');

        if(!$options->facebookAppId){
            return false;
        }

        try{
            $token = Mava_Helper_CURL::call(
                Mava_Helper_CURL::METHOD_GET,
                'https://graph.facebook.com/oauth/access_token',
                array(
                    'client_id' => $options->facebookAppId,
                    'redirect_uri' => $url,
                    'client_secret' => $options->facebookAppSecret,
                    'code' => $code
                )
            );



            if(preg_match('#^[{\[]#', $token)){
                return json_decode($token, true);
            }else{
                return Mava_Application::parseQueryString($token);
            }
        }catch(Mava_Exception $e){
            return false;
        }
    }

    /**
     * @param string $url
     * @param string $code
     * @return array|false Array of info (may be error); false if Facebook integration not active
     */
    public static function getLongLivedAccessToken($shortLivedToken){
        $options = Mava_Application::get('options');

        if(!$options->facebookAppId){
            return false;
        }

        try{
            $token = Mava_Helper_CURL::call(
                Mava_Helper_CURL::METHOD_GET,
                'https://graph.facebook.com/oauth/access_token',
                array(
                    'grant_type' => 'fb_exchange_token',
                    'client_id' => $options->facebookAppId,
                    'client_secret' => $options->facebookAppSecret,
                    'fb_exchange_token' => $shortLivedToken
                )
            );



            if(preg_match('#^[{\[]#', $token)){
                return json_decode($token, true);
            }else{
                return Mava_Application::parseQueryString($token);
            }
        }catch(Mava_Exception $e){
            return false;
        }
    }

    public static function getAccessTokenFromCode($code, $redirectUri = false)
    {
        if (!$redirectUri)
        {
            $redirectUri = preg_replace('#(&|\?)code=[^&]*#', '', Mava_Url::getCurrentAddress());
        }
        else
        {
            // FB does this strange thing with slashes after a ? for some reason
            $parts = explode('?', $redirectUri, 2);
            if (isset($parts[1]))
            {
                $redirectUri = $parts[0] . '?' . str_replace('/', '%2F', $parts[1]);
            }
        }
        return self::getAccessToken($redirectUri, $code);
    }

    /**
     * @param $accessToken
     * @param string $path
     * @param array $data
     * @return bool|mixed
     */
    public static function getUserInfo($accessToken, $path = 'me', $data = array())
    {
        try
        {
            if($accessToken){
                $data['access_token'] = $accessToken;
            }
            $info = Mava_Helper_CURL::call(
                Mava_Helper_CURL::METHOD_GET,
                'https://graph.facebook.com/' . $path,
                $data
            );

            if(preg_match('#^[{\[]#', $info)){
                return json_decode($info, true);
            }else{
                return Mava_Application::parseQueryString($info);
            }
        }
        catch (Mava_Exception $e)
        {
            return false;
        }
    }

    /**
     * Gets the user picture for the current user.
     *
     * @param string $accessToken FB access token (from code swap, or given by user)
     * @param string $size Size/type of picture. Defaults to large
     *
     * @return string Binary data
     */
    public static function getUserPicture($accessToken, $size = 'large')
    {
        try
        {
            $pictureBinary = Mava_Helper_CURL::call(
                Mava_Helper_CURL::METHOD_GET,
                'https://graph.facebook.com/me/picture?type=' . $size,
                array(
                    'access_token' => $accessToken
                )
            );

            return $pictureBinary;
        }
        catch (Mava_Exception $e)
        {
            return false;
        }
    }

    /**
     * Sets the fbUid cookie that is used by the JavaScript.
     *
     * @param integer $fbUid 64-bit int of FB user ID
     */
    public static function setUidCookie($fbUid)
    {
        Mava_Helper_Cookie::setCookie('fbUid', $fbUid, 14 * 86400);
    }

    /**
     * Gets the URL to request Facebook permissions.
     *
     * @param string $redirectUri URL to return to
     * @param string|null $appId Facebook app ID
     * @param string|null $state CSRF Protection
     *
     * @return string
     */
    public static function getFacebookRequestUrl($redirectUri, $appId = null, $state = null)
    {
        $perms = 'email,public_profile,user_friends';

        if (!$appId)
        {
            $appId = Mava_Application::get('options')->facebookAppId;
        }

        if (!$state)
        {
            $state = md5(uniqid('xf', true));
        }

        $session = Mava_Application::getSession();
        $session->set('fbCsrfState', $state);

        return 'https://www.facebook.com/dialog/oauth?client_id=' . $appId
        . '&scope=' . $perms
        . '&state=' . $state
        . '&redirect_uri=' . urlencode($redirectUri);
    }

    public static function getFacebookRequestErrorInfo($result, $expectedKey = false)
    {
        if (!$result)
        {
            return __('your_server_could_not_connect_to_facebook');
        }
        if (!is_array($result))
        {
            return __('facebook_returned_unknown_error');
        }
        if (!empty($result['error']['message']))
        {
            return __('facebook_returned_following_error_x', array('error' => $result['error']['message']));
        }
        if ($expectedKey && !isset($result[$expectedKey]))
        {
            return __('facebook_returned_unknown_error');
        }
        return false;
    }
}