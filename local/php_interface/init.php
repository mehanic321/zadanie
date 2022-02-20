<?php
use Bitrix\Main;
use Bitrix\Main\Application;
use Bitrix\Main\Web\Cookie;
use Bitrix\Main\Web\Uri;

Main\EventManager::getInstance()->addEventHandler(
    'sale',
    'OnSaleOrderSaved',
    'ModifySaleOrderSaved'
);

function ModifySaleOrderSaved(Main\Event $event)
{
    $isNew = $event->getParameter("IS_NEW");
    if(!$isNew) return false;

    $order = $event->getParameter("ENTITY");
    $context = Application::getInstance()->getContext();
    $request = $context->getRequest();
    $response = $context->getResponse();
    $server = $context->getServer();

    $uriReferer = new Uri($server->get("HTTP_REFERER"));
    parse_str($uriReferer->getQuery(), $uriParams);
    $utmSource = $uriParams["utm_source"];

    foreach ($order->getPropertyCollection() as $property) {
        if($property->getField('CODE') != "UTM_SOURCE") continue;
        $property->setField("VALUE", $utmSource);
    }

    $result = $order->save();
    if (!$result->isSuccess())
    { 
        var_dump($result->getErrorMessages());
        die;
    }

    $response->addCookie(new Cookie('UTM_SOURCE', $utmSource));
}
?>