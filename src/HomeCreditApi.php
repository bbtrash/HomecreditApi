<?php
namespace HomeCreditApi;

/**
 * Description of HomeCreditApi
 * 
 * https://csoneclicknew.docs.apiary.io/
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
    var $accessToken;


    private static $error;

    
    public function __construct() {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
    }
    
    public function setProduction($user,$pass){
        $this->apiURL = self::$prodURL;
        
        self::$user = $user;
        self::$password = $pass;
        
    }

    /**
     * Store token in session
     * @param $token
     */
    public function setAccessToken($token){
        $_SESSION['homeCreditAccessToken'] = $token;
    }
    

    public function setTrain(){
        $this->apiURL = self::$trainURL;
    }

    public function getError(){



    }
    
    
    public function token(){
        $url = $this->apiURL . '/authentication/v1/partner/';
        $post = [
            'username' => self::$user,
            'password' => self::$password
        ];

        $accessToken = $this->sendCurl($url, $post)['accessToken'];

        return $accessToken;
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
    
    
    public function testData()
    {
        $finalData = [
            'customer' => [
              'firstName' => 'Jaroslav',
              'lastName' => 'Trener',
              'email' => 'Jar.Trener954@sezznamm.cz',
              'phone' => '+420765787435',
              'addresses' => [
                0 => [
                  'city' => 'Brno',
                  'streetAddress' => 'Holandská',
                  'streetNumber' => '510',
                  'zip' => '60500',
                  'addressType' => 'PERMANENT',
                ],
              ],
            ],
            'order' => [
              'number' => '57834704124',
              'variableSymbols' => [
                0 => '989595',
              ],
              'totalPrice' => [
                'amount' => 200000,
                'currency' => 'CZK',
              ],
              'items' => [
                0 => [
                  'code' => '5202',
                  'ean' => '9999545',
                  'name' => 'iPhone 6s 32GB SpaceGray',
                  'quantity' => 1,
                  'totalPrice' => [
                    'amount' => 200000,
                    'currency' => 'CZK',
                  ],
                  'image' => [
                    'filename' => 'iphone6s.jpg',
                    'url' => 'https://i.cdn.nrholding.net/32523771/2000/2000/iphone6s.jpg',
                  ],
                ],
              ],
            ],
            'type' => 'INSTALLMENT',
            'settingsInstallment' => [
                  'preferredMonths' => 0,
                  'preferredInstallment' => [
                    'amount' => 26800,
                    'currency' => 'CZK',
                  ],
                  'preferredDownPayment' => [
                    'amount' => 0,
                    'currency' => 'CZK',
                  ],
                  'productCode' => 'COCONL08',
                  'productSetCode' => 'COCHCONL',
            ],
            'agreementPersonalDataProcessing' => true,
            'merchantUrls' => [
              'approvedRedirect' => 'http://localhost9',
              'rejectedRedirect' => 'http://localhost',
              'notificationEndpoint' => 'http://uzjepekne.cz',
            ],
          ];
        
        return $finalData;
    }
    
}
