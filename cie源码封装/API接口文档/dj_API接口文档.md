# 电竞API文档 #

### host:http://54.223.105.156/api/public ###
### ENV:test ###
### 版本更新（Version）###

Version |  Author 	| Date 			| Note
:------:|:---------:|:-------------:|------
1.0.0   |   胡红    	|2017-08-14   	|

## 用户模块

### 1.第三方登入

- **功能定义**	: 通过微信、QQ第三方登入系统
- **URL**		: `http://<host>/user/register_third`
- **Method**	: Post
- **请求参数**  	:

   参数名 	|  类型 		| 是否必填 		| 说明
:----------:|:---------:|:-------------:|---------------------------
login_type  |   int   	|是  			|登入设备类型[1pc2ios3android]
type  		|   int   	|是  			|第三方类型[1微信2qq目前只支持微信]
js_code  	|   string  |是  			|微信code
nickname  	|   string  |否  			|昵称
mobile  	|   string  |否  			|手机号
icon  		|   string  |否  			|头像url
sex  		|   int   	|否  			|性别[0未知1男2女]
age  		|   int   	|否  			|年龄
address  	|   string  |否  			|住址
sign  		|   string  |是  			|签名

- **返回值说明** ：
 
   参数名 	| 参数类型  	|说明
:----------:|:----------|:-------------
code  		|  string  	|请求状态码    
msg  		|  string  	|状态消息   
data  		|  object  	|返回数据  
uuid  		|  bigint  	|用户唯一标识UUID  
nickname  	|  string  	|用户昵称  
token  		|  string  	|Token令牌  
icon  		|  string  	|用户头像url  
sex  		|  int  	|性别[0未知1男2女]  
age  		|  int  	|年龄
address  	|  string  	|地址[空格隔开] 
mobile  	|  string  	|手机号   
 					
- **返回数据格式** ：  
{  
	"code":"0000",   
	"msg":"操作成功",  
	"data":{  
		　　"uuid": 1,  
	    　　"nickname": "",  
	    　　"mobile": "",  
	    　　"icon": "",  
	    　　"sex": "",  
	    　　"age": 0,  
	    　　"address": "",  
	    　　"token": "2ced145983a115546c831bfb2d2cc016"  
	　　}  
}



### 2.获取用户信息
- **功能定义**	: 通过uuid+token获取用户信息
- **URL**		: `http://<host>/user/info`
- **Method**	: Get
- **请求参数**  	:

   参数名 	|  类型 		| 是否必填 		| 说明
:----------:|:---------:|:-------------:|---------------------------
uuid  		|   bigint  |是  			|用户唯一标识uuid
token  		|   string  |是  			|Token令牌
sign  		|   string  |是  			|参数校验sign

- **返回值说明** ： 

   参数名 	| 参数类型  	|说明
:----------:|:----------|:-------------
code  		|  string  	|请求状态码    
msg  		|  string  	|状态消息   
data  		|  object  	|返回数据  
uuid  		|  bigint  	|用户唯一标识UUID  
nickname  	|  string  	|用户昵称  
token  		|  string  	|Token令牌  
icon  		|  string  	|用户头像url  
sex  		|  int  	|性别[0未知1男2女]  
age  		|  int  	|年龄
address  	|  string  	|地址[空格隔开] 
mobile  	|  string  	|手机号   

- **返回数据格式** ：  
{  
	"code":"0000",   
	"msg":"操作成功",  
	"data":{  
	　　"uuid": 1,  
    　　"nickname": "tteerer",  
    　　"mobile": "13616849092",  
    　　"icon": "http://ip/11/22/1.jpg",  
    　　"sex": "",  
    　　"age": 20,  
    　　"address": "上海 上海 普陀区 张江路388号",  
    　　"id_card": "",  
    　　"name": "ttttt",  
    　　"token": "55daad84eb3ea56a20ea3a2359971299"    
	　　}  
}


### 3.修改用户信息接口
- **功能定义**	: 修改用户信息
- **URL**		: `http://<host>/user/update`
- **Method**	: Post
- **请求参数**  	:

   参数名 	|  类型 		| 是否必填 		| 说明
:----------:|:---------:|:-------------:|---------------------------
uuid  		|   bigint  |是  			|用户唯一标识uuid
token  		|   string  |是  			|Token令牌
nickname  	|   string  |是  			|用户昵称
age  		|   int  	|是  			|年龄
address  	|   string  |是  			|地址
sex  		|   int  	|是  			|性别[0未知1男2女]
file  		|   二进制流 |是  			|文件流
sign  		|   string  |是  			|参数校验sign

- **返回值说明** ： 

   参数名 	| 参数类型  	|说明
:----------:|:----------|:-------------
code  		|  string  	|请求状态码    
msg  		|  string  	|状态消息   
data  		|  object  	|返回数据   

- **返回数据格式** ：  
{  
	"code":"0000",   
	"msg":"操作成功",  
	"data":{  
	　　"uuid": 1,  
    　　"nickname": "tteerer",  
    　　"mobile": "13616849092",  
    　　"icon": "http://ip/11/22/1.jpg",  
    　　"sex": "",  
    　　"age": 20,  
    　　"address": "上海 上海 普陀区 张江路388号",  
    　　"id_card": "",  
    　　"name": "ttttt",  
    　　"token": "55daad84eb3ea56a20ea3a2359971299"    
	　　}  
}


### 4.获取手机验证码
- **功能定义**	: 获取手机验证码
- **URL**		: `http://<host>/user/sms`
- **Method**	: Get
- **请求参数**  	:

   参数名 	|  类型 		| 是否必填 		| 说明
