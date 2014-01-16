<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 13-11-4
 * Time: 下午5:33
 */
class AsmsModel extends Model{
    public  $send_url;
    private  $params1;
    private  $params2;
    public   $params3;

    public  $websiteCode;
    public  $version;

    function __construct(){
        $this->send_url=C('ASMS_HOST').'/service/B2CTicketWebService?wsdl';
        $this->params1=C('ASMS_ACCOUNT');
        $this->params2=C('ASMS_PWD');
        $this->websiteCode=C('ASMS_WEBSITECODE');
        $this->version=C('ASMS_VERSION');

    }

    function sendXml($act,$xml,$format=''){
        header("Content-Type:text/html;charset=UTF-8");
        date_default_timezone_set(@date_default_timezone_get());
        vendor('nusoap.nusoap','','.php');
        $client = new nusoap_client($this->send_url, true);
        $client->soap_defencoding = 'utf-8';
        $client->decode_utf8 = false;
        $client->xml_encoding = 'utf-8';
        $err = $client->getError();
        if ($err){
            echo '<h2>Constructor error</h2><pre>' . $err . '</pre>';
        }

        $pwd = md5($this->params2);
        $xml=$xml?$xml:$this->params3;
        $params3 = str_replace(PHP_EOL, '', $xml);;
        $params2= md5($params3.$pwd);

        $result = $client->call($act, array('in0'=>$this->params1,'in1'=>$params2,'in2'=>$params3));

        if ($client->fault){
            echo '<h2>Fault</h2><pre>';
            print_r($result);
            echo '</pre>';
        } else {
            $err = $client->getError();   // Check for errors
            if ($err){
                // Display the error
                echo '<h2>Error</h2><pre>' . $err . '</pre>';
            } else {
                if(is_array($result))
                    return $result['out'];
                return $result;
            }
        }
    }


    /**
     * 机票查询接口
     * <?xml version="1.0" encoding="UTF-8"?>
    <request>
    <memberId>10258632541152(会员ID,如果为非会员查询则为非会员ip地址) 必须</memberId>
    <airline>BR(航空公司,为空则匹配所有航空公司)</airline>
    <arrivalCity>WUH(到达城市三字码(国内城市可传城市编号)) 必须</arrivalCity>
    <date>2012-07-05(出发日期) 必须</date>
    <departureCity>PEK(出发城市三字码(国内城市可传城市编号)) 必须</departureCity>
    <queryFlag>查询类型(0国际,1国内) 必须</queryFlag>
    <clerk>会员的扩展业务员(用户编号)</clerk>
    <websiteCode>合作网站代号 必须</websiteCode>
    <version>1(版本号) 必须</version>
    </request>
     * @return mixed
     */
    function searchTicket($data=array()){
$str = <<<xml
<?xml version="1.0" encoding="UTF-8"?>
<request>
<memberId>$data[0]</memberId>
<airline>$data[1]</airline>
<arrivalCity>$data[2]</arrivalCity>
<date>$data[3]</date>
<departureCity>$data[4]</departureCity>
<queryFlag>$data[5]</queryFlag>
<clerk>$data[6]</clerk>
<websiteCode>$this->websiteCode</websiteCode>
<version>$this->version</version>
<rs>1</rs>
</request>
xml;
        return $this->sendXml('searchTicket',$str);
    }


  /**
   * 其余舱位匹配政策
   * 第一屏返回的OtherCw > Cw节点下的HBS xml
  <HBS>
  <HEAD>16AUG(THU) WUHHGH DIRECT ONLY</HEAD>
  <HB>
  <XH>4</XH>
  <HBH>MF8372</HBH>
  <DS>DS#</DS>
  <CW>PC FC JC YC BC HC KC LC MC NC QC TC VC XC RC UC GC ZC SC IC WC EC OC</CW>
  <HC>WUHHGH</HC>
  <CFSJ>1040</CFSJ>
  <DDSJ>1155</DDSJ>
  <JX>737</JX>
  <TLCY>0 L</TLCY>
  <E>E</E>
  <FH>&gt;</FH>
  <GXHBH></GXHBH>
  <CW2></CW2>
  <OTHER>T2 -- 1:15</OTHER>
  </HB>
  </HBS>
   */
    function policyCabin(){
        $str=<<<xml
xml;
        return $this->sendXml('policyCabin',$str);
    }

