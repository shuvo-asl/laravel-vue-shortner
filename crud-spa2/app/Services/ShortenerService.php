<?php
namespace App\Services;
use App\Models\Shortner;
trait ShortenerService
{
    protected static $chars = "abcdefghijklmnopqrstuvwxyz|ABCDEFGHIJKLMNOPQRSTUVWXYZ|0123456789";
    protected static $checkUrlExists = true;
    protected static $codeLength = 6;


    public function urlToShortCode($url){
        if(empty($url)){
            throw new \Exception("No URL was supplied.");
        }

        if($this->validateUrlFormat($url) == false){
            throw new \Exception("URL does not have a valid format.");
        }

        if(self::$checkUrlExists){
            if (!$this->verifyUrlExists($url)){
                throw new \Exception("URL does not appear to exist.");
            }
        }

        $shortUrl = $this->urlExistsInDB($url);
        if($shortUrl == false){
            $shortUrl = $this->createShortCode($url);
        }

        return $shortUrl;
    }

    protected function validateUrlFormat($url){
        return filter_var($url, FILTER_VALIDATE_URL, FILTER_FLAG_HOST_REQUIRED);
    }

    protected function verifyUrlExists($url){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_NOBODY, true);
        curl_setopt($ch,  CURLOPT_RETURNTRANSFER, true);
        curl_exec($ch);
        $response = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        return (!empty($response) && $response != 404);
    }

    protected function urlExistsInDB($url){
        $shortner = Shortner::where('long_url',$url)->first();
        if(is_null($shortner)){
            return false;
        }else{
            return route('url_redirect',[$shortner->short_code]);
        }
    }

    protected function createShortCode($url){
        $shortCode = $this->generateRandomString(self::$codeLength);
        $short_url = $this->insertUrlInDB($url, $shortCode);
        return $short_url;
    }

    protected function generateRandomString($length = 6){
        $sets = explode('|', self::$chars);
        $all = '';
        $randString = '';
        foreach($sets as $set){
            $randString .= $set[array_rand(str_split($set))];
            $all .= $set;
        }
        $all = str_split($all);
        for($i = 0; $i < $length - count($sets); $i++){
            $randString .= $all[array_rand($all)];
        }
        $randString = str_shuffle($randString);
        return $randString;
    }

    protected function insertUrlInDB($url, $code){
        $shortner = Shortner::create([
            "long_url" => $url,
            "short_code" => $code,
        ]);

        return route('url_redirect',[$code]);;
    }

    public function shortCodeToUrl($code){
        if(empty($code)) {
            throw new \Exception("No short code was supplied.");
        }

        if($this->validateShortCode($code) == false){
            throw new \Exception("Short code does not have a valid format.");
        }

        $urlRow = Shortner::where('short_code',$code)->first();
        if(empty($urlRow)){
            throw new \Exception("Short code does not appear to exist.");
        }

        return $urlRow->long_url;
    }

    protected function validateShortCode($code){
        $rawChars = str_replace('|', '', self::$chars);
        return preg_match("|[".$rawChars."]+|", $code);
    }

}