:----------:|:---------:|:-------------:|---------------------------
uuid  		|   bigint  |是  			|用户唯一标识uuid
token  		|   string  |是  			|Token令牌
mobile  	|   string  |是  			|手机号
sign  		|   string  |是  			|参数校验sign

- **返回值说明** ： 

   参数名 	| 参数类型  	|说明
:----------:|:----------|:-------------
code  		|  string  	|请求状态码    
msg  		|  string  	|状态消息   
data  		|  object  	|返回数据   

- **返回数据格式** ：  
{  
	"code":"0000",   
	"msg":"操作成功",  
	"data":[]  
}


### 5.手机号认证
- **功能定义**	: 通过验证码绑定手机号
- **URL**		: `http://<host>/user/auth_mobile`
- **Method**	: Post
- **请求参数**  	:

   参数名 	|  类型 		| 是否必填 		| 说明
:----------:|:---------:|:-------------:|---------------------------
uuid  		|   bigint  |是  			|用户唯一标识uuid
token  		|   string  |是  			|Token令牌
mobile   	|   string  |是  			|用户昵称
verity_code |   int  	|是  			|年龄
sign  		|   string  |是  			|参数校验sign

- **返回值说明** ： 

   参数名 	| 参数类型  	|说明
:----------:|:----------|:-------------
code  		|  string  	|请求状态码    
msg  		|  string  	|状态消息   
data  		|  object  	|返回数据   

- **返回数据格式** ：json格式  
{  
	"code":"0000",   
	"msg":"操作成功",  
	"data":[] 
}



### 6.身份证验证[TODO]
- **功能定义**	: 验证身份证信息有效性
- **URL**		: `http://<host>/user/auth_idcard`
- **Method**	: Post
- **请求参数**  	:

   参数名 	|  类型 		| 是否必填 		| 说明
:----------:|:---------:|:-------------:|---------------------------
uuid  		|   bigint  |是  			|用户唯一标识uuid
token  		|   string  |是  			|Token令牌
id_card   	|   string  |是  			|身份证号
name 		|   string  |是  			|姓名
sign  		|   string  |是  			|参数校验sign

- **返回值说明** ： 

   参数名 	| 参数类型  	|说明
:----------:|:----------|:-------------
code  		|  string  	|请求状态码    
msg  		|  string  	|状态消息   
data  		|  object  	|返回数据   

- **返回数据格式** ：json格式  
{  
	"code":"0000",   
	"msg":"操作成功",  
	"data":[] 
}


## 大赛报名模块
### 1.获取大赛可报名游戏列表
- **功能定义**	: 获取大赛可报名游戏列表
- **URL**		: `http://<host>/match/game_list`
- **Method**	: Get
- **请求参数**  	:

   参数名 	|  类型 		| 是否必填 		| 说明
:----------:|:---------:|:-------------:|---------------------------
uuid  		|   bigint  |是  			|用户唯一标识uuid
token  		|   string  |是  			|Token令牌
type  		|   string  |是  			|[1个人赛2团队赛]
page  		|   string  |是  			|分页起始页 1开始
count  		|   int  	|是  			|每页显示条数
sign  		|   string  |是  			|参数校验sign

- **返回值说明** ： 

   参数名 	| 参数类型  	|说明
:----------:|:----------|:-------------
code  		|  string  	|请求状态码    
msg  		|  string  	|状态消息   
data  		|  object  	|返回数据   
pagecount  	|  int  	|数据总页数  
list  		|  object  	|数据对象  
id  		|  bigint  	|游戏ID  
name  		|  string  	|游戏名  
icon  		|  string  	|游戏ICON  
img  		|  string  	|游戏大图  
type  		|  int  	|赛事类型[1个人赛2团队赛]  
status  	|  int  	|报名审核状态[1审核中2通过2拒绝]  

- **返回数据格式** ：  
{
	"code": "0000",  
	"msg": "操作成功",  
	"data": {  
    　　"pagecount": 1,  
    　　"list": [{  
    　　　　"id": 3,  
    　　　　"name": "炉石",  
    　　　　"icon": "http://ip/11/22/1.jpg",  
    　　　　"img": "",  
    　　　　"type": 1,  
    　　　　"status": 1  
      　　　},]  
　　}  
}



### 2.获取我报名的游戏
- **功能定义**	: 获取我报名的游戏
- **URL**		: `http://<host>/match/myapply_list`
- **Method**	: Get
- **请求参数**  	:

   参数名 	|  类型 		| 是否必填 		| 说明
:----------:|:---------:|:-------------:|---------------------------
uuid  		|   bigint  |是  			|用户唯一标识uuid
token  		|   string  |是  			|Token令牌
sign  		|   string  |是  			|参数校验sign

- **返回值说明** ： 

   参数名 	| 参数类型  	|说明
:----------:|:----------|:-------------
code  		|  string  	|请求状态码    
msg  		|  string  	|状态消息   
data  		|  object  	|返回数据   
apply  		|  object  	|已报名列表
id  		|  bigint  	|游戏ID  
name  		|  string  	|游戏名  
icon  		|  string  	|游戏ICON  
img  		|  string  	|游戏大图  
type  		|  int  	|赛事类型[1个人赛2团队赛]  
status  	|  int  	|报名审核状态[1审核中2通过2拒绝]  
group_id  	|  bigint  	|战队ID
noapply  	|  object  	|未报名列表  
id  		|  bigint  	|游戏ID  
name  		|  string  	|游戏名  
icon  		|  string  	|游戏ICON  
img  		|  string  	|游戏大图  
type  		|  int  	|赛事类型[1个人赛2团队赛]   

