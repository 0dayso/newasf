<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 13-11-4
 * Time: 下午5:33
 */
class AsmsMemberModel extends AsmsModel{

    function __construct(){
        parent::__construct();
        $this->send_url=C('ASMS_HOST').'/service/B2CMemberWebService?wsdl';
    }

    /**会员登陆
     * <?xml version="1.0" encoding="UTF-8"?>
    注：登录方式有4种，根据会员注册名和密码登录;根据会员卡号和密码登录;根据会员手机号码和密码登陆；根据会员邮箱和密码登陆。
    <request>
    <memberNumber>(会员卡号)</memberNumber>
    <password>1111(登陆密码)</password>
    <phone>13971032564(手机号码)</phone>
    <email>1235648@qq.com(Email地址)</email>
    <username>music(登陆账号)</username>
    <websiteCode>1(合作网站代号) 必须</websiteCode>
    <version>1(版本号) 必须</version>
    </request>
     */
    function login($data){
        $str=<<<xml
<?xml version="1.0" encoding="UTF-8"?>
    <request>
    <memberNumber>$data[memberNumber]</memberNumber>
    <password>$data[password]</password>
    <phone>$data[phone]</phone>
    <email>$data[email]</email>
    <username>$data[username]</username>
    <websiteCode>parent::websiteCode</websiteCode>
    <version>parent::websiteCode</version>
    </request>
xml;
        return $this->sendXml('login',$str);
    }

    /**
     * 会员注册
    名称：registration
     * <?xml version="1.0" encoding="UTF-8"?>
    <request>
    <email>582875929@qq.com(EMAIL地址)</email>
    <identificationNumbers>111111111111111111(身份证号码)</identificationNumbers>
    <name>王欣(真实姓名) 必须</name>
    <password>1111(登陆密码) 必须</password>
    <phone>11111111111(手机号码) 必须</phone>
    <username>wangxin(登陆用户名) 必须</username>
    <sex>M(性别 M男 F女,默认M) 必须</sex>
    <clerk>会员的扩展业务员(用户编号)</clerk>
    <websiteCode>1(合作网站代号) 必须</websiteCode>
    <version>1(版本号) 必须</version>
    </request>
     */
    function registration($data=array()){
        $str=<<<xml
<?xml version="1.0" encoding="UTF-8"?>
    <request>
    <email>$data[email]</email>
    <identificationNumbers>$data[identificationNumbers]</identificationNumbers>
    <name>$data[name]</name>
    <password>$data[password]</password>
    <phone>$data[phone]</phone>
    <username>$data[username]</username>
    <sex>$data[sex]</sex>
    <clerk>$data[clerk]</clerk>
    <websiteCode>$this->websiteCode</websiteCode>
    <version>$this->version</version>
    </request>
xml;
        return $this->sendXml('registration',$str);
    }


    /**
     * 3、	接口：会员可用积分查询
    名称：pointsSearch
     * <?xml version="1.0" encoding="UTF-8"?>
    <request>
    <memberId>(会员ID) 必须</memberId>
    <websiteCode>1(合作网站代号) 必须</websiteCode>
    <version>1(版本号) 必须</version>
    </request>
     */

    function pointsSearch($data){
        $str=<<<xml
<?xml version="1.0" encoding="UTF-8"?>
    <request>
    <memberId>$data</memberId>
    <websiteCode>$this->websiteCode</websiteCode>
    <version>$this->version</version>
    </request>
xml;
    return $this->sendXml('pointsSearch',$str);
    }