    /**机票预订接口
     *  <?xml version="1.0" encoding="UTF-8"?>
    <request>
    <dptDate>2012-09-10(出发日期) 必须</dptDate>
    <bakDate>(返程日期yyyy-MM-dd) 必须</bakDate>
    <tripType>1(航程类型,1.单程，2.往返程，3联程) 必须</tripType>
    <adultNumber>1(成人数) 必须</adultNumber>
    <chdNumber>0(儿童数)</chdNumber>
    <infNumber>0(婴儿数)</infNumber>
    <issue>1(出票方式,1直接出票，0为暂缓出票)</issue>
    <tkDate>(留票日期yyyy-MM-dd)</tkDate>
    <tkTime>(留票时间HH:mm)</tkTime>
    <deliveryType>1(配送类型, 1.无需配送，2.市内配送，3.市内自取，4.机场自取) 必须</deliveryType>
    <route>0(是否需要行程单, 1.需要，0.不需要)</route>
    <recipient>(收件人名称)</recipient>
    <post>(邮政编码)</post>
    <postPrice>(邮寄费用)</postPrice>
    <rcptAddress>(收件地址)</rcptAddress>
    <selfGetDate>(自取日期yyyy-MM-dd)</selfGetDate>
    <selfGetTime>(自取时间HH:mm)</selfGetTime>
    <selfGetTime1>(几小时以后自取)</selfGetTime1>
    <deliveryDate>(配送日期yyyy-MM-dd)</deliveryDate>
    <deliveryTime>(配送时间HH:mm)</deliveryTime>
    <deliveryTime1>(几小时以内配送)</deliveryTime1>
    <deliveryAddress>(配送地址)</deliveryAddress>
    <insurancePrice>10(保险价格)</insurancePrice>
    <contactName>zhangyue小强(联系人姓名) 必须</contactName>
    <contactMobile>18607175122(联系人手机) 必须</contactMobile>
    <contactTel>(联系人电话)</contactTel>
    <email>yymusical@qq.com(联系人email)</email>
    <remark>(备注)</remark>
    <memberId>(会员ID)</memberId>
    <memberkh>(会员卡号)</memberkh>
    <compid>CSHT(订票公司ID) 必须</compid>
    <deptid>CSHTDZSW(订票公司部门) 必须</deptid>
    <userid>HTLVW(订票员) 必须</userid>
    <segments>
    <segment>
    <dpt>WUH(出发城市) 必须</dpt>
    <arr>HGH(到达城市三字码) 必须</arr>
    <dptTime>10:50(出发时间) 必须</dptTime>
    <arrTime>12:00(到达时间) 必须</arrTime>
    <airLine>CA8201(航班号) 必须</airLine>
    <carrier>CA(航空公司,二字码 大写) 必须</carrier>
    <discount>0(经停次数)</discount>
    <code>Q(舱位 大写) 必须</code>
    <planType>319(机型) 必须</planType>
    <privilege>0.0(优惠金额)</privilege>
    <zcid>a9b77c0c-3199-495c-beba-1716cb61302f(政策id)</zcid>
    <zclx>5(政策类型 2团，3:HL,4:HK 5:平台政策)</zclx>
    <plat>10100005(平台政策id)</plat>
    </segment>
    </segments>
    <passengers>
    <passenger>
    <name>张悦(乘机人姓名) 必须</name>
    <sex>男(性别) 必须</sex>
    <country>(国籍) 必须</country>
    <zjyxq>(证件有效期 yyyy-MM-dd) 必须</zjyxq>
    <csrq>(出生日期 yyyy-MM-dd) 必须</csrq>
    <certificateType>P(证件类型,P护照,NI身份证,ID其他) 必须</certificateType>
    <certificateNumber>11111(证件号码)</certificateNumber>
    <passengerType>1(乘机人类型, 1.成人，2.儿童，3.婴儿) 必须</passengerType>
    <insuranceDeal>1(保险份数)</insuranceDeal>
    <price>430.0(去程票价) 必须</price>
    <cheap>0.0(去程让利价)</cheap>
    <price1>0.0(回程票价) 必须</price1>
    <cheap1>0.0(回程让利价)</cheap1>
    <airTax>50.0(去程机场建设费) 必须</airTax>
    <airTax1>0.0(回程机场建设费) 必须</airTax1>
    <tax>50.0(去程燃油税) 必须</tax>
    <tax1>0.0(回程燃油税) 必须</tax1>
    </passenger>
    </passengers>
    　　<websiteCode>1(合作网站代号) 必须</websiteCode>
    　　<version>1(版本号) 必须</version>
    </request>
     */
    function ticketBooking(){
     $str=<<<xml
xml;
     return $this->sendXml('ticketBooking',$str);
    }