- **返回数据格式** ：  
{
	"code": "0000",  
	"msg": "操作成功",  
	"data": {    
    　　"apply": [{  
    　　　　"id": 1,  
    　　　　"name": "王者荣耀",  
    　　　　"icon": "http://ip/11/22/1.jpg",  
    　　　　"img": "",  
    　　　　"type": 2,  
    　　　　"status": 1，  
	　　　　"group_id": 1  
      　　　}],    
	　　"noapply": [{  
    　　　　"id": 2,  
    　　　　"name": "炉石",  
    　　　　"icon": "http://ip/11/22/1.jpg",  
    　　　　"img": "",  
    　　　　"type": 1,  
    　　　　"status": 1，  
	　　　　"group_id": 0   
      　　　}]    
　　}  
}


### 3.获取我的游戏列表
- **功能定义**	: 获取我的游戏列表
- **URL**		: `http://<host>/match/mygame_list`
- **Method**	: Get
- **请求参数**  	:

   参数名 	|  类型 		| 是否必填 		| 说明
:----------:|:---------:|:-------------:|---------------------------
uuid  		|   bigint  |是  			|用户唯一标识uuid
token  		|   string  |是  			|Token令牌
page  		|   string  |是  			|分页起始页 1开始
count  		|   int  	|是  			|每页显示条数
sign  		|   string  |是  			|参数校验sign

- **返回值说明** ： 

   参数名 	| 参数类型  	|说明
:----------:|:----------|:-------------
code  		|  string  	|请求状态码    
msg  		|  string  	|状态消息   
data  		|  object  	|返回数据   
pagecount  	|  int  	|数据总页数  
list  		|  object  	|数据对象  
id  		|  bigint  	|游戏ID  
name  		|  string  	|游戏名  
icon  		|  string  	|游戏ICON  
img  		|  string  	|游戏大图  
type  		|  int  	|赛事类型[1个人赛2团队赛]   

- **返回数据格式** ：  
{
	"code": "0000",  
	"msg": "操作成功",  
	"data": {  
    　　"pagecount": 1,  
    　　"list": [{  
    　　　　"id": 3,  
    　　　　"name": "炉石",  
    　　　　"icon": "http://ip/11/22/1.jpg",  
    　　　　"img": "",  
    　　　　"type": 1,   
      　　　},]  
　　}  
}



### 4.获取我的游戏信息
- **功能定义**	: 获取我的游戏信息
- **URL**		: `http://<host>/match/mygame_info`
- **Method**	: Get
- **请求参数**  	:

   参数名 	|  类型 		| 是否必填 		| 说明
:----------:|:---------:|:-------------:|---------------------------
uuid  		|   bigint  |是  			|用户唯一标识uuid
token  		|   string  |是  			|Token令牌
id  		|   bigint  |是  			|游戏ID
sign  		|   string  |是  			|参数校验sign

- **返回值说明** ： 

   参数名 	| 参数类型  	|说明
:----------:|:----------|:-------------
code  		|  string  	|请求状态码    
msg  		|  string  	|状态消息   
data  		|  object  	|返回数据   
id  		|  bigint  	|游戏ID  
name  		|  string  	|游戏名  
icon  		|  string  	|游戏ICON  
img  		|  string  	|游戏大图  
type  		|  int  	|赛事类型[1个人赛2团队赛]   
is_default	|  int  	|是否默认约战[1是0否]  
g_account	|  string  	|游戏账号 
g_nickname	|  string  	|游戏昵称
g_level		|  string  	|游戏等级
g_zone		|  int  	|游戏区服ID
points		|  int  	|游戏积分  

- **返回数据格式** ：  
{
	"code": "0000",  
	"msg": "操作成功",  
	"data": {  
    　　"id": 1,  
    　　"name": "tteerer",  
    　　"icon": "13616849092",  
    　　"img" : "http://ip/11/22/1.jpg",  
    　　"type": 1,  
    　　"is_default": 20,  
    　　"g_account": "****",  
    　　"g_nickname": "****",  
    　　"g_level": "2",  
    　　"points": 100     
　　}  
}



### 5.获取游戏报名所需信息
- **功能定义**	: 获取游戏报名所需填写信息
- **URL**		: `http://<host>/match/apply_info`
- **Method**	: Get
- **请求参数**  	:

   参数名 	|  类型 		| 是否必填 		| 说明
:----------:|:---------:|:-------------:|---------------------------
uuid  		|   bigint  |是  			|用户唯一标识uuid
token  		|   string  |是  			|Token令牌
id  		|   bigint  |是  			|游戏ID
sign  		|   string  |是  			|参数校验sign

- **返回值说明** ： 

   参数名 	| 参数类型  	|说明
:----------:|:----------|:-------------
code  		|  string  	|请求状态码    
msg  		|  string  	|状态消息   
data  		|  object  	|返回数据   
g_info  	|  object  	|填写游戏信息说明  
id  		|  bigint  	|游戏ID  
name  		|  string  	|游戏名  
icon  		|  string  	|游戏ICON  
img  		|  string  	|游戏大图  
g_account  	|  int  	|是否必填  
g_nickname	|  int  	|是否必填[1必须2可选0不需要该选项] 
g_level  	|  int  	|是否必填[1必须2可选0不需要该选项]  
g_zone  	|  int  	|是否必填[1必须2可选0不需要该选项]  
zone_list  	|  object  	|游戏区服列表 
id  		|  int  	|区服ID 
z_name  	|  string  	|区服名称 

