<!DOCTYPE html>
<html lang="en">
<head>
    <base href='<?= $baseUrl ?>'/>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <meta name="description" content="Official Alert te ține la curent cu noutăţile oficiale! Aplicaţia trimite notificări atunci când sunt actualizate paginile web ale instituțiilor de stat: ministere, agenții, institute etc.">
    <meta name="keywords" content="app, responsive, angular, bootstrap, dashboard, admin">
    <title data-ng-bind="::pageTitle()">Officialalert.md</title>

    <!--    <link rel="shortcut icon" href="images/favicon.ico">-->

    <!-- Bootstrap -->
    <link href="<?= $baseUrl ?>assets/css/bootstrap.min.css" rel="stylesheet">

<!--    <link href='https://fonts.googleapis.com/css?family=Open+Sans:400,300,600,700&amp;subset=latin,cyrillic'-->
<!--          rel='stylesheet' type='text/css'>-->
    <link href='https://fonts.googleapis.com/css?family=Open+Sans:400,300,600,700&subset=latin,cyrillic-ext,latin-ext,cyrillic,greek-ext,greek' rel='stylesheet' type='text/css'>
    <link href='https://fonts.googleapis.com/css?family=Roboto:400,700&amp;subset=latin,cyrillic' rel='stylesheet'
          type='text/css'>
    <link href='https://fonts.googleapis.com/css?family=Montserrat:400,700' rel='stylesheet' type='text/css'>

    <link href="<?= $baseUrl ?>assets/css/plugins/animate.css" rel="stylesheet">
    <link href="<?= $baseUrl ?>assets/css/plugins/slick.css" rel="stylesheet">
    <link href="<?= $baseUrl ?>assets/css/plugins/magnific-popup.css" rel="stylesheet">
    <link href="<?= $baseUrl ?>assets/css/plugins/font-awesome.css" rel="stylesheet">
    <link href="<?= $baseUrl ?>assets/css/plugins/streamline-icons.css" rel="stylesheet">

    <link href="<?= $baseUrl ?>assets/css/themes/green.css" rel="stylesheet">
    <link href="<?= $baseUrl ?>assets/css/event.css" rel="stylesheet">

    <script src="<?= $baseUrl ?>assets/js/ie/respond.min.js"></script>

    <script src="<?= $baseUrl ?>assets/js/modernizr.min.js"></script>
    <script src="<?= $baseUrl ?>assets/js/plugins/pace.js"></script>
</head>

<body ng-app="alert" class="animate-page" data-spy="scroll" data-target="#navbar" data-offset="100">
  
<!--<div class="search-bl" ng-controller="SearchController">-->
<!--    <div class="container" style="padding:20px">-->
<!--        <div class="col-md-10 s-in">-->
<!--            <input type="text" placeholder="Search..." ng-model="search.searchword">-->
<!--            <a href="javascript:;" class="go-search" ng-click="goToLink()">-->
<!--                <i class="fa fa-search"></i>-->
<!--            </a>-->
<!--        </div>-->
<!--    </div>-->
<!--</div>-->
<div data-ui-view="" data-autoscroll="true" class="wrapper"></div>

<script>var baseUrl = '<?=$baseUrl?>'</script>
<script src="<?= $baseUrl ?>app/base.js"></script>
<script src="<?= $baseUrl ?>app/app.js"></script>
<script src="<?= $baseUrl ?>assets/js/jquery.min.js"></script>
<script type="text/javascript" src="/templates/frontend/assets/js/angular-no-captcha.js"></script>
<!-- Bootstrap JS -->
<script src="<?= $baseUrl ?>assets/js/bootstrap.min.js"></script>

<!-- 3rd party Plugins -->
<script src="<?= $baseUrl ?>assets/js/plugins/countdown.js"></script>
<script src="<?= $baseUrl ?>assets/js/plugins/wow.js"></script>
<script src="<?= $baseUrl ?>assets/js/plugins/slick.js"></script>
<script src="<?= $baseUrl ?>assets/js/plugins/magnific-popup.js"></script>
<script src="<?= $baseUrl ?>assets/js/plugins/validate.js"></script>
<script src="<?= $baseUrl ?>assets/js/plugins/appear.js"></script>
<script src="<?= $baseUrl ?>assets/js/plugins/count-to.js"></script>
<script src="<?= $baseUrl ?>assets/js/plugins/nicescroll.js"></script>
<script src="<?= $baseUrl ?>assets/js/plugins/parallax.js"></script>
<script src="<?= $baseUrl ?>assets/js/truncate.js"></script>

<!-- Google Map -->

<script src="http://maps.google.com/maps/api/js?sensor=false"></script>
<script src="<?= $baseUrl ?>assets/js/plugins/infobox.js"></script>
<script src="<?= $baseUrl ?>assets/js/plugins/google-map.js"></script>
<script src="<?= $baseUrl ?>assets/js/plugins/directions.js"></script>

<!-- JS Includes -->
<script src="<?= $baseUrl ?>assets/js/includes/subscribe.js"></script>
<script src="<?= $baseUrl ?>assets/js/includes/contact_form.js"></script>
<script src="<?= $baseUrl ?>assets/js/masonry-new.js"></script>


<!-- Main Script -->
<script src="<?= $baseUrl ?>assets/js/main.js"></script>



</body>

</html>
