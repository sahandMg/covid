<?php
/**
 * Created by PhpStorm.
 * User: Sahand
 * Date: 7/13/20
 * Time: 4:24 PM
 */

namespace App\Providers;

namespace App;

class NajvaNotif
{
    public $destination;
    public $sendToAll;

    function __construct($sendToAll){
        $this->sendToAll = $sendToAll;
        if ($sendToAll == true){
            $this->destination = "https://app.najva.com/api/v1/notifications/";
        } else {
            $this->destination = "https://app.najva.com/notification/api/v1/notifications/";
        }
    }

    public $title;
    public $body;
    public $onClickAction;
    public $url;
    public $content;
    public $json;
    public $icon;
    public $image;
    public $sentTime;
    public $segmentInclude;
    public $segmentExclude;
    public $oneSignalEnabled = false;
    public $oneSignalAccounts;
    public $subscribersToken;

}