- **返回数据格式** ：  
{
	"code": "0000",  
	"msg": "操作成功",  
	"data": {  
		"g_info": {  
    　　　　"id": 1,  
    　　　　"name": "王者",    
    　　　　"icon": "http://ip/11/22/1.png",  
    　　　　"img": "http://ip/11/22/2.png",  
    　　　　"g_account": 1,  
    　　　　"g_nickname": 1,  
    　　　　"g_level": 1,  
    　　　　"g_zone": 1,  
      　　　},  
    　　"zone_list": [{  
    　　　　"id": 1,  
    　　　　"name": "微信区"  
      　　　}] 
　　}  
}


### 6.完善我的游戏信息
- **功能定义**	: 完善我的游戏信息
- **URL**		: `http://<host>/match/save_mygameinfo`
- **Method**	: Post
- **请求参数**  	:

   参数名 	|  类型 		| 是否必填 		| 说明
:----------:|:---------:|:-------------:|---------------------------
uuid  		|   bigint  |是  			|用户唯一标识uuid
token  		|   string  |是  			|Token令牌
id  		|   bigint  |是  			|游戏ID
account  	|   string  |是  			|游戏账号
nickname  	|   string  |否  			|游戏昵称[是否必须，根据游戏填写要求]
level  		|   string  |否  			|游戏level[是否必须，根据游戏填写要求]
z_id  		|   int  	|否  			|所在区服ID[是否必须，根据游戏填写要求]
sign  		|   string  |是  			|参数校验sign

- **返回值说明** ： 

   参数名 	| 参数类型  	|说明
:----------:|:----------|:-------------
code  		|  string  	|请求状态码    
msg  		|  string  	|状态消息   
data  		|  object  	|返回数据   
g_info  	|  object  	|填写游戏信息说明  
id  		|  bigint  	|游戏ID  
name  		|  string  	|游戏名  
icon  		|  string  	|游戏ICON  
img  		|  string  	|游戏大图  
g_account  	|  int  	|是否必填  
g_nickname	|  int  	|是否必填[1必须2可选0不需要该选项] 
g_level  	|  int  	|是否必填[1必须2可选0不需要该选项]  
g_zone  	|  int  	|是否必填[1必须2可选0不需要该选项]  
zone_list  	|  object  	|游戏区服列表 
id  		|  int  	|区服ID 
z_name  	|  string  	|区服名称 

- **返回数据格式** ：  
{
	"code": "0000",  
	"msg": "操作成功",  
	"data": []  
}


### 7.获取游戏赛区列表
- **功能定义**	: 获取游戏赛区列表
- **URL**		: `http://<host>/match/matchzone_list`
- **Method**	: Get
- **请求参数**  	:

   参数名 	|  类型 		| 是否必填 		| 说明
:----------:|:---------:|:-------------:|---------------------------
uuid  		|   bigint  |是  			|用户唯一标识uuid
token  		|   string  |是  			|Token令牌
id  		|   bigint  |是				|游戏ID  
sign  		|   string  |是  			|参数校验sign

- **返回值说明** ： 

   参数名 	| 参数类型  	|说明
:----------:|:----------|:-------------
code  		|  string  	|请求状态码    
msg  		|  string  	|状态消息   
data  		|  object  	|返回数据   
list  		|  object  	|数据对象  
id  		|  bigint  	|赛区ID  
name  		|  string  	|赛区名  

- **返回数据格式** ：  
{
	"code": "0000",  
	"msg": "操作成功",  
	"data": {  
    　　"list": [{  
    　　　　"id": 1,  
    　　　　"name": "上海1区",    
      　　　},]  
　　}  
}

### 8.创建战队
- **功能定义**	: 创建战队
- **URL**		: `http://<host>/match/create_group`
- **Method**	: Post
- **请求参数**  	:

   参数名 	|  类型 		| 是否必填 		| 说明
:----------:|:---------:|:-------------:|---------------------------
uuid  		|   bigint  |是  			|用户唯一标识uuid
token  		|   string  |是  			|Token令牌
id  		|   bigint  |是				|游戏ID
m_id  		|   int  	|是				|赛区ID
name  		|   string  |是				|战队名
file  		|   二进制流 |是				|战队logo  
sign  		|   string  |是  			|参数校验sign

- **返回值说明** ： 

   参数名 	| 参数类型  	|说明
:----------:|:----------|:-------------
code  		|  string  	|请求状态码    
msg  		|  string  	|状态消息   
data  		|  object  	|返回数据 
url  		|  string  	|战队二维码url  

- **返回数据格式** ：  
{
	"code": "0000",  
	"msg": "操作成功",  
	"data": [  
	　　"url": "http://ip/match/apply_group?uuid=1&group_id=13"  
	]  
}


### 9.获取战队列表
- **功能定义**	: 获取战队列表
- **URL**		: `http://<host>/match/group_list`
- **Method**	: Get
- **请求参数**  	:

   参数名 	|  类型 		| 是否必填 		| 说明
:----------:|:---------:|:-------------:|---------------------------
uuid  		|   bigint  |是  			|用户唯一标识uuid
token  		|   string  |是  			|Token令牌
page  		|   int  	|是				|分页起始页 1开始
count  		|   int  	|是				|每页显示条数
sign  		|   string  |是  			|参数校验sign

- **返回值说明** ： 

   参数名 	| 参数类型  	|说明
:----------:|:----------|:-------------
code  		|  string  	|请求状态码    
msg  		|  string  	|状态消息   
data  		|  object  	|返回数据   
pagecount  	|  int  	|数据总页数  
list  		|  object  	|数据对象  
id  		|  bigint  	|战队ID  
name  		|  string  	|战队名  
icon  		|  string  	|战队icon  
info  		|  string  	|战队口号  
g_id  		|  int  	|游戏ID  
num  		|  int  	|战队人数  
status  	|  int  	|战队审核状态[1审核中2通过2拒绝]  

