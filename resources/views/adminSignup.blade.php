<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="robots" content="noindex">
             <script  src="https://cdnjs.cloudflare.com/ajax/libs/axios/0.18.0/axios.js" ></script>
             <script src="https://unpkg.com/axios/dist/axios.min.js"></script>
    <title>Admin Singup</title>
</head>

<style>
    *{
        font-family:"B Yekan";
    }
    .container{
        width: 100%;
    }
    .register-box{
        width: 50%;
        margin: 50px auto;
        border: 1px solid black;
        border-radius: 10px;
        padding: 10px;
        background: whitesmoke;
        text-align: center;

    }
    .form-group > input{
        display: block;
        border: 1px solid black;
        border-radius: 10px;
        width: 80%;
        margin: 10px auto;
        padding: 15px;
        font-size: 18px;
        box-sizing: border-box;

    }
    .form-group > label{
        display: block;
        width: 80%;
        margin: 15px auto;
        text-align: right;
    }
    h1{
        text-align: center;
    }
    .btn-container{
        width: 20%;
        margin: 0 auto;
    }
    .submit-btn{
        padding: 10px;
        border: 1px solid;
        border-radius: 10px;
        width: 100%;
        background: #00bd00;
        color: white;
        font-size: 20px;
        cursor: pointer;
        margin: 15px 0 0 0;

    }
    .submit-btn:hover{
        background: #009b00;
    }
    .alert{
        background: rgba(252, 0, 0, 0.21);
        padding: 10px;
        width: 50%;
        margin: 100px auto;
        border-radius: 10px;
    }
    .alert > ul{
        text-align: right;
        list-style: none;
    }

    .sucsses{
        background: rgba(26, 252, 9, 0.21);
        padding: 10px;
        width: 50%;
        margin: 100px auto;
        border-radius: 10px;
        text-align: right;
    }

    @media screen and (min-width: 300px) and (max-width: 1280px){

        .register-box{
            width: 90%;
        }
        .form-group > input{
            width: 100%;
        }

        .form-group > label{
            width: 100%;
        }
        .btn-container{
            width: 70%;
        }
    }


</style>

<body>
<div class="container">

    @if ($errors->any())
        <div class="alert">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if (session()->has('message'))
        <div class="sucsses">
           <p>{{session('message')[0]}}</p>
        </div>
    @endif
    <div class="register-box">
        <h1>ثبت ادمین</h1>
         <form style="padding: 20px;"  class="form-box" method="post" action="{{route('adminSignup')}}">
                  <input type="hidden" name="_token" value="{{csrf_token()}}">
               <div class="form-group">
                 <label for="name">نام کاربری</label>
                 <input name="name" type="text" value="{{Request::old('name')}}" class="form-control" required id="name" aria-describedby="emailHelp" placeholder="نام کاربری خود را وارد کنید">
               </div>

               <div class="form-group">
                 <label for="exampleInputEmail1">ایمیل</label>
                 <input name="email" type="email" class="form-control" value="{{Request::old('email')}}" required id="email" aria-describedby="emailHelp" placeholder="ایمیل خود را وارد کنید">
               </div>

                 {{--<div class="form-group">--}}
                     {{--<label for="exampleInputEmail1">شماره همراه</label>--}}
                     {{--<input name="phone" type="tel"  class="form-control"  value="{{Request::old('phone')}}" required id="phone" aria-describedby="emailHelp" placeholder="شماره همراه خود را وارد کنید">--}}
                 {{--</div>--}}

               <div class="form-group">
                 <label for="exampleInputPassword1">کلمه عبور</label>
                 <input name="password" type="password" class="form-control" required id="password" placeholder="کلمه عبور">
               </div>

             <div class="form-group">
                 <label for="exampleInputPassword1">تایید کلمه عبور</label>
                 <input name="password_confirmation" type="password" class="form-control" required id="password" placeholder="تایید کلمه عبور">
             </div>
                <div class="btn-container">
                 <button type="submit" class="submit-btn">ثبت نام </button>
                </div>

         </form>
    </div>
</div>
</body>
</html>