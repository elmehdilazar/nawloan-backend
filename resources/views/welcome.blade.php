<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <!-- Favicon icon -->
        <meta name="theme-color" content="#444" />
        <link rel="shortcut icon"
            href="{{setting('favoico')!='' ? asset(setting('favoico')) : asset('uploads/img/logo.png')}}">

        <title>@lang('site.app_name')</title>

        <style>
            *, ::before, ::after {
                box-sizing: border-box;
                margin: 0;
                padding: 0;
            }
            
            body {
                height: 100vh;
                position: relative;
                background-color: black;
            }
            
            #myVideo {
              width: 100vw;
              height: 100vh;
              
              position: fixed;
              left: 0;
              right: 0;
              top: 0;
              bottom: 0;
              z-index: -1;
            }
            
            
            
        </style>
    </head>
    <body>
        <center>
            <video autoplay muted loop id="myVideo">
              <source src="{{ asset('assets/videos/NAWLOAN-APP-COMING-SOON.mp4') }}" type="video/mp4">
              Your browser does not support HTML5 video.
            </video>
        </center>
    </body>
</html>