- **返回数据格式** ：  
{
	"code": "0000",  
	"msg": "操作成功",  
	"data": {  
    　　"pagecount": 1,  
    　　"list": [{  
    　　　　"id": １,  
    　　　　"name": "战队１",  
    　　　　"icon": "http://ip/11/22/1.jpg",  
    　　　　"info": "我的战队",  
    　　　　"g_id": 1,   
	　　　　"num" : 4,  
    　　　　"status": 1,   
      　　　},]  
　　}  
}

### 10.获取战队详情
- **功能定义**	: 获取战队详情
- **URL**		: `http://<host>/match/group_info`
- **Method**	: Get
- **请求参数**  	:

   参数名 	|  类型 		| 是否必填 		| 说明
:----------:|:---------:|:-------------:|---------------------------
uuid  		|   bigint  |是  			|用户唯一标识uuid
token  		|   string  |是  			|Token令牌
id  		|   bigint  |是  			|战队ID
sign  		|   string  |是  			|参数校验sign

- **返回值说明** ： 

   参数名 	| 参数类型  	|说明
:----------:|:----------|:-------------
code  		|  string  	|请求状态码    
msg  		|  string  	|状态消息   
data  		|  object  	|返回数据   
id  		|  bigint  	|战队ID  
name  		|  string  	|战队名  
icon  		|  string  	|战队icon  
info  		|  string  	|战队口号  
g_id  		|  bigint  	|游戏ID
g_name		|  string  	|游戏名  
num			|  int  	|战队人数 
status		|  int  	|战队审核状态[1审核中2通过2拒绝]  
m_id		|  bigint  	|赛区ID
m_name		|  string  	|赛区名
u_list		|  object  	|成员列表
uuid		|  bigint  	|成员UUID
nickname	|  string  	|成员昵称
icon		|  string  	|成员ICON
status		|  int  	|成员认证状态[1普通用户2认证用户]  
type		|  int  	|成员身份[1队长2副队长3普通成员]

- **返回数据格式** ：  
{
	"code": "0000",  
	"msg": "操作成功",  
	"data": {  
    　　"id": 1,  
    　　"name": "战队1",  
    　　"icon": "http://ip/11/22/1.jpg",  
    　　"info" : "",  
    　　"g_id": 1,  
    　　"g_name":"王者荣耀",  
    　　"num": 4,  
    　　"m_id": 1,  
    　　"m_name": "上海区",   
    　　"u_list":[{     
	　　　　"uuid": 1,   
    　　　　"nickname": "****",  
    　　　　"icon": "http://ip/11/22/1.jpg",    
    　　　　"status": 1,   
    　　　　"type": 1,  
	　　　}] 
	}  
}


### 11.获取战队成员信息
- **功能定义**	: 获取战队成员信息
- **URL**		: `http://<host>/match/groupuser_info`
- **Method**	: Get
- **请求参数**  	:

   参数名 	|  类型 		| 是否必填 		| 说明
:----------:|:---------:|:-------------:|---------------------------
uuid  		|   bigint  |是  			|用户唯一标识uuid
token  		|   string  |是  			|Token令牌
id  		|   bigint  |是  			|游戏ID
o_uuid  	|   bigint  |是  			|其他成员UUID
sign  		|   string  |是  			|参数校验sign

- **返回值说明** ： 

   参数名 	| 参数类型  	|说明
:----------:|:----------|:-------------
code  		|  string  	|请求状态码    
msg  		|  string  	|状态消息   
data  		|  object  	|返回数据   
user  		|  object  	|成员信息  
uuid  		|  bigint  	|成员uuid
nickname  	|  string  	|成员昵称  
id_card  	|  string  	|IDcard  
name  		|  bigint  	|姓名
sex			|  int  	|性别[0未知1男2女]  
icon		|  string  	|头像  
age			|  int  	|年龄  
wx_account	|  string  	|微信账号  
status		|  string  	|身份审核状态[1普通2身份证认证]  
type		|  string  	|成员身份[1队长2副队长3成员]  
g_info		|  object  	|游戏信息 
id			|  bigint  	|游戏ID 
name		|  string  	|游戏名
icon		|  string  	|游戏icon
img			|  string  	|游戏大图
g_account	|  string  	|游戏账号
g_nickname	|  string  	|游戏昵称
g_level		|  string  	|游戏level
g_zone		|  int  	|游戏区服

- **返回数据格式** ：  
{
	"code": "0000",  
	"msg": "操作成功",  
	"data": {  
	　　"user":{  
    　　　　"uuid": 1,  
    　　　　"nickname": "test",  
    　　　　"type": 1,  
    　　　　"name" : "**",  
    　　　　"sex": 1,  
    　　　　"age":0,  
    　　　　"icon": "http://ip/11/22/3.png",  
    　　　　"wx_account": "***",  
    　　　　"status": 1,   
	　　　},  
    　　"g_info":{     
	　　　　"id": 1,   
    　　　　"name": "王者荣耀",  
    　　　　"icon": "http://ip/11/22/1.jpg",    
    　　　　"g_account": "测试账号1",     
    　　　　"g_nickname": "_test",    
    　　　　"g_level": "30级",  
    　　　　"g_zone": 1,  
	　　　}  
　　	}  
}



### 12.获取战队分享二维码URL
- **功能定义**	: 获取战队分享二维码URL
- **URL**		: `http://<host>/match/group_url`
- **Method**	: Get
- **请求参数**  	:

   参数名 	|  类型 		| 是否必填 		| 说明
