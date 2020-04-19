<?php
/*
 * @Wibuheker | Curl Class
 */
class Curl {
    public  $URL = null;
    public  $ch;
    public function __construct()
    {
        $this->ch = curl_init();
        curl_setopt ($this->ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt ($this->ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt ($this->ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt ($this->ch, CURLOPT_HEADER, 1);
    }
    public  function SetHeaders($header)
    {
        curl_setopt ($this->ch, CURLOPT_HTTPHEADER, $header);
    }
    public  function setTimeout($timeout)
    {
        curl_setopt ($this->ch, CURLOPT_TIMEOUT, $timeout);
		curl_setopt ($this->ch, CURLOPT_CONNECTTIMEOUT,$timeout);
    }
    public  function Cookies($file_path)
    {
        $fp = fopen($file_path, 'wb');
        fclose($fp);
        curl_setopt ($this->ch, CURLOPT_COOKIEJAR, $file_path);
        curl_setopt ($this->ch, CURLOPT_COOKIEFILE, $file_path);
    }
    public  function Follow()
    {
        curl_setopt ($this->ch, CURLOPT_FOLLOWLOCATION, 1);
    }
    public  function Post($data) 
    {
        curl_setopt ($this->ch, CURLOPT_URL, $this->URL);
        curl_setopt ($this->ch, CURLOPT_POST, 1);	
		curl_setopt ($this->ch, CURLOPT_POSTFIELDS, $data);
    }
    public  function Get()
    {
        curl_setopt ($this->ch, CURLOPT_URL, $this->URL);
        curl_setopt ($this->ch, CURLOPT_POST, 0);
    }
    public  function Response()
    {
        $data = curl_exec ($this->ch);
        $header_size = curl_getinfo($this->ch, CURLINFO_HEADER_SIZE);
		$status_code = curl_getinfo($this->ch, CURLINFO_HTTP_CODE);
		$head = substr($data, 0, $header_size);
		$body = substr($data, $header_size);
        return json_decode(json_encode(
            array(
                'status_code' => $status_code,
                'headers' => self::HeadersToArray($head),
                'body' => $body
            )
            ));
    }
    public  function HeadersToArray($str) {
        $str = explode("\r\n", $str);
        $str = array_splice($str, 0, count($str) - 1);
        $output = [];
        foreach($str as $item) {
            if ($item === '' || empty($item)) continue;
            $index = stripos($item, ": ");
            $key = substr($item, 0, $index);
            $key = strtolower(str_replace('-', '_', $key));
            $value = substr($item, $index + 2);
            if (@$output[$key]) {
                if (strtolower($key) === 'set_cookie') {
                    $output[$key] = $output[$key] . "; " . $value; 
                } else {
                    $output[$key] = $output[$key];
                }
            } else {
                $output[$key] = $value;
            }
        }
        return $output;
    }
    
}
