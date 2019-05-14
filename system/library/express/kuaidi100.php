<?php
class kuaidi100
{
    public function getTracking($params = array()) {
        $post_data = array();
        $post_data['customer'] = $params['app_id'];
        $key = $params['app_key'] ;
        $post_data["param"] = json_encode(array(
            'com'   => $params['express_code'],
            'num'   => $params['express_num'],
        ));

        $url='http://poll.kuaidi100.com/poll/query.do';
        $post_data['sign'] = strtoupper(md5($post_data['param'] . $key . $post_data['customer']));

        $o="";
        foreach ($post_data as $k=>$v)
        {
            $o.= "$k=".urlencode($v)."&";
        }
        $post_data = substr($o, 0, -1);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
        $result = curl_exec($ch);
        $data = str_replace("\"",'"',$result );

        $result = json_decode($data,true);
        if (!emtpy($result['data'])){
            $ret = array();
            foreach($result['data'] as $row){
                $ret[] = array(
                    'accept_time'       => $row['ftime'],
                    'accept_station'    => $row['context'],
                    'remark'            => '',
                );
            }
        }else{
            return array();
        }
    }
}