:----------:|:---------:|:-------------:|---------------------------
uuid  		|   bigint  |是  			|用户唯一标识uuid
token  		|   string  |是  			|Token令牌
id  		|   bigint  |是				|战队ID
sign  		|   string  |是  			|参数校验sign

- **返回值说明** ： 

   参数名 	| 参数类型  	|说明
:----------:|:----------|:-------------
code  		|  string  	|请求状态码    
msg  		|  string  	|状态消息   
data  		|  object  	|返回数据 
url  		|  string  	|战队二维码url  

- **返回数据格式** ：  
{
	"code": "0000",  
	"msg": "操作成功",  
	"data": [  
	　　"url": "http://ip/match/apply_group?uuid=1&group_id=13"  
	]  
}


### 13.申请加入战队
- **功能定义**	: 申请加入战队
- **URL**		: `http://<host>/match/apply_group`
- **Method**	: Post
- **请求参数**  	:

   参数名 	|  类型 		| 是否必填 		| 说明
:----------:|:---------:|:-------------:|---------------------------
uuid  		|   bigint  |是  			|用户唯一标识uuid
token  		|   string  |是  			|Token令牌
id  		|   bigint  |是				|战队ID
o_uuid  	|   bigint  |是				|邀请人uuid
sign  		|   string  |是  			|参数校验sign

- **返回值说明** ： 

   参数名 	| 参数类型  	|说明
:----------:|:----------|:-------------
code  		|  string  	|请求状态码    
msg  		|  string  	|状态消息   
data  		|  object  	|返回数据 

- **返回数据格式** ：  
{
	"code": "0000",  
	"msg": "操作成功",  
	"data": []  
}

### 14.获取战队申请列表
- **功能定义**	: 获取战队申请列表
- **URL**		: `http://<host>/match/groupapply_list`
- **Method**	: Get
- **请求参数**  	:

   参数名 	|  类型 		| 是否必填 		| 说明
:----------:|:---------:|:-------------:|---------------------------
uuid  		|   bigint  |是  			|用户唯一标识uuid
token  		|   string  |是  			|Token令牌
id  		|   bigint  |是				|战队ID
page  		|   int  	|是				|分页起始页 1开始
count  		|   int  	|是				|每页显示条数
sign  		|   string  |是  			|参数校验sign

- **返回值说明** ： 

   参数名 	| 参数类型  	|说明
:----------:|:----------|:-------------
code  		|  string  	|请求状态码    
msg  		|  string  	|状态消息   
data  		|  object  	|返回数据
pagecount  	|  int  	|数据总页数
list  		|  object  	|申请列表
id  		|  bigint  	|申请id
uuid  		|  bigint  	|申请人uuid
nickname  	|  string  	|申请人昵称
icon  		|  string  	|申请人头像
wx_account  |  string  	|申请人微信号 

- **返回数据格式** ：  
{
	"code": "0000",  
	"msg": "操作成功",  
	"data": {  
    　　"pagecount": 1,  
    　　"list": [{  
    　　　　"id": １,  
    　　　　"uuid": "test",   
    　　　　"nickname": "test",  
    　　　　"icon": "http://ip/11/22/1.jpg",  
    　　　　"wx_account": "weixin_",     
      　　　},]  
　　}  
}

### 15.战队操作接口
- **功能定义**	: 操作接口包括[1剔除队员2同意用户加入战队3忽略4自动退出战队5指定副队长6取消副队长]
- **URL**		: `http://<host>/match/do_group`
- **Method**	: Post
- **请求参数**  	:

   参数名 	|  类型 		| 是否必填 		| 说明
:----------:|:---------:|:-------------:|---------------------------
uuid  		|   bigint  |是  			|用户唯一标识uuid
token  		|   string  |是  			|Token令牌
id  		|   bigint  |是				|战队ID
o_uuid  	|   bigint  |是				|被操作用户uuid
type  		|   int  	|是				|操作类型[1剔除队员2同意用户加入战队3忽略4自动退出战队5指定副队长6取消副队长]
sign  		|   string  |是  			|参数校验sign

- **返回值说明** ： 

   参数名 	| 参数类型  	|说明
:----------:|:----------|:-------------
code  		|  string  	|请求状态码    
msg  		|  string  	|状态消息   
data  		|  object  	|返回数据

- **返回数据格式** ：  
{
	"code": "0000",  
	"msg": "操作成功",  
	"data": []  
}

### 16.获取战队消息列表
- **功能定义**	: 获取战队消息列表
- **URL**		: `http://<host>/match/groupmsg_list`
- **Method**	: Get
- **请求参数**  	:

   参数名 	|  类型 		| 是否必填 		| 说明
:----------:|:---------:|:-------------:|---------------------------
uuid  		|   bigint  |是  			|用户唯一标识uuid
token  		|   string  |是  			|Token令牌
id  		|   bigint  |是				|战队ID
page  		|   int  	|是				|分页起始页 1开始
count  		|   int  	|是				|每页显示条数
sign  		|   string  |是  			|参数校验sign

- **返回值说明** ： 

   参数名 	| 参数类型  	|说明
:----------:|:----------|:-------------
code  		|  string  	|请求状态码    
msg  		|  string  	|状态消息   
data  		|  object  	|返回数据
pagecount  	|  int  	|数据总页数
list  		|  object  	|申请列表
id  		|  bigint  	|消息id
uuid  		|  bigint  	|用户uuid
nickname  	|  string  	|用户昵称
wx_account  |  string  	|用户微信号 
icon  		|  string  	|用户头像 
type  		|  int  	|消息类型[1加战队2退队3被踢出战队4变队长5变副队长]
time  		|  datetime |记录时间 
o_uuid  	|  bigint  	|操作者uuid


