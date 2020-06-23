<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>گزارش مشکل</title>
    <style>
        .container{
            width: 80%;
            margin: 50px auto;
            border: 1px solid darkgray;
            border-radius: 10px;
            padding:10px;
            text-align: right;
            font-family: IRANSans;
            background: whitesmoke;
        }
        .title{
            text-align: center;padding: 10px 0 ;width: 40%;margin:0 auto;border-bottom: 1px solid darkgray
        }
        .logo{
            padding: 10px;
            float: left;
        }
        .logo img{
            width: 80px;

        }
        .logo::before{
            clear: both;
            content: "";
            display: table;
        }
        .logo::after{
            clear: both;
            content: "";
            display: table;
        }
        .name{

            direction: rtl;
            font-weight: bold;
        }
        .date{
            /*float: right;*/
            direction: rtl;
        }
        @media screen and (max-width: 768px){

            .container{
                width: 100%;
                box-sizing: border-box;
                font-size: 14px;
            }
            .logo img{
                width: 60px;
            }
            .title {

                font-size: 18px;
            }
            .date{
                font-size: 12px;
            }
        }
    </style>

</head>

<body>

<div class="container">

    <div class="logo">
        <img src="http://joyvpn.xyz/img/jv.png" alt="">
    </div>
    <h2 class="title">پیام کاربر</h2>


    <div class="name">
        <p>نام : {{$user->name}}</p>
        <p>ایمیل : {{$user->email}}</p>
    </div>

    <div class="name">
        <p>موضوع : {{$issue->title}}</p>
        <p> شرح مشکل : </p>
    </div>
    <div>
        <p style="text-align: justify;direction: rtl">
            {{$issue->desc}}
        </p>
    </div>
    <hr>
    <div class="date"><p> تاریخ : {{\Morilog\Jalali\Jalalian::fromCarbon(\Carbon\Carbon::parse($issue->created_at))->format('%A %d %B %y - %M : %H')}}</p></div>
</div>

</body>
</html>