    /**
     * 会员个人资料或密码修改
    名称：infoPassModify
     * <?xml version="1.0" encoding="UTF-8"?>
    <request>
    <address>(联系地址)</address>
    <answer>111(问题答案)</answer>
    <areOpen>(是否公开 1公开 0不公开 默认为1)</areOpen>
    <companyAddress>(公司地址)</companyAddress>
    <companyFax>(公司传真)</companyFax>
    <companyPhone>(公司电话)</companyPhone>
    <dateOfBirth>(出生日期)</dateOfBirth>
    <documentType>(证件类型 NI身份证 PP护照 ID其他)</documentType>
    <email>(EMAIL)</email>
    <fax>(传真)</fax>
    <gender>(性别 M男 F女)</gender>
    <homePhone>(家庭电话)</homePhone>
    <identificationNumbers>(证件号码)</identificationNumbers>
    <interests>(兴趣爱好)</interests>
    <memberId>120712142346433228(会员ID) 必须</memberId>
    <userName>(会员注册名，注：只用于找回密码)</userName>
    <name>(真实姓名)</name>
    <newPass>1111(新密码)</newPass>
    <oldPass>1111(原密码)</oldPass>
    <phone>(手机)</phone>
    <post>(职务,会员填写)</post>
    <question>您母亲的名字是(密码问题)</question>
    <respectiveDistricts>(所属地区,会员填写)</respectiveDistricts>
    <respectiveProvinces>(所属省份,会员填写)</respectiveProvinces>
    <website>(网址)</website>
    <websiteCode>1(合作网站代号)</websiteCode>
    <workUnit>(工作单位)</workUnit>
    <zipCode>(邮政编码)</zipCode>
    <version>1(版本号) 必须</version>
    </request>
     */
    function infoPassModify($data){
        if(is_array($data)){

        }
        $str=<<<xml
<?xml version="1.0" encoding="UTF-8"?>
    <request>
    <address>$data[address]</address>
    <answer>$data[answer]</answer>
    <areOpen>$data[areOpen]</areOpen>
    <companyAddress>$data[companyAddress]</companyAddress>
    <companyFax>$data[companyFax]</companyFax>
    <companyPhone>$data[companyPhone]</companyPhone>
    <dateOfBirth>$data[dateOfBirth]</dateOfBirth>
    <documentType>$data[documentType]</documentType>
    <email>$data[email]</email>
    <fax>$data[fax]</fax>
    <gender>$data[gender]</gender>
    <homePhone>$data[homePhone]</homePhone>
    <identificationNumbers>$data[identificationNumbers]</identificationNumbers>
    <interests>$data[interests]</interests>
    <memberId>$data[memberId]</memberId>
    <userName>$data[userName]</userName>
    <name>$data[name]</name>
    <newPass>$data[newPass]</newPass>
    <oldPass>$data[oldPass]</oldPass>
    <phone>$data[phone]</phone>
    <post>$data[post]</post>
    <question>$data[question]</question>
    <respectiveDistricts>$data[respectiveDistricts]</respectiveDistricts>
    <respectiveProvinces>$data[respectiveProvinces]</respectiveProvinces>
    <website>$data[website]</website>
    <workUnit>$data[workUnit]</workUnit>
    <zipCode>$data[zipCode]</zipCode>
    <websiteCode>$this->websiteCode</websiteCode>
    <version>$this->version</version>
    </request>
xml;
      return $this->sendXml('infoPassModify',$str);

    }

    /**
     * 会员忘记密码
    名称：forgetPass
     * <?xml version="1.0" encoding="UTF-8"?>
    <request>
    <question>你最喜欢的运动？(密码找回问题)</question>
    <answer>篮球(答案)</answer>
    <flag>0(密码找回标识 0：表示根据密码问题和答案找回密码
    1：表示根据手机号码和会员注册名找回密码)</ flag>
    <phone>13971022789(联系电话)</phone>
    <userName>(会员注册名) 必须</userName>
    <websiteCode>1(合作网站代号) 必须</websiteCode>
    <version>1(版本号) 必须</version>
    </request>
     */
function forgetPass($data){
    $str=<<<xml
<?xml version="1.0" encoding="UTF-8"?>
    <request>
    <question>$data[question]</question>
    <answer>$data[question]</answer>
    <flag>$data[flag]</ flag>
    <phone>$data[phone]</phone>
    <userName>$data[userName]</userName>
    <websiteCode>$this->websiteCode</websiteCode>
    <version>$this->version</version>
    </request>
xml;
    return $this->sendXml('forgetPass',$str);
}

    /**
     * 会员常旅客查询
    名称：frequentFlyerSearch
    <?xml version="1.0" encoding="UTF-8"?>
    <request>
    <count>5(每页显示记录条数) 必须</count>
    <identificationNumbers>42011419885236541(证件号码)</ identificationNumbers>
    <memberId>1236587932(会员ID) 必须</memberId>
    <passengerName>(旅客姓名)</ passengerName>
    <phone>13971022789(联系电话)</phone>
    <start>0(从哪条记录开始) 必须</ start>
    <websiteCode>1(合作网站代号) 必须</websiteCode>
    <version>1(版本号) 必须</version>
    </request>
     */
    function frequentFlyerSearch($data){
        $str=<<<xml
<?xml version="1.0" encoding="UTF-8"?>
    <request>
    <count>$data[count]</count>
    <identificationNumbers>$data[identificationNumbers]</ identificationNumbers>
    <memberId>$data[memberId]</memberId>
    <passengerName>$data[passengerName]</ passengerName>
    <phone>$data[count]</phone>
    <start>$data[start]</ start>
    <websiteCode>$this->websiteCode</websiteCode>
    <version>$this->version</version>
    </request>
xml;
        return $this->sendXml('frequentFlyerSearch',$str);
    }


