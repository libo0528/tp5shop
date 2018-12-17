<?php
// 应用公共文件
if(!function_exists('encrypt_password')){
    //定义密码加密函数
    function encrypt_password($password){
        //加盐方式 自定义的字符串内容
        $salt='sdkfjiow7e9ksdf';
        return md5(md5($password).$salt);
    }
}
//获取树形结构
if (!function_exists('getTree')) {
    //递归方法实现无限极分类
    function getTree($list,$pid=0,$level=0) {
        static $tree = array();
        foreach($list as $row) {
            if($row['pid']==$pid) {
                $row['level'] = $level;
                $tree[] = $row;
                getTree($list, $row['id'], $level + 1);
            }
        }
        return $tree;
    }
}
//富文本编辑器过滤标签
if (!function_exists('remove_xss')) {
    //使用htmlpurifier防范xss攻击
    /**
     * @param $string
     * @return string
     */
    function remove_xss($string){
        //相对index.php入口文件，引入HTMLPurifier.auto.php核心文件
        require_once './plugins/htmlpurifier/HTMLPurifier.auto.php';
        // 生成配置对象
        $cfg = HTMLPurifier_Config::createDefault();
        // 以下就是配置：
        $cfg -> set('Core.Encoding', 'UTF-8');
        // 设置允许使用的HTML标签
        $cfg -> set('HTML.Allowed','div,b,strong,i,em,a[href|title],ul,ol,li,br,p[style],span[style],img[width|height|alt|src]');
        // 设置允许出现的CSS样式属性
        $cfg -> set('CSS.AllowedProperties', 'font,font-size,font-weight,font-style,font-family,text-decoration,padding-left,color,background-color,text-align');
        // 设置a标签上是否允许使用target="_blank"
        $cfg -> set('HTML.TargetBlank', TRUE);
        // 使用配置生成过滤用的对象
        $obj = new HTMLPurifier($cfg);
        // 过滤字符串
        return $obj -> purify($string);
    }
}
//获取前台首页目录（树形结构）
if(!function_exists('get_cate_tree')){
    //递归方式实现 无限极分类树
    function get_cate_tree($list, $pid=0){
        $tree = array();
        foreach($list as $row) {
            if($row['pid']==$pid) {
                $row['son'] = get_cate_tree($list, $row['id']);
                $tree[] = $row;
            }
        }
        return $tree;
    }
}
//封装curl请求函数
if(!function_exists('curl_request'))
{
    //使用curl函数库放送请求
    function curl_request($url,$post=false,$params=[],$https=false)
    {
        //初始化请求会话
        $ch=curl_init($url);
        //调用curl_setopt()设置一些选项
        //post请求需要单独设置 （默认是get请求）
        if($post){
            //设置请求方式 post
            curl_setopt($ch,CURLOPT_POST,true);
            //设置请求参数
            curl_setopt($ch,CURLOPT_POSTFIELDS,$params);
        }
        //请求协议处理  https
        if($https){
            //禁止从服务器验证本地客户端的证书
            curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,false);
        }
        //发送请求
        //设置直接返回参数  通过curl_exec()直接返回
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
        $res=curl_exec($ch);
        //关闭请求
        curl_close($ch);
        return $res;
    }
}
//封装发送短信验证码的函数
if(!function_exists('sendmsg'))
{
    function sendmsg($phone,$msg){
    //调用短信接口 发送短信
    //检测 $phone 和 $msg 格式
        if(!preg_match('/^1[3-9]\d{9}$/',$phone)){
            return '参数错误';
        }
    //从配置文件读取接口信息
        $gateway=config('msg.gateway');
        $appkey=config('msg.appkey');
       //请求地址 get方式
        //$url="{$gateway}?mobile={$phone}&content={$msg}&appkey={$appkey}";
        $url=$gateway . '?mobile=' . $phone . '&content=' . $msg . '&appkey=' . $appkey;
        //echo $url;die;
        //发送请求
        $res=curl_request($url,false,[],true);
        //echo $res;
       /* //post请求方式 appkey是公共参数，必须放在url中
         $url="{$gateway}?appkey={$appkey}";
         $params=['mobile'=>$phone,'content'=>$msg];

         $res=curl_request($url,true,$params,true);*/
//         echo $res;die;
        if(!$res){
            //由于服务器原因，请求没有发出去
            return '请求出错';
        }
        //解析返回的结果
        $arr=json_decode($res,true);
        if(isset($arr['code']) && $arr['code']==10000){
            return true;
        }else{
            return '发送失败';
            //return $arr['msg'];
        }
    }
}
if(!function_exists('sendmail')){
    //使用phpmailer发送邮件
    function sendmail($email, $subject, $body)
    {
        //不传参数，表示不使用异常机制
        $mail = new \PHPMailer\PHPMailer\PHPMailer();

        //Server settings
        //$mail->SMTPDebug = 2;                                 // 调试输出
        $mail->isSMTP();                                      // 使用smtp服务
        $mail->Host = config('email.host');                        // 邮箱服务器地址
        $mail->SMTPAuth = true;                               // 开启SMTP认证
        $mail->Username = config('email.email');                 //发件箱账号
        $mail->Password = config('email.password');              // 发件箱密码（授权码）
        $mail->SMTPSecure = 'tls';                            // 安全加密方式 tls ssl
        $mail->Port = 25;                                    // 发送邮件端口
        $mail->CharSet = 'utf-8';                           //设置字符编码

        //Recipients
        $mail->setFrom(config('email.email')); //设置发件箱及昵称（昵称可以不设置）
        $mail->addAddress($email);     // 添加收件地址
//        $mail->addReplyTo('info@example.com', 'Information'); //回复人
//        $mail->addCC('cc@example.com'); //抄送人
//        $mail->addBCC('bcc@example.com');  //密送

        //Attachments
//        $mail->addAttachment('/var/tmp/file.tar.gz');         // 添加附件
//        $mail->addAttachment('/tmp/image.jpg', 'new.jpg');    // Optional name

        //Content
        $mail->isHTML(true);                     // 设置邮件内容格式html
        $mail->Subject = $subject;                      //邮件主题
        $mail->Body    = $body;                         //邮件内容
        //$mail->AltBody = 'This is the body in plain text for non-HTML mail clients';
        //发送邮件
        if($mail->send()){
            //发送成功
            return true;
        }else{
            return $mail->ErrorInfo;
        }
    }
}
if(!function_exists('encrypt_phone'))
{
    //加密手机号的函数  12312345678  -》123****5678
    function encrypt_phone($phone){
        //截取手机号
        return substr($phone,0,3)."****".substr($phone,7);
    }
}