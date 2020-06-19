<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>خطا در تراکنش</title>
</head>
<style>
    .container{
        width: 80%;
        margin: 0 auto;
        font-family: "B Yekan";
    }
    .txt{
        text-align: center;
        font-size: 20px;
    }
    .btn{
        text-decoration: none;
        box-sizing: border-box;
        background: #00b500;
        color: white;
        border-radius: 15px;
        padding: 20px;
        margin: 50px auto;
        display: block;
        width: 50%;

    }
    .btn:hover{
        background: #00a100;
    }
    .image{
        width: 40%;
        margin: 20px auto 0 auto;
    }
    .image > img{
        width: 100%;
    }
    @media screen and (max-width: 1024px){
        .image{
            width: 60%;
            margin: 100px auto 0 auto;
        }
        .btn{
            font-size: 18px;
            width: 90%;
            padding: 15px;
            line-height: 1.3;
        }
    }
</style>
<body>
<div class="container">
    <div class="image">
        <img src="{{URL::asset('images/cancel.png')}}" alt="">
    </div>
    <div class="txt">
        <h2>تراکنش ناموفق بود</h2>
        <h3>کد پیگیری تراکنش</h3>
        <h4 style="font-family: Arial">{{$id}}</h4>
        <a class="btn" href="#">جهت بازگشت به اپلیکیشن کلیک کنید</a>
    </div>
</div>
</body>
</html>