    /**
     * 获取配送自取地址接口
    名称：getDeliveryAddress
     * <?xml version="1.0" encoding="UTF-8"?>
    <request>
    <compid>XYJ(总公司id)</compid>
    <deptCategory>(部门类别（2-自取票部门 3-机场取票部门）)</deptCategory>
    <deptFunctions>10088003 (
    部门职能，见b_class id=10088) 必须</deptFunctions>
    <deptType>(部门类型（105501-自有部门 105502-分销商 105503-外入单位105504-外派单位 105505-外出票单位）)</deptType>
    <isValid>(是否有效  0无效  1有效  默认为1)</isValid>
    <orderBy>(排序条件)</orderBy>
    <subDept>true(是否包含下级部门,默认是不包含,true包含、false不包含)</subDept>
    <websiteCode>1(合作网站代号) 必须</websiteCode>
    <whereCity>true(为"true"取所在城市信息，默认为"false"，主要用于订单详细取自取票城市)</whereCity>
    <version>1(版本号) 必须</version>
    </request>
     */

    function getDeliveryAddress(){
        $str=<<<xml
xml;
        return $this->sendXml('getDeliveryAddress',$str);
    }

    /**
     *机票保险接口
     * <?xml version="1.0" encoding="UTF-8"?>
    <request>
    <websiteCode>1(合作网站代号) 必须</websiteCode>
    <version>1(版本号) 必须</version>
    </request>
     */
    function getInsurance(){
        $str=<<<xml
xml;
        return $this->sendXml('getInsurance',$str);
    }


    /**
     * 获取票价接口
    名称：getTicketPrice
     * <?xml version="1.0" encoding="UTF-8"?>
    <request>
    <ycabinPrice>150.00(Y舱价格)</ycabinPrice>
    <price>150.00(销售价)</price>
    <route>WUHPEK(航程,机场三字码) 必须</route>
    <carrier>BR(航空公司) 必须</carrier>
    <code>Y(舱位代码,大写) 必须</code>
    <planType>(飞机机型)</planType>
    <date>2012-07-06(出发日期) 必须</date>
    <version>1(版本号) 必须</version>
    </request>
     */
    function getTicketPrice(){
        $str=<<<xml
xml;
        return $this->sendXml('getTicketPrice',$str);
    }

    /**
     * 订单价格PAT比较接口
    名称：orderPat
     * <?xml version="1.0" encoding="UTF-8"?>
    <request>
    <orderNumber>(订单编号) 必须</orderNumber>
    <websiteCode>合作网站代号 必须</websiteCode>
    <version>1(版本号) 必须</version>
    </request>
     */
    function orderPat($data=array()){
        $str=<<<xml
xml;
        return $this->sendXml('orderPat',$str);
    }

    /**
     * 8、	接口：PAT获取特价
    名称：orderPAT
     * <?xml version=\"1.0\" encoding=\"UTF-8\"?>
    <request>
    <hbh>MU5186（航班号必须）</hbh>
    <cw>K（舱位必须）</cw>
    <cfcity>SHA（出发城市必须）</cfcity>
    <ddcity>PEK（到达城市必须）</ddcity>
    <cfdate>2013-07-31（出发日期必须）</cfdate>
    <websiteCode>1（合作网站代号 必须）</websiteCode>
    <version>1（接口版本 必须）</version>
    </request>
     */

    function special($data=array()){
$str=<<<xml
<?xml version=\"1.0\" encoding=\"UTF-8\"?>
    <request>
    <hbh>$data[0]</hbh>
    <cw>$data[1]</cw>
    <cfcity>$data[2]</cfcity>
    <ddcity>$data[3]</ddcity>
    <cfdate>$data[4]</cfdate>
    <websiteCode>$data[5]</websiteCode>
    <version>$data[6]</version>
    </request>
xml;
    return $this->sendXml('orderPAT',$str);

    }

    /**
    经停
    名称：getStopover
     * <?xml version=\"1.0\" encoding=\"UTF-8\"?>
    <request>
    <hbh>ZH9553（航班号 必须）</hbh>
    <cfcity>CAN（出发城市 必须）</cfcity>
    <ddcity>LHW（到达城市 必须）</ddcity>
    <date>2013-08-31（出发日期 必须）</date>
    <websiteCode>1（合作网站代号 必须）</websiteCode>
    <version>1（接口版本 必须）</version>
    </request>
     */
    function getStopover($data=array()){
        $str=<<<xml
<?xml version=\"1.0\" encoding=\"UTF-8\"?>
    <request>
    <hbh>$data[0]</hbh>
    <cfcity>$data[0]</cfcity>
    <ddcity>$data[0]</ddcity>
    <date>$data[0]</date>
    <websiteCode>$this->websiteCode</websiteCode>
    <version>$this->version</version>
    </request>
xml;
        return $this->sendXml('orderPAT',$str);
    }




 }
