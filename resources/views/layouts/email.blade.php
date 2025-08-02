<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" 
    lang="ar" dir="rtl"
>
<head>    
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Amiri" rel="stylesheet">
</head>
<body style="direction: rtl;
font-family: 'Amiri-Bold', Avenir, Helvetica, sans-serif; box-sizing: border-box; background-color: #f5f8fa; color: #74787e; height: 100%; hyphens: auto; line-height: 1.4; margin: 0; -moz-hyphens: auto; -ms-word-break: break-all; width: 100% !important; -webkit-hyphens: auto; -webkit-text-size-adjust: none; word-break: break-word;">
<style>
        .btn:hover {
          background-color: #3498db !important;
          border-color: #3498db !important; 
          box-shadow:0px 0px 0px #fff !important;
        }
    @media only screen and (max-width: 600px) {
        .inner-body {
            width: 100% !important;
        }
        .footer {
            width: 100% !important;
        }
    }
    @media only screen and (max-width: 500px) {
        .button {
            width: 100% !important;
        }
    }    
</style>@yield('styles')
<table class="wrapper" width="100%" cellpadding="0" cellspacing="0"
 style="font-family: 'Amiri-Bold',Avenir, Helvetica, sans-serif; box-sizing: border-box;
  background-color: #f5f8fa; margin: 0; padding: 0; width: 100%; -premailer-cellpadding: 0; -premailer-cellspacing: 0; -premailer-width: 100%;">
    <tr>
        <td align="center" style="font-family: 'Amiri-Bold',Avenir, Helvetica, sans-serif; box-sizing: border-box;">
            <table class="content" width="100%" cellpadding="0" cellspacing="0" style="font-family: 'Amiri-Bold',Avenir, Helvetica, sans-serif; box-sizing: border-box; margin: 0; padding: 0; width: 100%; -premailer-cellpadding: 0; -premailer-cellspacing: 0; -premailer-width: 100%;">
                <tr style="background-color: #324191   ;">
                    <td class="header" style="font-family: 'Amiri-Bold',Avenir, Helvetica, sans-serif; box-sizing: border-box; padding: 25px 0; text-align: center;">
                        <a href="{{$data['site_link']}}" style="font-family: 'Amiri-Bold',Avenir, Helvetica, sans-serif; box-sizing: border-box; color: #bbbfc3; font-size: 19px; 
                        font-weight: 700; text-decoration: none; text-shadow: 0 1px 0 #ffffff;">
                            <img src="{{ setting('favoico')!=''?asset(setting('favoico')) : URL::asset('uploads/img/logo.12') }}"
                            style="max-width: 120px;" alt="{{ setting('app_name')!=''?asset(setting('app_name')) :__('site.app_name')}}">
                        </a>
                    </td>
                </tr>
                <tr>
                    <td class="body" width="100%" cellpadding="0" cellspacing="0"
                     style="font-family: 'Amiri-Bold',Avenir, Helvetica, sans-serif; box-sizing: border-box;
                      background-color: #ffffff; border-bottom: 1px solid #edeff2;
                       border-top: 1px solid #edeff2; margin: 0; padding: 0; 
                       width: 100%; -premailer-cellpadding: 0; -premailer-cellspacing: 0; -premailer-width: 100%;">
                        <table class="inner-body" align="center" width="80%" cellpadding="0" cellspacing="0" 
                        style="font-family: 'Amiri-Bold',Avenir, Helvetica, sans-serif; box-sizing: border-box;
                         background-color: #ffffff; margin: 0 auto; padding: 0; width: 80%;
                          -premailer-cellpadding: 0; -premailer-cellspacing: 0; -premailer-width:  80%;">
                            <!-- Body content -->
                            <tr>
                                <td class="content-cell" 
                                style="font-family: 'Amiri-Bold',Avenir, Helvetica, sans-serif; box-sizing: border-box; padding: 35px;">
                                    @yield('content')
                                    <br>
                                    <hr>
                                    <br>
                                    <p style="text-align:center ;font-weight: 600">
                                        @lang('site.Regards')<br>
                                    </p>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr style="background-color: #324191   ;">
                    <td style="font-family: 'Amiri-Bold',Avenir, Helvetica, sans-serif; box-sizing: border-box;">
                        <table class="footer" align="center" width="570" cellpadding="0" cellspacing="0" style="font-family: 'Amiri-Bold',Avenir, Helvetica, sans-serif; box-sizing: border-box; margin: 0 auto; padding: 0; text-align: center; width: 570px; -premailer-cellpadding: 0; -premailer-cellspacing: 0; -premailer-width: 570px;">
                            <tr>
                                <td class="content-cell" align="center" style="font-family: 'Amiri-Bold',Avenir, Helvetica, sans-serif; box-sizing: border-box; padding: 35px;">
                                    <p style="font-family: 'Amiri-Bold',Avenir, Helvetica, sans-serif; box-sizing: border-box; line-height: 1.5em; margin-top: 0; color: #ffffff; font-size: 12px; text-align: center;font-weight: 600">
                                         @lang('site.copyright') <span style="color:#ffffff">&copy; {{date('Y')}}</span> @lang('site.rights_reserved')
                                            <a href="{{route('home')}}" title=" {{setting('app_name')}}" style="color:#fff;text-decoration:none"> {{setting('app_name')}}</a>
                                    </p>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
</body>
</html>
