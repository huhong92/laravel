<!doctype html>
<html lang="{{ app()->getLocale() }}">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <script src="https://code.jquery.com/jquery-3.2.1.min.js"></script>

        <title>Laravel</title>

        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css?family=Raleway:100,600" rel="stylesheet" type="text/css">

        <!-- Styles -->
        <style>
            html, body {
                background-color: #fff;
                color: #636b6f;
                font-family: 'Raleway', sans-serif;
                font-weight: 100;
                height: 100vh;
                margin: 0;
            }

            .full-height {
                height: 100vh;
            }

            .flex-center {
                align-items: center;
                display: flex;
                justify-content: center;
            }

            .position-ref {
                position: relative;
            }

            .top-right {
                position: absolute;
                right: 10px;
                top: 18px;
            }

            .content {
                text-align: center;
            }

            .title {
                font-size: 84px;
            }

            .links > a {
                color: #636b6f;
                padding: 0 25px;
                font-size: 12px;
                font-weight: 600;
                letter-spacing: .1rem;
                text-decoration: none;
                text-transform: uppercase;
            }

            .m-b-md {
                margin-bottom: 30px;
            }
        </style>
    </head>
    <body>
        <div class="flex-center position-ref full-height">
            @if (Route::has('login'))
                <div class="top-right links">
                    @if (Auth::check())
                        <a href="{{ url('/home') }}">Home</a>
                    @else
                        <a href="{{ url('/login') }}">Login</a>
                        <a href="{{ url('/register') }}">Register</a>
                    @endif
                </div>
            @endif

            <div class="content">
                <div class="title m-b-md">
                    Laravel
                </div>

                <div class="links">
                    <a href="https://laravel.com/docs">Documentation</a>
                    <a href="https://laracasts.com">Laracasts</a>
                    <a href="https://laravel-news.com">News</a>
                    <a href="https://forge.laravel.com">Forge</a>
                    <a href="https://github.com/laravel/laravel">GitHub</a>
                </div>

                <div class="links">generate2_qrcode
                    <form action="{{ url('pub/generate2_qrcode') }}" method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="_method" value="Post">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        {{--uuid:<input type="text" name="uuid" value="1"><br />--}}
                        {{--token:<input type="text" name="token" value="222"><br />--}}
                        sign:<input type="text" name="sign" value="11111"><br />
                        url:<input type="text" name="url" value="http://www.baidu.com"><br />
                        logo:<input type="file" name="file" value=""><br />
                        text:<input type="text" name="text" value="微信二维码，扫一扫进入小程序"><br />
                        textcolor:<input type="text" name="color" value="255,0,0"><br />
                        bgcolor:<input type="text" name="bgcolor" value="255,0,0"><br />
                        p:<input type="text" name="p" value="22"><br />
                        e:<input type="text" name="e" value="L"><br />
                        <button type="submit" name="提交" value="">提交2</button>
                    </form>
                </div>


                <div class="links">
                    <form action="{{ url('pub/encode_qrcode') }}" method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="_method" value="Post">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        {{--uuid:<input type="text" name="uuid" value="1"><br />--}}
                        {{--token:<input type="text" name="token" value="222"><br />--}}
                        sign:<input type="text" name="sign" value="11111"><br />
                        file:<input type="file" name="file" value=""><br />
                        fontsize:<input type="text" name="fontsize" value="200"><br />
                        x:<input type="text" name="x" value="60"><br />
                        y:<input type="text" name="y" value="0"><br />
                        text:<input type="text" name="text" value="微信二维码，扫一扫进入小程序"><br />
                        color:<input type="text" name="color" value="255,0,0,20"><br />
                        <button type="submit" name="提交" value="">提交2</button>
                    </form>
                </div>

                <div>

                    {{--<button id="js_register_">提交注册</button>--}}

                </div>


                <div class="links">
                    <form action="http://54.223.105.156/api/public/pub/decode_qrcode" method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="_method" value="Post">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        {{--uuid:<input type="text" name="uuid" value="1"><br />--}}
                        {{--token:<input type="text" name="token" value="222"><br />--}}
                        sign:<input type="text" name="sign" value="11111"><br />

                        file:<input type="file" name="file" value=""><br />

                        <button type="submit" name="提交" value="">提交2</button>
                    </form>
                </div>

            </div>
        </div>
    </body>
<script>
    $(function(){
        $("#js_register_").click(function(){
            alert(22);

            $.post("/laravel5/public/user/register",{'mobile':3333,'verify_code':33333,'sign':6666},function(result){
                console.log(result);
            });
        });



    });


</script>
</html>
