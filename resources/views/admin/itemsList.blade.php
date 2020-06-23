<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    {{--<meta name="viewport"--}}
          {{--content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">--}}
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
     <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
    <title>لیست محصولات</title>
    <style>
        .table-box{
            width: 80%;
            margin: 50px auto;
        }
        .table th{
           text-align: right;
        }
        td{
            text-align: right;
            direction: rtl;
        }
        .btn{
            padding: 10px;
            background: #00AAAA;
            border-radius: 10px;
            text-decoration: none;
            color: white;
            width: 100%;
        }
        .btn:hover{
            color: #d2fcb6;
        }
        .remove{
            background: darkred;
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
        .add{
            width: 20%;
            background: #82d090;
            margin: 10px 0;
            float: right;
            font-weight: bold;
            color:black;
        }
        .btn-box::after,::before{
            content: "";
            display: table;
            clear: both;
        }
        @media screen and (max-width: 768px){
            .add{
                width: 50%;
            }
        }
    </style>
</head>
<body>
<div class="container">
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

    <div class="table-box">
        <div class="btn-box">
            <a class="add btn" href="{{route('addproduct')}}">اضافه کردن محصول</a>
        </div>
        <br>
        @if(count($products) > 0)
            <table class="table table-strip">
                <thead>
                <th>حذف</th>
                <th>آپدیت</th>
                <th>عکس</th>
                <th>توضیحات</th>
                <th>قیمت</th>
                <th>محصول</th>
                <th>ردیف</th>
                </thead>
                <tbody>
                    @foreach($products as $key=>$item)
                        <tr>
                            <td><a href="{{route('removeItem',['name'=>$item->p_name])}}" class="btn remove">حذف</a></td>
                            <td><a href="{{route('updateProduct',['name'=>$item->p_name])}}" class="btn">آپدیت</a></td>
                            <td><img width="50" src="{{URL::asset('images/'.$item->img)}}" alt=""></td>
                            <td>{{$item->desc}}</td>
                            <td>{{$item->price}}</td>
                            <td>{{$item->p_name}}</td>
                            <td>{{$key+1}}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
</div>
</body>
</html>