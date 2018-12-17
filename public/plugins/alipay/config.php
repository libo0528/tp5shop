<?php
$config = array (	
		//应用ID,您的APPID。
		'app_id' => "2016092000555707",

		//商户私钥
		'merchant_private_key' => "MIIEpQIBAAKCAQEAvIc+UMl+LenRww2bitrE6LJCMaZpW3s2EyttI2iMnehGV1kn1GVUaGJYAOm8eD0ngoZ+aqhPJojaAlCamHUTfpFfFdkZ2kqChCsmFEPLmXMrI+OtCctLUuuKwzUG+iThn1ZW5dSdgwZ1o7X9N/SWEXpGimEg5NGTXKEEorA8pG27SCry8ovDQ+sO6hKuxyuWSwdoo3/kqdh84CRTyrP/WgeXVguFw7a8slQnOmbdWUK0FnD9IlwDN52CahpFZPubHMes+5G3KaZ/D6W1QP98j5OBPuYLLjvmnFasGm1wHd1vsCnqVPot9wJtBzqOJ2lJE4UZiZzqGgMeQN9pf1N6kwIDAQABAoIBAQCNISEBERUoAbVJMtKRa5ukJm1vBYgxN1cPmp6ktwss2kht8wvh78j0K0p9xHuV9xGwoEXaSgyWaDPrL7PLFTl2shRgIpk71DNIBZUH7ohnYtOUlkde4ZSNik3iO7vxXdKYmOWMP1F6WEeQOhhI3wvIIaTUykj5gfmyiccSkqOS4lk5HcgSe4HBiJyVjje4fYl07kwXZYU22q/Q6NdCG2xRycGBjRtONlgXjaXZqOdrw+FLASWJpelO4k28MLeuDkRnqzptAeXvnaDfnc2RxihZWpNo4TYfU/hOEAnBgFiOtl5kEGzPHt1yyFgDQ/ytDMItVDvh4CLgaMxGrnKURaPBAoGBANyVHQwoe0kTkpPMf5GC5SmoIJyl6z42d2mkh+R/qyO6dmWTBGCgFUmBFbZ1IFVmbFWL5YJ3zY5dBzOwbzl8K6MDVKHuTAjbhfhhLVcPf9rmml6HAYzK2x7FgjH/9VpwY9qc5q0eW/o6kHMD8OhdQH9CdLCvFcWWFqPLAkKN8mtZAoGBANrMkMlA5KTeEzxx/SkLpEcC6ncNSgc2lBLGmNKsDnBlPSUYjKP2pFwuWrx1Ico8X98EQoxUoK76tjfbYpleOvgW3PWtYffvY7Y47HmUhMagq+y0EdVxtNtIGkvM5XIATV5rKZpujsZAdCysFTIRU6S3K3Qye+4jzttaUafAztPLAoGBAKE11ZHiMCA7Tr/4EC+vo34rYxI5bND+C43I8ow8Bj7JcuhPZz6bIJOk5NKBjYz8myZ2ZNlSZ4epU68iPleb+WRc9ciXGTjL/4CEvRdIu5+nM6DsOGTGiRhXLw0NzSo4w7GtRCW3727UyGdWtQWNyap4TkIm49mRFKDGqLMtfMOZAoGBAIc1UOe9PU4QmgyJWGtr8UmcqK2HPc71UE3GAhx2DLoyJbXt1MNiIDGCx7mcHJRv+MLFTCaSCXX4InCkFus1yvUSk9sSGO9xNMofpSqAuAhSs8ujfz3KdXvos69R4BwxyZ3mVSBE39/tZp1QfgyhnnG0a5rzs5f28takmr8yq90/AoGAMzqOYU9OYTCPjSdyA/P/4dt5RII4nMPAXfYMn0tKDYHI7tC6ig3TB+aI7MpDoaXaJkElXmjJsW7khEMwqVYoGF+4bqUYpQyTvAPVnhMese+RI9WAyxHkw3rQv3gHzMHAJQKwQ++MczCwm+upfDyCPue0xCwna4WgmupwRDoqHoo=",
		
		//异步通知地址
		'notify_url' => "http://".$_SERVER['SERVER_NAME']."/index.php/home/order/notify",
		
		//同步跳转
		'return_url' => "http://".$_SERVER['SERVER_NAME']."/index.php/home/order/callback",

		//编码格式
		'charset' => "UTF-8",

		//签名方式
		'sign_type'=>"RSA2",

		//支付宝网关
		'gatewayUrl' => "https://openapi.alipaydev.com/gateway.do",

		//支付宝公钥,查看地址：https://openhome.alipay.com/platform/keyManage.htm 对应APPID下的支付宝公钥。
		'alipay_public_key' => "MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEA9+KaceHRAAu3RFfMHklz5hIJ7HqNLT+Xk0zKBuZy4a2Ks/DJB1ei1104VFEqrvrlb4FDVTBD5HCru4Pt6O5RK3v0UCpsIAK8FhdVh0GJi0vCBG0H3zl27k7aEu8uKq/EZ2fr2mk+QfUUE3btKkK9Oz6oFYYoXGcNFVfyzQQfmSV6d9sHGJlZYSHj8TwwsJco/V2yz4ojHxqCuQ/VQDS014S7CziBXgSdBKBZHYkkSxSxGgjhang9PbRXlrHpmWaRS44GZd9pfQxUnVby0ri3EfF9rXc9tG11VzIJsdc29e7yLnt8nGFNp328jao9Qtc3HzAtqgE/utT0Y7XRj7+oJwIDAQAB",
);