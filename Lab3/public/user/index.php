<!DOCTYPE html>
<html data-ng-app="angle">

<head>
   <meta charset="utf-8">
   <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
   <meta name="description" ng-attr-content="{{app.description}}">
   <meta name="keywords" ng-attr-content="app, responsive, angular, bootstrap, dashboard, admin">
   <title data-ng-bind="::pageTitle()">Angle - Angular Bootstrap Admin Template</title>

   <link href="https://fonts.googleapis.com/css?family=Roboto:400,700&amp;subset=latin,cyrillic" rel="stylesheet" type="text/css">

   <!-- Bootstrap styles-->
   <link rel="stylesheet" href="app/css/bootstrap.css" data-ng-if="!app.layout.isRTL">
   <!-- Application styles-->
   <link rel="stylesheet" href="app/css/app.css" data-ng-if="!app.layout.isRTL">
   <link rel="stylesheet" href="app/css/bootstrap-social.css">

</head>

<body data-ng-class="{ 'layout-fixed' : app.layout.isFixed, 'aside-collapsed' : app.layout.isCollapsed, 'layout-boxed' : app.layout.isBoxed, 'layout-fs': app.useFullLayout, 'hidden-footer': app.hiddenFooter, 'layout-h': app.layout.horizontal, 'aside-float': app.layout.isFloat, 'aside-toggled': app.asideToggled}">
      <script>
            var fbAppId = '1164902103558185';
            var host = window.location.hostname;
            if (host != "officialalert.md") {
              fbAppId = '548437285350563';
            }
            window.fbAsyncInit = function () {
                FB.init({
                    appId: fbAppId,
                    xfbml: true,
                    version: 'v2.8'
                });
            };

            (function (d, s, id) {
                var js, fjs = d.getElementsByTagName(s)[0];
                if (d.getElementById(id)) {
                    return;
                }
                js = d.createElement(s);
                js.id = id;
                js.src = "//connect.facebook.net/en_US/sdk.js";
                fjs.parentNode.insertBefore(js, fjs);
            }(document, 'script', 'facebook-jssdk'));
        </script>
        
<!--  aEwUrU9LGtRyR4KdAiwmqvaE -->
 <div data-ui-view="" data-autoscroll="false" class="wrapper"></div>
   <script src="app/js/base.js"></script>
          <script src="https://apis.google.com/js/platform.js"></script>

       <script src="app/js/google-plus-signin.js"></script>
   <script src="app/js/app.js"></script>
           <script
  src="https://www.google.com/recaptcha/api.js?onload=vcRecaptchaApiLoaded&render=explicit"
  async defer
></script>
        <script type="text/javascript" src="vendor/angular-recaptcha/angular-recaptcha.min.js"></script>

</body>

</html>