- **返回数据格式** ：  
{
	"code": "0000",  
	"msg": "操作成功",  
	"data": {  
    　　"pagecount": 1,  
    　　"list": [{  
    　　　　"id": １,  
    　　　　"uuid": 14,   
    　　　　"nickname": "tes_14t",  
    　　　　"icon": "http://ip/11/22/1.jpg",  
    　　　　"wx_account": "weixin_14",     
    　　　　"type": 2,  
    　　　　"time": "2017-08-15 11:36:01",     
    　　　　"o_uuid": 1,     
      　　　},]  
　　}  
}




## 约战模块
### 1.我的约战列表-待确认战绩
- **功能定义**	: 获取我的约战列表
- **URL**		: `http://<host>/pmatch/myreserve_list`
- **Method**	: Get
- **请求参数**  	:

   参数名 	|  类型 		| 是否必填 		| 说明
:----------:|:---------:|:-------------:|---------------------------
uuid  		|   bigint  |是  			|用户唯一标识uuid
token  		|   string  |是  			|Token令牌
page  		|   int  	|是				|分页起始页 1开始
count  		|   int  	|是				|每页显示条数
sign  		|   string  |是  			|参数校验sign

- **返回值说明** ： 

   参数名 	| 参数类型  	|说明
:----------:|:----------|:-------------
code  		|  string  	|请求状态码    
msg  		|  string  	|状态消息   
data  		|  object  	|返回数据
pagecount  	|  int  	|数据总页数
list  		|  object  	|申请列表
id  		|  bigint  	|约战id
g_id  		|  bigint  	|游戏ID
name  		|  string  	|游戏名
point  		|  int  	|游戏积分 
icon  		|  string  	|用户头像 
type  		|  int  	|约战类型[1一对一2约组队]
status  	|  int 		|约战状态[1待迎战2待结果3管理员审核中4完成...10过期]
date  		|  datetime |约战时间 
- **返回数据格式** ：  
{
	"code": "0000",  
	"msg": "操作成功",  
	"data": {  
    　　"pagecount": 1,  
    　　"list": [{  
    　　　　"id": １,  
    　　　　"g_id": 2,   
    　　　　"name": "LOL",  
    　　　　"icon": "http://ip/11/22/1.jpg",  
    　　　　"point": 2050,     
    　　　　"type": 2,   
    　　　　"status": 1,     
	　　　　"date": "2017-08-15 11:36:01",     
      　　　},]  
　　}  
}


### ２.我的约战列表-历史战绩
- **功能定义**	: 获取我的历史战绩
- **URL**		: `http://<host>/pmatch/myrecord_list`
- **Method**	: Get
- **请求参数**  	:

   参数名 	|  类型 		| 是否必填 		| 说明
:----------:|:---------:|:-------------:|---------------------------
uuid  		|   bigint  |是  			|用户唯一标识uuid
token  		|   string  |是  			|Token令牌
page  		|   int  	|是				|分页起始页 1开始
count  		|   int  	|是				|每页显示条数
sign  		|   string  |是  			|参数校验sign

- **返回值说明** ： 

   参数名 	| 参数类型  	|说明
:----------:|:----------|:-------------
code  		|  string  	|请求状态码    
msg  		|  string  	|状态消息   
data  		|  object  	|返回数据
pagecount  	|  int  	|数据总页数
list  		|  object  	|申请列表
id  		|  bigint  	|约战id
g_id  		|  bigint  	|游戏ID
name  		|  string  	|游戏名
point  		|  int  	|游戏积分 
icon  		|  string  	|用户头像 
type  		|  int  	|约战类型[1一对一2约组队]
result  	|  int 		|约战结果[1胜2败3平]
point_change|  int 		|积分变更值
date  		|  datetime |约战时间 
- **返回数据格式** ：  
{
	"code": "0000",  
	"msg": "操作成功",  
	"data": {  
    　　"pagecount": 1,  
    　　"list": [{  
    　　　　"id": １,  
    　　　　"g_id": 2,   
    　　　　"name": "LOL",  
    　　　　"icon": "http://ip/11/22/1.jpg",  
    　　　　"point": 2050,     
    　　　　"type": 2,   
    　　　　"status": 1,     
	　　　　"date": "2017-08-15 11:36:01",     
      　　　},]  
　　}  
}

### 3.约战列表
- **功能定义**	: 获取约战列表
- **URL**		: `http://<host>/pmatch/reserve_list`
- **Method**	: Get
- **请求参数**  	:

   参数名 	|  类型 		| 是否必填 		| 说明
:----------:|:---------:|:-------------:|---------------------------
uuid  		|   bigint  |是  			|用户唯一标识uuid
token  		|   string  |是  			|Token令牌
id  		|   bigint  |是  			|游戏ID
page  		|   int  	|是				|分页起始页 1开始
count  		|   int  	|是				|每页显示条数
sign  		|   string  |是  			|参数校验sign

- **返回值说明** ： 

   参数名 	| 参数类型  	|说明
