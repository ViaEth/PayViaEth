<?php
class Etherscan_API {
    public function __construct($args=array()) { 
        $defaults = array(
            'endpoint' => '',
            'resource' => '',
            'apikey' => '',
        );

        $args       = array_merge($defaults, $args );
        foreach ($args as $key => $value) {
            $this->$key=$value;
        }

        // ob_start();
        // print_r($args);
        // echo PHP_EOL;
        // echo PHP_EOL;
        // echo PHP_EOL;
        // echo PHP_EOL;
        // $data1=ob_get_clean();
        // file_put_contents(dirname(__FILE__)  . '/args.log',$data1,FILE_APPEND);
        //please update this if you don't use 'endpoint' as your target url
        // $this->endpoint=constant('self::'. $args['endpoint']);
    }

    public function get_api_key_query_params(){
        return '&apikey=' . $this->apikey;
    }

    public function get_enther_price_url() {
        //https://etherscan.io/apis#stats
        $resource='module=stats&action=ethprice';
        return $this->endpoint . $resource . $this->get_api_key_query_params();
    }

    public function get_transaction_status_url($transaction_hash) {
        //https://etherscan.io/apis#stats
        $resource=sprintf('module=transaction&action=getstatus&txhash=%s', $transaction_hash );
        return $this->endpoint . $resource . $this->get_api_key_query_params();
    }

    public function get_latest_transactions_url($wallet_address, $number) {
        //https://etherscan.io/apis#stats
        $order='desc';
        $resource=sprintf('module=account&action=txlist&address=%s&startblock=0&endblock=99999999&page=1&offset=%s&sort=%s', $wallet_address, $number, $order);
        return $this->endpoint . $resource . $this->get_api_key_query_params();
    }
    
    public function get_enther_price() {
        //https://etherscan.io/apis#stats
        $body=array();
        return $this->request('module=stats&action=ethprice',$body,'GET');
    }

    public function get_transaction_status($transaction_hash) {
        //https://etherscan.io/apis#transactions
        // https://api-kovan.etherscan.io/api?module=transaction&action=getstatus&txhash=0x8541e2bb2f9b297822d98718695cca9d492387dfb9dc8e012637f64cf96967de&apikey=U3M41P7XQ2GCX56ZPUVND61W6V98E38RQU
        // {"status":"1","message":"OK","result":{"isError":"0","errDescription":""}}
        $body=array();
        $resource=sprintf('module=transaction&action=getstatus&txhash=%s', $transaction_hash );
        return $this->request($resource,$body,'GET');
    }

    public function get_latest_transactions($wallet_address, $number=20) {
        //https://etherscan.io/apis#accounts
        if(empty($number)){
            $number=20;
        }
        // if(empty($order)){
        $order='desc';
        // }

        $body=array();
        $resource=sprintf('module=account&action=txlist&address=%s&startblock=0&endblock=99999999&page=1&offset=%s&sort=%s', $wallet_address, $number, $order);
        return $this->request($resource, $body, 'GET');
    }

    public function request( $resource, $body, $method = 'POST', $options = array() ) {
        // set default options
        $timeout=0.6 * ini_get('max_execution_time');
        if(empty($timeout)){
            $timeout=30;
        }

        $options = wp_parse_args( $options, array(
            'method'  => $method,
            'timeout' => $timeout,
            'body'    => in_array( $method, array( 'GET') ) ? null : json_encode( $body ),
            'headers' => array(),
            // 'sslverify' => false,
        ) );

        // set default header options
        $options['headers'] = wp_parse_args( $options['headers'], array(
            'Content-Type' => 'application/json; charset=UTF-8',
            // 'Authorization' => 'Basic ' . base64_encode( $this->username . ':' . $this->password )
        ) );

        // WP docs say method should be uppercase
        $options['method'] = strtoupper( $options['method'] );

        if(!empty($this->resource)){
            $resource=$this->resource;
        }

        $url  = $this->endpoint . $resource . $this->get_api_key_query_params();

        $result = wp_remote_request( $url, $options );

        // ob_start();
        // print_r($url);
        // echo PHP_EOL;
        // print_r($result);
        // echo PHP_EOL;
        // echo PHP_EOL;
        // echo PHP_EOL;
        // echo PHP_EOL;
        // $data1=ob_get_clean();
        // file_put_contents(dirname(__FILE__)  . '/url.log',$data1,FILE_APPEND);
        
        if ( is_wp_error( $result ) ) {
            throw new Exception( 'Request failed. '. $result->get_error_message() );
        }else{
            if ( $result['response']['code'] == 400 ) {
                throw new Exception( 'Input is in the wrong format.' );
            } elseif ( $result['response']['code'] == 401 ) {
                throw new Exception( 'API credentials invalid.' );          
            } else  if($result['response']['code'] != 200){
                throw new Exception( sprintf( '%s: %s', $result['response']['code'], $result['response']['message'] ) );
            }
        }
        return json_decode( $result['body'], true );
    }
}
