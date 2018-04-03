<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
	<title>填写核对订单信息</title>
	<link rel="stylesheet" href="/style/base.css" type="text/css">
	<link rel="stylesheet" href="/style/global.css" type="text/css">
	<link rel="stylesheet" href="/style/header.css" type="text/css">
	<link rel="stylesheet" href="/style/fillin.css" type="text/css">
	<link rel="stylesheet" href="/style/footer.css" type="text/css">

	<script type="text/javascript" src="/js/jquery-1.8.3.min.js"></script>
	<script type="text/javascript" src="/js/cart2.js"></script>

</head>
<body>
	<!-- 顶部导航 start -->
    <?php
    //顶部导航开始
    include Yii::getAlias('@app')."/views/common/nav.php";
    //顶部导航结束
    ?>
	<!-- 顶部导航 end -->
	
	<div style="clear:both;"></div>
	
	<!-- 页面头部 start -->
	<div class="header w990 bc mt15">
		<div class="logo w990">
			<h2 class="fl"><a href="index.html"><img src="/images/logo.png" alt="京西商城"></a></h2>
			<div class="flow fr flow2">
				<ul>
					<li>1.我的购物车</li>
					<li class="cur">2.填写核对订单信息</li>
					<li>3.成功提交订单</li>
				</ul>
			</div>
		</div>
	</div>
	<!-- 页面头部 end -->
	
	<div style="clear:both;"></div>
    <form>
        <input type="hidden" name="_csrf-frontend" value="<?=Yii::$app->request->csrfToken?>" >
	<!-- 主体部分 start -->
	<div class="fillin w990 bc mt15">
		<div class="fillin_hd">
			<h2>填写并核对订单信息</h2>
		</div>

		<div class="fillin_bd">
			<!-- 收货人信息  start-->
			<div class="address">
				<h3>收货人信息 </h3>
				<div class="address_select">
					<ul>
                        <!--收货地址方式开始-->
                        <?php
                            foreach ($address as $key=>$value):
                        ?>
						<li class="<?=$key==0?"cur":"";?>">
							<input type="radio" name="address" value="<?=$value->id?>" <?=$value->status==1?"checked='checked'":"";?>/>
                            <?=$value->name?>
                            <?=$value->province?>
                            <?=$value->city?>
                            <?=$value->county?>
                            <?=$value->address?>
                            <?=$value->mobile?>
							&ensp;<a href="javasrtipt:;"><?=$value->status==1?"默认地址":""?></a>
						</li>
                        <?php
                            endforeach;
                        ?>
                        <!--收货地址方式结束-->
					</ul>

				</div>
			</div>
			<!-- 收货人信息  end-->

			<!-- 配送方式 start -->
			<div class="delivery">
				<h3>送货方式</h3>

				<div class="delivery_select">
					<table>
						<thead>
							<tr>
								<th class="col1">送货方式</th>
								<th class="col2">运费</th>
								<th class="col3">运费标准</th>
							</tr>
						</thead>
						<tbody>
                        <!--配送方式开始-->
                        <?php
                            foreach ($delivery as $key=>$value):
                        ?>
							<tr class="<?=$key==0?"cur":"";?>" >
								<td>
									<input type="radio" name="delivery" value="<?=$value->id?>" <?=$key==0?"checked='checked'":"";?>/>
                                    <?=$value->name?>
								</td>
								<td>￥<span><?=$value->money?></span></td>
								<td><?=$value->content?></td>
							</tr>
                        <?php
                            endforeach;
                        ?>
                        <!--配送方式结束-->
						</tbody>
					</table>

				</div>
			</div> 
			<!-- 配送方式 end --> 

			<!-- 支付方式  start-->
			<div class="pay">
				<h3>支付方式</h3>


				<div class="pay_select">
					<table>
                        <!--支付方式开始-->
                        <?php
                            foreach ($pay as $key=>$value):
                        ?>
						<tr class="<?=$key==0?"":"cur";?>cur" >
                            <td class="col1"><input type="radio" name="pay" value="<?=$value->id?>" <?=$key==0?"checked='checked'":"";?>/><?=$value->name?></td>
                            <td class="col2"><?=$value->content?></td>
						</tr>
                        <?php
                            endforeach;
                        ?>
                        <!--支付方式结束-->
					</table>

				</div>
			</div>
			<!-- 支付方式  end-->

			<!-- 发票信息 start-->

            <!-- 发票信息 end-->

			<!-- 商品清单 start -->
			<div class="goods">
				<h3>商品清单</h3>
				<table>
					<thead>
						<tr>
							<th class="col1">商品</th>
							<th class="col3">价格</th>
							<th class="col4">数量</th>
							<th class="col5">小计</th>
						</tr>	
					</thead>
					<tbody>
                    <?php
                        $money="";
                        $num="";
                        foreach ($goods as $good):
                        $num+=$cart[$good->id];
                        $money+=$good->shop_price*$cart[$good->id] ;
                    ?>

						<tr>
							<td class="col1"><a href=""><img src="<?=$good->logo?>" alt="" /></a>
                                <strong><a href=""><?=$good->name?></a></strong></td>
							<td class="col3">￥<?=$good->shop_price?></td>
							<td class="col4"><?=$cart[$good->id]?></td>
							<td class="col5"><span><?=$good->shop_price*$cart[$good->id]?></span></td>
						</tr>
                    <?php
                        endforeach;
                    ?>
					</tbody>
					<tfoot>
						<tr>
							<td colspan="5">
								<ul>
									<li>
										<span><?=$num;?> 件商品，总商品金额：</span>
										<em>￥<span class="goods_price"><?=$money;?></span></em>
									</li>

									<li>
										<span>运费：</span>
										<em>￥<span id="price"><?=$delivery[0]->money;?></span></em>
									</li>
									<li>
										<span>应付总额：</span>
										<em>￥<span class="price_all"><?=$money+$delivery[0]->money?></span></em>
									</li>
								</ul>
							</td>
						</tr>
					</tfoot>
				</table>
			</div>
			<!-- 商品清单 end -->
		
		</div>

		<div class="fillin_ft">
			<a href="javascript:;" id="sub_btn"><span >提交订单</span></a>
			<p>应付总额：<strong>￥<span class="price_all"><?=$money+$delivery[0]->money?></span>元</strong></p>
			
		</div>
	</div>
	<!-- 主体部分 end -->
    </form>
	<div style="clear:both;"></div>
	<!-- 底部版权 start -->
	<div class="footer w1210 bc mt15">
		<p class="links">
			<a href="">关于我们</a> |
			<a href="">联系我们</a> |
			<a href="">人才招聘</a> |
			<a href="">商家入驻</a> |
			<a href="">千寻网</a> |
			<a href="">奢侈品网</a> |
			<a href="">广告服务</a> |
			<a href="">移动终端</a> |
			<a href="">友情链接</a> |
			<a href="">销售联盟</a> |
			<a href="">京西论坛</a>
		</p>
		<p class="copyright">
			 © 2005-2013 京东网上商城 版权所有，并保留所有权利。  ICP备案证书号:京ICP证070359号 
		</p>
		<p class="auth">
			<a href=""><img src="/images/xin.png" alt="" /></a>
			<a href=""><img src="/images/kexin.jpg" alt="" /></a>
			<a href=""><img src="/images/police.jpg" alt="" /></a>
			<a href=""><img src="/images/beian.gif" alt="" /></a>
		</p>
	</div>
	<!-- 底部版权 end -->
    <script type="text/javascript">

        $(function () {

            //监听配送运费
            $("input[name='delivery']").change(function () {
               //得到运费
                var price =$(this).parent().next().children().text();
//                console.debug(price);
                //更改运费
                $('#price').text(price);
                //修改总金额
                $(".price_all").text((parseFloat(price)+parseFloat($(".goods_price").text())).toFixed(2))
            });

            //点击提交订单
            $("#sub_btn").click(function () {
                console.dir($("form").serialize());
                $.post("/order/add",$("form").serialize(),function (data) {
                    if (data.status) {
                        self.location.href="/order/list?id="+data.orderId;
                    }
                },'json');
            });
        });
    </script>
</body>
</html>