    /**
     * 7、	接口：会员常旅客新增修改
    名称：frequentFlyerAddOrModify
    请求xml格式：
    <?xml version="1.0" encoding="UTF-8"?>
    <request>
    <address>湖北武汉(地址)</address>
    <assengerFrom>(来源)</assengerFrom>
    <assengerName>zhnagyue(常旅客姓名) 必须</assengerName>
    <companyName>武汉胜意(公司名称)</companyName>
    <dateOfBirth>1987-02-08(出生日期)</dateOfBirth>
    <deptName>研发部(部门名称)</deptName>
    <documentType>P(证件类型,P护照,NI身份证,ID其他)</documentType>
    <gender>M(性别M男 F女)</gender>
    <homePhone>87020856(联系电话)</homePhone>
    <id>12589634(常旅客ID)</id>
    <identificationNumbers>125893244(证件号码) 必须</identificationNumbers>
    <memberId>(会员ID)</memberId>
    <memberNumber>141817(会员卡号)</memberNumber>
    <nationality>中国(会员国籍)</nationality>
    <passportNumber>1418521(证件号码)</passportNumber>
    <passportValid>2012-07-15(证件有效期)</passportValid>
    <phone>1395486234(联系手机) 必须</phone>
    <post>经理(职务)</post>
    <userStatus>1(用户状态 0不启用 1启用)</userStatus>
    <websiteCode>1(合作网站代号) 必须</websiteCode>
    <version>1(版本号) 必须</version>
    </request>
     */

    function frequentFlyerAddOrModify($data){
        $str=<<<xml
<?xml version="1.0" encoding="UTF-8"?>
    <request>
    <address>$data[address]</address>
    <assengerFrom>$data[assengerFrom]</assengerFrom>
    <assengerName>$data[assengerName]</assengerName>
    <companyName>$data[companyName]</companyName>
    <dateOfBirth>$data[dateOfBirth]</dateOfBirth>
    <deptName>$data[deptName]</deptName>
    <documentType>$data[documentType]</documentType>
    <gender>$data[gender]</gender>
    <homePhone>$data[homePhone]</homePhone>
    <id>$data[id]</id>
    <identificationNumbers>$data[identificationNumbers]</identificationNumbers>
    <memberId>$data[memberId]</memberId>
    <memberNumber>$data[memberNumber]</memberNumber>
    <nationality>$data[nationality]</nationality>
    <passportNumber>$data[passportNumber]</passportNumber>
    <passportValid>$data[passportValid]</passportValid>
    <phone>$data[phone]</phone>
    <post>$data[post]</post>
    <userStatus>$data[userStatus]</userStatus>
    <websiteCode>$this->websiteCode</websiteCode>
    <version>$this->version</version>
    </request>
xml;
     return $this->sendXml('frequentFlyerAddOrModify',$str);
    }

    /**
     * 机票订单查询
    名称：ticketOrdersSearch
    请求xml格式：
    <?xml version="1.0" encoding="UTF-8"?>
    <request>
    <arrivalCity>(到达城市 三字码)</arrivalCity>
    <bookingEnd>(订票时间结束)</bookingEnd>
    <bookingStart>(订票时间开始)</bookingStart>
    <count>5(每页显示记录条数)</count>
    <departureCity>(出发城市 三字码)</departureCity>
    <domeOrInter>1(国内国际 1是国内 0是国际)</domeOrInter>
    <memberId>110507150326161026(会员ID) 必须</memberId>
    <orderNumber>(订单号)</orderNumber>
    <start>0(从哪条记录开始) 必须</start>
    <status>(订单状态 0 申请中 1已订座 2已调度 3已出票 4配送中 5部分出票 7客户消 8已取消 9完成 A 未审核)</status>
    <websiteCode>1(合作网站代号) 必须</websiteCode>
    <version>1(版本号) 必须</version>
    </request>
     */
    function ticketOrdersSearch($data){
        $str=<<<xml
<?xml version="1.0" encoding="UTF-8"?>
    <request>
    <arrivalCity>$data[arrivalCity]</arrivalCity>
    <bookingEnd>$data[bookingEnd]</bookingEnd>
    <bookingStart>$data[bookingStart]</bookingStart>
    <count>$data[count]</count>
    <departureCity>$data[departureCity]</departureCity>
    <domeOrInter>$data[domeOrInter]</domeOrInter>
    <memberId>$data[memberId]</memberId>
    <orderNumber>$data[orderNumber]</orderNumber>
    <start>$data[start]</start>
    <status>$data[status]</status>
    <websiteCode>$this->websiteCode</websiteCode>
    <version>$this->version</version>
    </request>
xml;
     return $this->sendXml('ticketOrdersSearch',$str);
    }

    /**
     *
     */

 }
