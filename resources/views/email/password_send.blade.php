<!DOCTYPE html
    PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:200,200i,300,300i,400,400i,600,600i,700,700i,900,900i&display=swap" rel="stylesheet">
    <style type="text/css">
        body{
            font-family: 'Source Sans Pro', sans-serif !important;
        }
        p, span, a, h1, h2, h3, h4, h5, h6, div, b, strong, td{
            font-family: 'Source Sans Pro', sans-serif !important;
        }
        p, span, a, span span, td{font-size: 16px !important;}
        strong, strong span{    color: #272f47 !important;}
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
    </style>
</head>
<body style="font-family: 'Source Sans Pro', sans-serif !important;box-sizing: border-box; background-color: transparent; color: #74787E; height: 100%; hyphens: auto; line-height: 1.4; margin: 0; -moz-hyphens: auto; -ms-word-break: break-all; width: 100% !important; -webkit-hyphens: auto; -webkit-text-size-adjust: none; word-break: break-word;">
<table class="wrapper" width="800px" cellpadding="0" cellspacing="0" style="font-family: 'Source Sans Pro', sans-serif !important; box-sizing: border-box; background-color: #e5e6e9; margin: 0 auto; padding: 0; width: 800px; -premailer-cellpadding: 0; -premailer-cellspacing: 0; -premailer-width: 100%;">
    <tbody>
        <tr>
            <td align="left" style="font-family: 'Source Sans Pro', sans-serif !important; box-sizing: border-box;">
                <table class="content" width="100%" cellpadding="0" cellspacing="0" style="font-family: 'Source Sans Pro', sans-serif !important; box-sizing: border-box; margin: 0; padding: 0; width: 100%; -premailer-cellpadding: 0; -premailer-cellspacing: 0; -premailer-width: 100%;">
                    <tbody>

                        <!-- Email Body -->
                        <tr>
                            <td class="body" width="100%" cellpadding="0" cellspacing="0" style="font-family: 'Source Sans Pro', sans-serif !important; box-sizing: border-box; background-color: #e5e6e9; border-bottom: none; border-top: none; margin: 0; padding: 0; width: 100%; -premailer-cellpadding: 0; -premailer-cellspacing: 0; -premailer-width: 100%;">
                                <table class="inner-body" align="left" width="770px" cellpadding="0" cellspacing="0" style="font-family: 'Source Sans Pro', sans-serif !important; float:none !important;box-sizing: border-box; background-color: #ffffff; margin: 15px auto 0 auto; padding: 0; width: 770px; -premailer-cellpadding: 0; -premailer-cellspacing: 0; -premailer-width: 770px;">
                                    <tbody>
                                    <!-- Body content -->
                                    <tr>
                                        <td class="content-cell" style="font-family: 'Source Sans Pro', sans-serif !important; box-sizing: border-box; padding: 20px;">
                                            <table class="action" align="center" width="100%" cellpadding="0" cellspacing="0" style="font-family: 'Source Sans Pro', sans-serif !important; box-sizing: border-box; margin: 0 auto; padding: 0; text-align: center; width: 100%; -premailer-cellpadding: 0; -premailer-cellspacing: 0; -premailer-width: 100%;">
                                                <tbody>
                                                <tr>
                                                    <td align="left" style="font-family: 'Source Sans Pro', sans-serif !important; box-sizing: border-box;">
                                                        <table width="100%" border="0" cellpadding="0" cellspacing="0" style="font-family: 'Source Sans Pro', sans-serif !important; box-sizing: border-box;">
                                                            <tbody>
                                                            <tr>
                                                                <td align="left" style="font-family: 'Source Sans Pro', sans-serif !important; box-sizing: border-box;">
                                                                    <table width="100%" border="0" cellpadding="0" cellspacing="0" style="font-family: 'Source Sans Pro', sans-serif !important; box-sizing: border-box;width: 100%;">
                                                                        <tbody>
                                                                        {{-- <tr>
                                                                            <td>
                                                                                <a href="{{ URL('/') }}" style="    font-family: 'Source Sans Pro', sans-serif !important;box-sizing: border-box; color: #bbbfc3; font-size: 19px; font-weight: bold; text-decoration: none; text-shadow: 0 1px 0 white;">
                                                                                    <img src="{{ asset('/images/logo_blue.png')}}" style="width: 111px;height: 40px;">
                                                                                </a>
                                                                            </td>
                                                                        </tr> --}}
                                                                        <tr><td height="15px"></td></tr>
                                                                        <tr>
                                                                            <td>
                                                                                {!! str_replace('[content]',  $password, $body) !!}
                                                                            </td>
                                                                        </tr>
                                                                        </tbody>
                                                                    </table>
                                                                </td>
                                                            </tr>
                                                            </tbody>
                                                        </table>
                                                    </td>
                                                </tr>
                                                </tbody>
                                            </table>
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                            </td>
                        </tr>


                        <!-- Email footer -->
                        <tr>
                            <td style="font-family: 'Source Sans Pro', sans-serif !important; box-sizing: border-box;">
                                <table class="footer" align="center" width="800px" cellpadding="0" cellspacing="0" style="font-family: 'Source Sans Pro', sans-serif !important; box-sizing: border-box; margin: 0 auto; padding: 0; text-align: center; width: 800px; -premailer-cellpadding: 0; -premailer-cellspacing: 0; -premailer-width: 800px;">
                                    <tbody>
                                    <tr><td height="15px"></td></tr>
                                    <tr>
                                        <td class="content-cell" align="center" style="font-family: 'Source Sans Pro', sans-serif !important;color: #AEAEAE; font-size: 13px;  font-weight: 400; box-sizing: border-box;">
                                            <p style="font-family: 'Source Sans Pro', sans-serif !important; color: #AEAEAE; font-size: 13px;  font-weight: 400;box-sizing: border-box; line-height: 1.5em; margin-top: 0; text-align: center;">
                                                © {{ date('Y') }} {{ config('app.name', 'Laravel') }}.
                                            </p>
                                        </td>
                                    </tr>
                                    {{-- <tr>
                                        <td>
                                            <a href="{{ URL('/') }}"><img src="{{ asset('/images/gray_logo.png')}}" style="width: 56px;height: 20px;"></a>
                                        </td>
                                    </tr> --}}
                                    <tr><td height="15px"></td></tr>
                                    </tbody>
                                </table>
                            </td>
                        </tr>

                    </tbody>
                </table>
            </td>
        </tr>
    </tbody>
</table>
</body>
</html>