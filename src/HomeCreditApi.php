<?php
namespace HomeCreditApi;

/**
 * Description of HomeCreditApi
 *
 * @author bbtrash
 */
class HomeCreditApi {
    
    // train enviroment (cz version)
    static $trainURL = 'https://apicz-test.homecredit.net/verdun-train';
    static $user = '024243tech';
    static $password = '024243tech';
    
    // production (cz version)
    static $prodURL = 'https://api.homecredit.cz';
    
    // currency (czk)
    static $currency = 'CZK';
    
    var $apiURL;
    
    
    public function __construct() {
        
    }
    
    public function setProduction($user,$pass){
        $this->apiURL = self::$prodURL;
        
        self::$user = $user;
        self::$password = $pass;
        
    }
    
    public function setTrain(){
        $this->apiURL = self::$trainURL;
    }
    
    
    public function token(){
        $url = $this->apiURL . '/authentication/v1/partner/';
        $post = [
            'username' => self::$user,
            'password' => self::$password
        ];
        return $this->sendCurl($url, $post)['accessToken'];
    }
    
    
    private function sendCurl($url,$post,$token = false){
        
        $ch = curl_init($url);
        
        $header = [
            'Content-Type: application/json',
            'Charset: utf-8'
        ];
        
        if ($token ==! false)
        {
            $header[] = 'Authorization: Bearer ' . $token;
        }
        
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLINFO_HEADER_OUT, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($post));
        curl_setopt($ch,CURLOPT_HTTPHEADER,$header); 
        $result = curl_exec($ch);
        $response = json_decode( $result,true );
        if (!curl_errno($ch)) {
            $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            if ($http_code != 200) {
                echo 'Unexpected HTTP code: ', $http_code, ' ,  response: '.print_r($response,true).', error: '. curl_error($ch).' <br />';
            }
        }
        curl_close($ch);
        return $response;
    }
    
    
    public function createApplication($data)
    {
        $url = $this->apiURL . '/financing/v1/applications';

        $return = $this->sendCurl($url, $data, $this->token());
        
        echo "ID:".$return["id"], '<br />';
        $soubor = fopen("./applicationID.txt", "w+");
        fwrite($soubor, $return["id"]);
        fclose($soubor);

        //echo $responze["gatewayRedirectUrl"] ;
        echo "<a href='".$return["gatewayRedirectUrl"]."'>Odkaz</a>";
    }
    
}
