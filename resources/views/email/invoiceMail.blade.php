<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    {{--<link href="//maxcdn.bootstrapcdn.com/bootstrap/4.1.1/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">--}}
    {{--<link href='https://maxcdn.bootstrapcdn.com/bootstrap/4.1.1/css/bootstrap.min.css'>--}}
    {{--<script src='https://maxcdn.bootstrapcdn.com/bootstrap/4.1.1/js/bootstrap.min.js'></script>--}}
    {{--<script src='https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js'></script>--}}
    <!------ Include the above in your HEAD tag ---------->

    <link href='https://fonts.googleapis.com/css?family=Open+Sans:400,600,700' rel='stylesheet' type='text/css' />

    <title>Invoice</title>

    <style>
        body {
            background-color: #ffffff;
            font-family: IRANSans;
        }
        .title{
            text-align: center;padding: 10px 0 ;width: 40%;margin:0 auto;border-bottom: 1px solid darkgray
        }
        .logo{
            padding: 20px;
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
        .recieptor{
            width: 90%;
            margin: 10px auto;
            text-align: right;
            padding: 0 20px;
        }

        .desc{
            text-align: right;
            padding: 20px 20px 0 0;
            direction: rtl;
        }
        .container{
            width: 70%;
            margin: 0 auto;
            border-radius: 10px;
            background: whitesmoke;
        }
        .table-box{
            width: 90%;
            margin: 10px auto;
        }
        .table-1{
            border-top: 1px solid darkgray;
            width: 100%;
            border-collapse: collapse
        }
        .table-1  tr{
            border-top: 1px solid darkgray;

        }
        .table-1 td{

            padding: 10px;
            border-top: 1px solid darkgray;
            margin: 0;
            text-align: center;
        }
        .table-1 tr:nth-child(even){

            background: #eaeaea;
        }
        .table-1 th{
            padding: 10px;
        }


        .table-box2{

            width: 90%;
            margin: 20px auto;

        }
        .table-2{

            width: 30%;
            /*float: left;*/
            direction: rtl;
            margin-bottom: 20px;
        }
        .table-2 tr:nth-child(even){
            background: none;
        }
        /*.table-box2::before{*/
            /*clear: both;*/
            /*content: "";*/
            /*display: table;*/
        /*}*/
        /*.table-box2::after{*/
            /*clear: both;*/
            /*content: "";*/
            /*display: table;*/
        /*}*/
        .footnote{
            direction: rtl;
            padding: 0 20px 20px 0 ;
        }
        @media screen and (max-width: 768px){

            .container{
                width: 100%;
            }
            .logo img{
                width: 60px;
            }
            .table-2 {

                width: 70%;
            }
        }
    </style>

</head>

<?php
    $cartItems = $cart->cart;
?>
<body>



<div class="container">

    <div class="logo">
        <img src="http://joyvpn.xyz/img/jv.png" alt="">
    </div>
    <h2 class="title">فاکتور مشتری</h2>

    <div class="desc">
    <h4>    شماره فاکتور : {{$cart->code}}</h4>
    <h4>تاریخ : {{\Morilog\Jalali\Jalalian::fromCarbon(\Carbon\Carbon::parse($cart->updated_at))->format('%A %d %B %y')}} </h4>
    <h4>    خریدار : {{$cart->user->name}}</h4>
    <h4>   شماره تماس :  {{$cart->phone}}</h4>
    <h4> آدرس تحویل :  {{$cart->address}}   </h4>
    </div>

    <div class="recieptor">

    </div>
    
        <div class="table-box">
        <table class="table-1">
            <thead>
            <th>قیمت</th>
            <th>تعداد</th>
            <th>فی</th>
            <th>محصول</th>
            <th>ردیف</th>
            </thead>
            <tbody>
            @for($i=0;$i<count($cartItems['cart']);$i++)
            <tr>
                <td>{{$cartItems['cart'][$i]['num']*$cartItems['cart'][$i]['price']}}</td>
                <td>{{$cartItems['cart'][$i]['num']}}</td>
                <td>{{$cartItems['cart'][$i]['price']}}</td>
                <td>{{$cartItems['cart'][$i]['name']}}</td>
                <td>{{$i+1}}</td>
            </tr>
            @endfor
            </tbody>
        </table>
    </div>
    <div class="table-box2">
        <table class="table-1 table-2">
            <tr>
                <th>مجموع</th>
                <td>{{$cart->amount}}</td>
            </tr>
            <tr>
                <th>هزینه ارسال</th>
                <td>25000</td>
            </tr>
            <tr>
                <th>قابل پرداخت </th>
                <td style="font-weight: bold">{{$cart->amount}}</td>
            </tr>
        </table>
    </div>

    <hr>
    <p class="footnote">تهران خیابان جمهوری جنت فلانی کوچه بهمانی</p>
</div>

</body>
</html>