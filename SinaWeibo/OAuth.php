<?php
/**
 * OAuth warppaer for SinaWeibo
 *
 * @author nightsailer @nightsailer
 * @copyright Copyright 2010, Pan Fan(nightsailer). (http://nightsailer.com/)
 * @license GPLv3 (http://www.gnu.org/licenses/gpl-3.0.txt)
 */
class SinaWeibo_OAuth {
    public $host = 'http://api.t.sina.com.cn';
    public $format = 'json';
    public $decode_json = true;
    public $http_info;
    public $access_token_url = 'http://api.t.sina.com.cn/oauth/access_token';
    public $authenticate_url = 'http://api.t.sina.com.cn/oauth/authenticate';
    public $authorize_url    = 'http://api.t.sina.com.cn/oauth/authorize';
    public $request_token_url= 'http://api.t.sina.com.cn/oauth/request_token';

    protected static $http_method_constants = array(
        'POST' => OAUTH_HTTP_METHOD_POST,
        'PUT'  => OAUTH_HTTP_METHOD_PUT,
        'GET'  => OAUTH_HTTP_METHOD_GET,
        'DELETE' => OAUTH_HTTP_METHOD_DELETE,
        'HEAD' => OAUTH_HTTP_METHOD_HEAD,
    );

    private $last_api_call;
    private $consumer_key;
    private $consumer_secret;
    private $oauth;
    private $token;

    public function __construct($consumer_key, $consumer_secret, $oauth_token = NULL, $oauth_token_secret = NULL) {
        $this->consumer_key = $consumer_key;
        $this->consumer_secret = $consumer_secret;
        $this->oauth = new OAuth($this->consumer_key,$this->consumer_secret,OAUTH_SIG_METHOD_HMACSHA1,OAUTH_AUTH_TYPE_AUTHORIZATION);
        $this->oauth->setRequestEngine(OAUTH_REQENGINE_CURL);
        if (!empty($oauth_token) && !empty($oauth_token_secret)) {
            $this->set_token($oauth_token,$oauth_token_secret);
        }
    }

    /**
     * 获取未授权的Request Token
     *
     * @param string $oauth_callback_url
     * @return array
     */
    public function get_request_token($oauth_callback_url=NULL) {
        $oauth = $this->oauth;
        try {
            $response = $oauth->getRequestToken($this->request_token_url,$oauth_callback_url);
            if (!empty($response)) {
                $this->token = array(
                    'oauth_token' => $response['oauth_token'],
                    'oauth_token_secret' => $response['oauth_token_secret'],
                );
            }
            else {
                throw new SinaWeibo_Exception('Failed fetching request token: response was: '. $oauth->getLastResponse());
            }
            return $this->token;
        } catch (OAuthException $e) {
            throw new SinaWeibo_Exception('OAuth Exception,Error:'.$e->getMessage());
        }
    }

    /**
     * 生成一个url,用于请求用户授权Token
     *
     * @param array $token request token
     * @param string $callback_url
     * @param string $sign_in_with_weibo 是否登录到微博
     * @return string
     */
    public function get_authorize_url($token,$callback_url,$sign_in_with_weibo = TRUE) {
        if (is_array($token)) {
            $token = $token['oauth_token'];
        }
        if (!$sign_in_with_weibo) {
            return $this->authorize_url."?oauth_token={$token}&oauth_callback=".urlencode($callback_url);
        } else {
            return $this->authenticate_url."?oauth_token={$token}&oauth_callback=".urlencode($callback_url);
        }
    }

    /**
     * 获取授权过的Access Token
     *
     * @param string $oauth_verifier
     * @param array $token
     * @return array
     */
    public function get_access_token($oauth_verifier=null,$token = null) {
        $oauth = $this->oauth;
        try {
            if (empty($token)) {
                $token = $this->token;
            }
            $oauth->setToken($token['oauth_token'],$token['oauth_token_secret']);
            $access_token_info = $oauth->getAccessToken($this->access_token_url,null,$oauth_verifier);
            if (!empty($access_token_info)) {
                $this->token = array(
                    'oauth_token' => $access_token_info['oauth_token'],
                    'oauth_token_secret' => $access_token_info['oauth_token_secret'],
                );
            }
            else {
                throw new SinaWeibo_Exception("Failed fetching access token, response was: " . $oauth->getLastResponse());
            }
            return $this->token;
        } catch (OAuthException $e) {
            throw new SinaWeibo_Exception('OAuth Exception,Error:'.$e->getMessage());
        }
    }

    /**
     * 执行一个Rest请求
     *
     * @param string $url
     * @param array $parameters
     * @param string $http_method HTTP METHOD
     * @param bool $multipart
     * @return mixed
     */
    public function oauth_fetch($url,$parameters=array(),$http_method=null) {
        if (strrpos($url, 'http://') !== 0 && strrpos($url, 'https://') !== 0) {
            $url = "{$this->host}{$url}.{$this->format}";
        }
        if (!empty($http_method) && in_array($http_method,self::$http_method_constants)) {
            $http_method = self::$http_method_constants[$http_method];
        }
        else {
            $http_method = OAUTH_HTTP_METHOD_POST;
        }
        $oauth = $this->oauth;
        try {
            if (!$oauth->fetch($url,$parameters,$http_method)) {
                throw new SinaWeibo_Exception("Failed fetching resource:$url, response was: " . $oauth->getLastResponse());
            }
        } catch (OAuthException $e) {
            var_dump($e);
            $this->http_info = $oauth->getLastResponseInfo();
            throw new SinaWeibo_Exception('OAuth Exception,Error:'.$e->getMessage());
        }
        $response = $oauth->getLastResponse();
        if ($this->format === 'json' && $this->decode_json) {
            return json_decode($response, true);
        }
        return $response;
    }

    /**
     * GET wrappwer for oauth_fetch.
     *
     * @return mixed
     */
    public function get($url, $parameters = array()) {
        return $this->oauth_fetch($url,$parameters,'GET');
    }

    /**
     * POST wrapper for oauth_fetch.
     *
     * @return mixed
     */
    public function post($url, $parameters = array()) {
        return $this->oauth_fetch($url,$parameters,'POST');
    }

    /**
     * DELETE wrapper for oauth_fetch.
     *
     * @return mixed
     */
    public function delete($url, $parameters = array()) {
        return $this->oauth_fetch($url,$parameters,'DELETE');
    }

    public function set_token($oauth_token,$oauth_token_secret) {
        $this->token['oauth_token'] = $oauth_token;
        $this->token['oauth_token_secret'] = $oauth_token_secret;
        $this->oauth->setToken($oauth_token,$oauth_token_secret);
    }

    public function debug($enabled=true) {
        $this->oauth->debug=$enabled;
    }
}
?>