:----------:|:----------|:-------------
code  		|  string  	|请求状态码    
msg  		|  string  	|状态消息   
data  		|  object  	|返回数据
g_id  		|  bigint  	|游戏ID
name  		|  string  	|游戏名
pagecount  	|  int  	|约战列表总页数
list  		|  object  	|约战列表
id  		|  bigint  	|约战id
uuid  		|  int  	|约战用户uuid 
nickname  	|  string  	|约战用户昵称 
icon  		|  string  	|约战用户头像
point  		|  int  	|游戏积分
type  		|  int 		|约战类型[1一对一2约组队]
percent		|  int 		|约战胜率[百分比]
date  		|  datetime |约战时间 
- **返回数据格式** ：  
{
	"code": "0000",  
	"msg": "操作成功",  
	"data": {  
    　　"pagecount": 1,  
    　　"g_id": 1 
    　　"name": "LOL"
    　　"list": [{  
    　　　　"id": １,  
    　　　　"uuid": 2,   
    　　　　"nickname": "test_",  
    　　　　"icon": "http://ip/11/22/1.jpg",  
    　　　　"point": 2050,     
    　　　　"type": 2,   
    　　　　"percent": 89,     
	　　　　"date":"2017-08-15 11:36:01",     
      　　　},]  
　　}  
}

### 4.获取未完成约战数
- **功能定义**	: 获取未完成约战数
- **URL**		: `http://<host>/pmatch/num`
- **Method**	: Get
- **请求参数**  	:

   参数名 	|  类型 		| 是否必填 		| 说明
:----------:|:---------:|:-------------:|---------------------------
uuid  		|   bigint  |是  			|用户唯一标识uuid
token  		|   string  |是  			|Token令牌
sign  		|   string  |是  			|参数校验sign

- **返回值说明** ： 

   参数名 	| 参数类型  	|说明
:----------:|:----------|:-------------
code  		|  string  	|请求状态码    
msg  		|  string  	|状态消息   
data  		|  object  	|返回数据
num  		|  int  	|未完成数

- **返回数据格式** ：  
{
	"code": "0000",  
	"msg": "操作成功",  
	"data": {  
    　　"num": 1,  
　　	}  
}


### ５.发布约战
- **功能定义**	: 发布约战
- **URL**		: `http://<host>/pmatch/reserve`
- **Method**	: Get
- **请求参数**  	:

   参数名 	|  类型 		| 是否必填 		| 说明
:----------:|:---------:|:-------------:|---------------------------
uuid  		|   bigint  |是  			|用户唯一标识uuid
token  		|   string  |是  			|Token令牌
id  		|   bigint  |是				|游戏ID
type  		|   int  	|是				|约战类型[1一对一2约组队]
date  		|   datetime|是				|约战时间
sign  		|   string  |是  			|参数校验sign

- **返回值说明** ： 

   参数名 	| 参数类型  	|说明
:----------:|:----------|:-------------
code  		|  string  	|请求状态码    
msg  		|  string  	|状态消息   
data  		|  object  	|返回数据

- **返回数据格式** ：  
{
	"code": "0000",  
	"msg": "操作成功",  
	"data": []  
}

### 6.迎战|组队操作
- **功能定义**	: 发布约战
- **URL**		: `http://<host>/pmatch/do_pmatch`
- **Method**	: Post
- **请求参数**  	:

   参数名 	|  类型 		| 是否必填 		| 说明
:----------:|:---------:|:-------------:|---------------------------
uuid  		|   bigint  |是  			|用户唯一标识uuid
token  		|   string  |是  			|Token令牌
id  		|   bigint  |是				|约战ID
sign  		|   string  |是  			|参数校验sign

- **返回值说明** ： 

   参数名 	| 参数类型  	|说明
:----------:|:----------|:-------------
code  		|  string  	|请求状态码    
msg  		|  string  	|状态消息   
data  		|  object  	|返回数据
id  		|  bigint  	|约战ID    
type  		|  string  	|约战类型[1一对一2约组队]  
wx_account  |  string  	|对手微信号    
date  		|  datetime |约战时间  

- **返回数据格式** ：  
{
	"code": "0000",  
	"msg": "操作成功",  
	"data": {  
    　　"id": 1,  
    　　"type": 1,  
    　　"wx_account": "wx_test",  
    　　"date" : "2017-08-16 21:00:00",  
	}  
}

### 7.比赛结果处理
- **功能定义**	: 发布约战
- **URL**		: `http://<host>/pmatch/do_result`
- **Method**	: Post
- **请求参数**  	:

   参数名 	|  类型 		| 是否必填 		| 说明
:----------:|:---------:|:-------------:|---------------------------
uuid  		|   bigint  |是  			|用户唯一标识uuid
token  		|   string  |是  			|Token令牌
id  		|   bigint  |是				|约战ID
type  		|   int  	|是				|比赛结果处理[1胜(赞)2负(弱爆了)]
sign  		|   string  |是  			|参数校验sign

- **返回值说明** ： 

   参数名 	| 参数类型  	|说明
:----------:|:----------|:-------------
code  		|  string  	|请求状态码    
msg  		|  string  	|状态消息   
data  		|  object  	|返回数据

- **返回数据格式** ：  
{
	"code": "0000",  
	"msg": "操作成功",  
	"data": [] 
}

### 8.上传截图
- **功能定义**	: 上传比赛截图
- **URL**		: `http://<host>/pmatch/upload_img`
- **Method**	: Post
- **请求参数**  	:

   参数名 	|  类型 		| 是否必填 		| 说明
:----------:|:---------:|:-------------:|---------------------------
uuid  		|   bigint  |是  			|用户唯一标识uuid
token  		|   string  |是  			|Token令牌
id  		|   bigint  |是				|约战ID
file  		|   文件流  	|是				|比赛截图
sign  		|   string  |是  			|参数校验sign

- **返回值说明** ： 

   参数名 	| 参数类型  	|说明
:----------:|:----------|:-------------
code  		|  string  	|请求状态码    
msg  		|  string  	|状态消息   
data  		|  object  	|返回数据

- **返回数据格式** ：  
{
	"code": "0000",  
	"msg": "操作成功",  
	"data": [] 
}

