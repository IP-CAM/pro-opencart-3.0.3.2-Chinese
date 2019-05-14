<?php
class kdniao
{
    public function getTracking($params){
        $requestData= json_encode(array(
            'ShipperCode'   => $params['express_code'],
            'LogisticCode'  => $params['express_num'],
        ));

        $datas = array(
            'EBusinessID'   => $params['app_id'],
            'RequestType'   => '1002',
            'RequestData'   => urlencode($requestData) ,
            'DataType'      => '2',
            'DataSign'      => urlencode(base64_encode(md5($requestData . $params['app_key'])))
        );


        if ($params['app_id'] == 'test1474089') { //test account
            $url = 'http://sandboxapi.kdniao.com:8080/kdniaosandbox/gateway/exterfaceInvoke.json';
        }else{
            $url = 'http://api.kdniao.com/Ebusiness/EbusinessOrderHandle.aspx';
        }

        $temps = array();
        foreach ($datas as $key => $value) {
            $temps[] = sprintf('%s=%s', $key, $value);
        }
        $post_data = implode('&', $temps);
        $url_info = parse_url($url);
        if (empty($url_info['port'])) {
            $url_info['port']=80;
        }
        $httpheader = "POST " . $url_info['path'] . " HTTP/1.0\r\n";
        $httpheader.= "Host:" . $url_info['host'] . "\r\n";
        $httpheader.= "Content-Type:application/x-www-form-urlencoded\r\n";
        $httpheader.= "Content-Length:" . strlen($post_data) . "\r\n";
        $httpheader.= "Connection:close\r\n\r\n";
        $httpheader.= $post_data;
        $fd = fsockopen($url_info['host'], $url_info['port']);
        fwrite($fd, $httpheader);
        $gets = "";

        while (!feof($fd)) {
            if (($header = @fgets($fd)) && ($header == "\r\n" || $header == "\n")) {
                break;
            }
        }
        while (!feof($fd)) {
            $gets.= fread($fd, 128);
        }
        fclose($fd);

        $result = @json_decode($gets,true);

        if ($result['Success']) {
            $ret = array();
            foreach($result['Traces'] as $row) {
                $ret[] = array(
                    'accept_time'       => $row['AcceptTime'],
                    'accept_station'    => $row['AcceptStation'],
                    'remark'            => $row['Remark'],
                );
            }

            return $ret;
        }else{
            return array();
        }

    }
}