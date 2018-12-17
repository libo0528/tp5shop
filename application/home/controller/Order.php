<?php

namespace app\home\controller;

use think\Controller;
use think\Request;
use app\home\model\Order as OrderModel;
use app\home\model\Address as AddressModel;
use app\home\model\Goods as GoodsModel;
use app\home\model\Cart as CartModel;
use app\home\model\OrderGoods as  OrderGoodsModel;
class Order extends Base
{
    public function index(){
        return view();
    }
   //显示商品结算页
    public function create()
    {
        //提交订单前 先判定是否登录
        if(!session('?user_info')) {
            //未登录 必须跳回登录页面 再跳回购物车页面
            session('back_url','home/cart/index');
            $this->redirect('home/login/login');
        }
        //接收数据
        $cart_ids=request()->param('cart_ids');
        //dump($cart_ids);die;
        if(!preg_match('/^\d+/',$cart_ids)){
            $this->error('参数错误，请重新提交订单');
        }
        //获取用户id
        $user_id=session('user_info.id');
        $goods=CartModel::alias('t1')
            ->join('tpshop_goods t2','t1.goods_id=t2.id','left')
            ->where('t1.user_id',$user_id)
            ->where('t1.id','in',$cart_ids)
            ->field('t1.*,t2.goods_name,t2.goods_logo,t2.goods_price')
            ->select();
        //订单商品总件数 总金额
        $total_number=0;
        $total_price=0;
        foreach($goods as $v){
            $total_number+=$v['number'];
            $total_price+=$v['number']*$v['goods_price'];
        }
        //查询收货地址信息
        $address=AddressModel::where('user_id',$user_id)->select();
        //dump($address);die();
        //支付方式
        $pay_type=config('pay_type');
        //渲染模板
        return view('create',[
            'address'=>$address,
            'pay_type'=>$pay_type,
            'goods'=>$goods,
            'total_number'=>$total_number,
            'total_price'=>$total_price,
        ]);
    }

    /**
     * 保存提交订单的信息
     *
     * @param  \think\Request  $request
     * @return \think\Response
     */
    public function save(Request $request)
    {
        $data=$request->param();
        //dump($data);die;
        $rule=[
            'cart_ids'=>'require',
            'address_id'=>'require|integer|gt:0',
            'pay_type'=>'require'
        ];
        $msg=[
            'cart_ids.require'=>'订单内容不能为空',
            'address_id.require'=>'收货地址不能为空',
            'address_id.gt'=>'请选择正确的收货地址',
            'address.integer'=>'收货地址参数不正确',
            'pay_type.require'=>'请选择支付方式',
        ];
        $validate= new \think\Validate($rule,$msg);
        if(!$validate->check($data)){
            $error=$validate->getError();
            $this->error($error);
        }

        //开启事务
        \think\Db::startTrans();
        try{
            //收集数据表信息 整理 提交
            $order_sn=time().mt_rand(1000,9999);
            $user_id=session('user_info.id');
            $address=AddressModel::find($data['address_id']);
            //联表查询购物车商品信息
            $goods=CartModel::alias('t1')
                ->join('tpshop_goods t2','t1.goods_id=t2.id','left')
                ->where('t1.user_id',$user_id)
                ->where('t1.id','in',$data['cart_ids'])
                ->field('t1.*,t2.goods_name,t2.goods_logo,t2.goods_price')
                ->select();
            //订单商品总价
            $order_amount=0;
            foreach($goods as $v){
                $order_amount+=$v['goods_price']*$v['number'];
            }
            //整合订单数据表信息 添加到数据表
            $order_data=[
                'order_sn'=>$order_sn,
                'order_amount'=>$order_amount,
                'user_id'=>$user_id,
                'consignee_name'=>$address['consignee'],
                'consignee_phone'=>$address['phone'],
                'consignee_address'=>$address['address'],
                'shipping_type'=>'shunfeng',
                'pay_type'=>$data['pay_type']
            ];
            $order=OrderModel::create($order_data);
            //整合商品订单表信息 批量添加
            $ordergoods_data=[];
            foreach($goods as $v){
                $row=[
                    'order_id'=>$order->id,
                    'goods_id'=>$v['goods_id'],
                    'goods_name'=>$v['goods_name'],
                    'goods_price'=>$v['goods_price'],
                    'goods_logo'=>$v['goods_price'],
                    'number'=>$v['number'],
                    'goods_attr_ids'=>$v['goods_attr_ids']
                    ];
                $ordergoods_data[]=$row;
            }
            $ordergoods=new OrderGoodsModel();
            $ordergoods->saveAll($ordergoods_data);
            //删除购物车记录
            CartModel::destroy($data['cart_ids']);
            \think\Db::commit();
        }catch (\Exception $e){
            //回滚事务
            \think\Db::rollback();
            $error=$e->getMessage();
            //开发阶段还需要错误码
            //$code=$e->getCode();
            $this->error($error);
        }
        //支付流程
        switch ($data['pay_type']){
            //微信支付
            case 'wechat':
                return view('weixinpay',['order_sn'=>$order_sn,'order_amount'=>$order_amount]);
                break;
            //银联支付
            case 'card':break;
            //货到付款
            case 'cash':break;
            //支付宝支付
            default:
                $html="<form id='alipayment' action='/plugins/alipay/pagepay/pagepay.php' method='post'
                         style='display: none'>
            <input id='WIDout_trade_no' name='WIDout_trade_no' value='{$order_sn}' />
            <input id='WIDsubject' name='WIDsubject' value='品优购订单'/>
            <input id='WIDtotal_amount' name='WIDtotal_amount' value='{$order_amount}'/>
            <input id='WIDbody' name='WIDbody' value='测试购物付款'/>
            </form><script type='text/javascript'>document.getElementById('alipayment').submit()</script>";
                echo $html;
        }
    }

