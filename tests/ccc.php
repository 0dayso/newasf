<?php
$xml=<<<xml
<?xml version="1.0" encoding="UTF-8"?>
<OTA_OrderSynRQ xmlns="http://www.trafree.com/OTA/2011/05" Version="1.000" encoding="UTF-8"><POS><Requestors><Requestor Type="13" Password="B2767789E670B0A770923A01CB42B532" ID="aishangfei"/></Requestors></POS><Order OrderId="F130801000912f9" source="fly4free" orderDateTime="2013-08-01 14:43:33" priceDetail="(13690+2925)*2|(10290+2570)*2|" uid="1103"/><BookingReferences><BookingReference><OriginDestinationOption RefNumber="1" FilingAirline="HU"><FlightSegment DepartureDateTime="2013-08-10 11:50:00" ArrivalDateTime="2013-08-10 14:15:00" FlightNumber="7606" ResBookDesigCode="Y"><DepartureAirport LocationCode="SHA" CityCode="SHA"/><ArrivalAirport LocationCode="PEK" CityCode="BJS"/><MarketingAirline Code="HU"/></FlightSegment><FlightSegment DepartureDateTime="2013-08-10 16:20:00" ArrivalDateTime="2013-08-10 12:05:00" FlightNumber="495" ResBookDesigCode="B"><DepartureAirport LocationCode="PEK" CityCode="BJS"/><ArrivalAirport LocationCode="SEA" CityCode="SEA"/><MarketingAirline Code="HU"/></FlightSegment><FlightSegment DepartureDateTime="2013-08-10 13:55:00" ArrivalDateTime="2013-08-10 16:05:00" FlightNumber="1757" ResBookDesigCode="E"><DepartureAirport LocationCode="SEA" CityCode="SEA"/><ArrivalAirport LocationCode="SFO" CityCode="SFO"/><MarketingAirline Code="VX"/></FlightSegment></OriginDestinationOption><OriginDestinationOption RefNumber="2" FilingAirline="HU"><FlightSegment DepartureDateTime="2013-09-16 09:15:00" ArrivalDateTime="2013-09-16 11:18:00" FlightNumber="311" ResBookDesigCode="G"><DepartureAirport LocationCode="SFO" CityCode="SFO"/><ArrivalAirport LocationCode="SEA" CityCode="SEA"/><MarketingAirline Code="AS"/></FlightSegment><FlightSegment DepartureDateTime="2013-09-16 14:00:00" ArrivalDateTime="2013-09-17 16:55:00" FlightNumber="496" ResBookDesigCode="L"><DepartureAirport LocationCode="SEA" CityCode="SEA"/><ArrivalAirport LocationCode="PEK" CityCode="BJS"/><MarketingAirline Code="HU"/></FlightSegment><FlightSegment DepartureDateTime="2013-09-17 21:05:00" ArrivalDateTime="2013-09-17 23:15:00" FlightNumber="7603" ResBookDesigCode="Y"><DepartureAirport LocationCode="PEK" CityCode="BJS"/><ArrivalAirport LocationCode="SHA" CityCode="SHA"/><MarketingAirline Code="HU"/></FlightSegment></OriginDestinationOption><BookingReferenceID ID="HNPTW3" Type="1" Resource="TFR" InfoSource="PEK440"/><AirItineraryPricingInfo><Agent AgentId="aishangfei"><PriceInfo Fare="13590" Tax="2925" Currency="CNY" GroupId="1" PassengerTypeCode="ADT" PassengerQuantity="2" PriceDetail=""/><PriceInfo Fare="10190" Tax="2570" Currency="CNY" GroupId="1" PassengerTypeCode="CNN" PassengerQuantity="2" PriceDetail=""/></Agent><TicketingInfo TicketTimeLimit="2013-08-02 14:43:41"/></AirItineraryPricingInfo></BookingReference></BookingReferences><Travelerinfos><Travelerinfo title="Mr" type="ADT" firstName="hz" lastName="xn" dateBirth="1968-06-06" idType="1" idNumber="1111111" idCountry="CN" idTimeLimit="2018-06-06"/><Travelerinfo title="Ms" type="ADT" firstName="sfz" lastName="xv" dateBirth="1999-05-05" idType="0" idNumber="2222222" idCountry="TW" idTimeLimit="2018-06-06"/><Travelerinfo title="Mr" type="CNN" firstName="gat" lastName="xn" dateBirth="2010-05-05" idType="2" idNumber="3333333" idCountry="HK" idTimeLimit="2018-06-06"/><Travelerinfo title="Ms" type="CNN" firstName="tbz" lastName="xv" dateBirth="2008-09-11" idType="1" idNumber="4444444" idCountry="IT" idTimeLimit="2019-09-09"/></Travelerinfos><Contactinfo account="1103" email="test@test.com" cell="11111111111" name="11/11 Mr"/></OTA_OrderSynRQ>
xml;
//
//    echo $xml;
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "http://localhost/newasf/api/synorder");
curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);
$rs=curl_exec($ch);
curl_close($ch);
header("content-type:text/xml");
print_r( $rs);