<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="robots" content="noindex">
    <title>اضافه کردن دستگاه</title>
    <style>
        .container{
            font-family: "B Nazanin";
        }
        .form-box{
            width: 70%;
            border: 1px solid darkgray;
            border-radius: 10px;
            padding: 10px;
            margin: 50px auto;
            background: whitesmoke;
        }
        .form-group{
            direction: rtl;
        }
        .radio{
            display: inline-block;
            width: 30%;

        }
        label{
            display: block;
            text-align: right;
        }
        .form{
            text-align: center;
        }
        .form input{
            padding: 20px;
            width: 80%;
            border: 1px solid black;
            border-radius: 20px;
            margin: 10px auto;
        }
        .btn-box{
            width: 40%;
            margin: 10px auto;
        }
        .btn{
            width: 100%;
            padding: 10px;
            border-radius: 10px;
            border: none;
            background: #00b200;
            color: white;
            font-size: 18px;
            cursor: pointer;
            margin: 20px 0;
        }
        .redirect{
            background: #3ca0ff;
            text-decoration: none;
            margin: 20px 0;
        }
        .btn:hover{
            background: #008f00;
        }
        @media screen and (max-width: 768px){

            .form-box{
                width: 100%;
                box-sizing: border-box;
            }
            .btn-box{
                width: 80%;
                margin: 10px auto;
            }
        }
        .message{
            background: #92c88a;
            text-align: right;
            direction: rtl;
            border-radius: 10px;
            width: 60%;
            margin: 10px auto;
            padding: 10px;
            color: white;
        }
        .error{
            background: #c86e6d;
            text-align: right;
            direction: rtl;
            width: 60%;
            margin: 10px auto;
            padding: 10px;
            border-radius: 10px;
            color: white;
        }
    </style>
</head>
<body>

@if(session()->has('message'))
<div class="message">
    <p>{{session('message')}}</p>
</div>
@endif
@if(session()->has('error'))
    <div class="error">
        <p>{{session('error')}}</p>
    </div>
@endif
@if(count($errors->all())>0)
<div class="error">
    @foreach($errors->all() as $err)
        <p>{{$err}}</p>
    @endforeach
</div>
@endif
<div class="container">
    <div class="form-box">
        <h2 style="text-align: center">اضافه کردن محصول</h2>
         <form style="padding: 20px;" method="POST" action="{{route('addproduct')}}" class="form" enctype="multipart/form-data">
                  <input type="hidden" name="_token" value="{{csrf_token()}}">
               <div class="form-group">
                 <label for="p_name">نام محصول</label>
                 <input name="p_name" type="text" required value="{{Request::old('p_name')}}" class="form-control" id="exampleInputEmail1" aria-describedby="emailHelp" placeholder="نام محصول را وارد کنید">
               </div>

             <div class="form-group">
                 <label for="p_title">عنوان محصول</label>
                 <input name="p_title" type="text" required value="{{Request::old('p_title')}}" class="form-control" id="exampleInputEmail1" aria-describedby="emailHelp" placeholder="عنوان محصول را وارد کنید">
             </div>

               <div class="form-group">
                 <label for="exampleInputEmail1">قیمت (تومان) </label>
                 <input name="price" type="number" required min="1000"   class="form-control" value="{{Request::old('price')}}" id="exampleInputEmail1" aria-describedby="emailHelp" placeholder="قیمت محصول">
             </div>

             <div class="form-group">
                 <label for="exampleInputEmail1">آیکون محصول</label>
                 <input name="img" type="file" required  class="form-control"  id="exampleInputEmail1" aria-describedby="emailHelp" >
             </div>

             <div class="form-group">
                 <label for="exampleInputEmail1">تصویر محصول</label>
                 <input name="page_img" type="file" required  class="form-control"  id="exampleInputEmail1" aria-describedby="emailHelp" >
             </div>

             <div class="form-group">
                 <label for="exampleInputEmail1">توضیح محصول</label>
                 <input name="desc" type="text" required  class="form-control" maxlength="200"  id="exampleInputEmail1" aria-describedby="emailHelp" >
             </div>

             <div class="form-group radio">
                 <label for="available">موجود</label>
                 <input name="available" type="radio" required  class="form-control" id="exampleInputEmail1" value="1" aria-describedby="emailHelp" >
             </div>

             <div class="form-group radio">
                <label for="available">ناموجود</label>
                <input name="available" type="radio" required  class="form-control radio"  id="exampleInputEmail1" value="0" aria-describedby="emailHelp" >
            </div>

               <div class="form-group">
                 <label for="exampleInputPassword1">کلمه عبور</label>
                 <input name="password" type="password" required class="form-control" id="exampleInputPassword1" placeholder="کلمه عبور">
               </div>
                <div class="btn-box">
                    <button type="submit" class="btn">ثبت محصول </button>
                    <br>
                    <a href="{{route('productList')}}" class="btn redirect">لیست محصولات </a>
                </div>
               </form>
        <div style="text-align: center;margin: 20px auto;width: 40%; background: #00AAAA;text-decoration: none">

        </div>
    </div>
</div>

</body>
</html>