    /**
     * 增加收货地址单页.
     *
     * @return \think\Response
     */
    public function addressList()
    {
        //渲染模板
        return view();
    }

    /**
     * 评价
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function mycomment($id)
    {
        //渲染模板
        return view();
    }
    //支付宝异步通知地址  支付宝post发送过来订单支付状态等信息
    public function notify(){
        $data=request()->param();
        //dump($data);die;
        //参数检测
        require_once("./plugins/alipay/config.php");
        require_once './plugins/alipay/pagepay/service/AlipayTradeService.php';
        $alipaySevice = new \AlipayTradeService($config);
        $result = $alipaySevice->check($data);
        //$alipaySevice->writeLog(var_export($_POST,true));  //记录到日志
        if($result){
            //验签成功
            //将接收到的参数，存储到数据表（创建一个支付结果表）
            //交易状态判断
            if($data['trade_status']=='TRADE_FINISHED'){
                //商户已经处理
                echo 'success';die;
            }
            if($data['trade_status']=='TRADE_SUCCESS'){
                // 此订单 需要进行处理--更改订单状态
                $order_sn=$data['out_trade_no'];
                $order=OrderModel::where('order_sn',$order_sn)->find();
                if($order['order_amount']==$data['total_amount'] && $order['pay_status']==0){
                    //将订单状态该成已付款
                    $order->pay_status=1;
                    $order->save();
                    echo  'sucess';die;
                }
            }
            echo 'fail';die;
        }else{
            //验签失败
            echo 'fail';die;
        }
    }
    //支付宝同步跳转函数   用户显示订单支付状态页面
    public function callback(){
        $data=request()->param();
        //dump($data);die;
        //参数检测
        require_once("./plugins/alipay/config.php");
        require_once './plugins/alipay/pagepay/service/AlipayTradeService.php';
        $alipaySevice = new \AlipayTradeService($config);
        //$data['total_amount']=12123132;
        $result = $alipaySevice->check($data);
        //dump($result);die; //返回布尔值
        //$data['total_amount']=12123132;
        if($result){
            //验签成功
            $order_sn=$data['out_trade_no'];
            $order_amount=$data['total_amount'];
            //验证支付成功的单号 总价 和数据表的单号总价是否一致
            $order=OrderModel::where(['order_sn'=>$order_sn])->find();
            if($order && $order['order_amount']==$order_amount){
                return view('paysuccess',['total_amount'=>$order_amount]);
            }else{
                return view('payfail',['msg'=>'支付失败！支付金额不正确']);
            }
        }else{
            //验签失败
            return view('payfail',['msg'=>'支付失败！参数验证失败']);
        }
    }
}
