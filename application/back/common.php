<?php



 /**
  * @param string $url
  * @param array $vars
  * @param $field=''    需要生成url排序的 字段
  * @param $order=[]     当前的排序的 参数 比如 [type=>asc,$field=>title] 按标题排序 升序排序
  * @param bool $suffix
  * @param bool $domain
  * @return string
  */
 function urlOrder($url = '', $vars = [],$field='',$order=[], $suffix = true, $domain = false)
 {
    //分析排序的参数u 加入到url
     //要按照字段的排序
     $vars['order_field'] = $field;
     if (isset($order['field']) && isset($order['type']) && $field == $order['field'] && $order['type'] == 'asc'){
     //根据当前的排序方式  确定是升序还是降序
         // 已经按照当前字段排序了
         $vars['order_type'] = 'desc';
     }

     else{
         $vars['order_type'] = 'asc';
     }
     return url($url,$vars,$suffix,$domain);
 }
 function classOrder($field,$order=[])
 {
     if (isset($order['type'])&& isset($order['field']) && $field == $order['field']){
         return $order['type'];
     }else{
         return '';
     }
 }

