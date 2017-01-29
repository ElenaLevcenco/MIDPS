<div class="preloader"></div>

<!-- Fixed navbar -->
<nav class="navbar navbar-default navbar-fixed-top " ng-controller="HomeController">
    <div class="container">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar"
                    aria-expanded="false" aria-controls="navbar">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="#"><img src="assets/img/logo-big.png" alt="Gather"> </a>
        </div>
        <div id="navbar" class="navbar-collapse collapse">

            <ul class="nav navbar-nav navbar-right">
                <li><a href="/#home">Acasă</a></li>
                <li><a href="/#events">Alerte</a></li>
                <li><a href="/#subscribe">Abonează-te</a></li>
                <li><a href="/#about">Despre noi</a></li>
                <li class="active"><a href="/#/blog">Blog</a></li>
                <li><a href="/user">{{ isUserLogged ? 'Profil': 'Logare'}}</a></li>
                <li><a class="fb" target="_blank" href="https://www.facebook.com/officialalert.md/"><i
                            class="fa fa-facebook-square fa-2"></i></a></li>
            </ul>
        </div>
        <!--/.nav-collapse -->
    </div>
</nav>
<!-- // End Fixed navbar -->


<header class="header  parallax-bg" id="blog">
    <div class="container">

        <div class="header-cell">

            <h1 class="headline-support wow fadeInDown">BLOG</h1>

        </div>

    </div>
    <!-- end .container -->
</header>


<!--
 Blog posts
 ====================================== -->
<div class="section-title wow fadeInUp">
    <h3>Ultimele Articole:</h3>
</div>
<div ng-controller="BlogController">

<div class="posts-grid" >

    <div class="post-item" ng-repeat="post in posts">
        <a href="/#/post/{{post.slug}}" class="post-link" ng-click="select(post)">

            <div class="post-image">
                <img ng-src="/uploads/blog/{{post.img}}" alt="">
            </div>

            <div class="post-date">
                <div class="day">
                    {{post.date.date}}
                </div>
                <div class="month">
                     {{post.date.month}}
                </div>
            </div>

            <div class="post-title">
                {{post.title}}
            </div>

            <div class="post-desc" ng-bind-html="post.content.substring(0,200)">
              
            </div>
        </a>

        <ul class="post-socials">
            <li><a href="javascript:;"><i class="fa fa-facebook" aria-hidden="true"></i></a></li>
            <li><a href="javascript:;"><i class="fa fa-twitter" aria-hidden="true"></i></a></li>
            <li><a href="javascript:;"><i class="fa fa-pinterest-p" aria-hidden="true"></i></a></li>
            <li><a href="javascript:;"><i class="fa fa-google-plus" aria-hidden="true"></i></a></li>
        </ul>

    </div>


    <div class="clearfix"></div>
</div>

<div class="official-pagination text-center" >
    <ul>
        <li><a href="javascript:;" class="prev" ng-click="prevPage()"><i class="fa fa-angle-left" aria-hidden="true"></i></a></li>
        
        
<!--        <li class="active"><a href="javascript:;">1</a></li>-->
        <li ng-repeat="p in getNumber(pages) track by $index"><a href="javascript:;">{{$index+1}}</a></li>
        
        <li><a href="javascript:;" class="next" ng-click="nextPage()"><i class="fa fa-angle-right" aria-hidden="true"></i></a></li>
    </ul>
</div>
</div>

<!--
 Contact us
 ====================================== -->

<div class="highlight">
    <div class="container">

        <div class="row">
            <div class="col-sm-4">
                <div class="contact-box">
                    <img src="assets/images/location-icon.png" alt="location icon" class="wow zoomIn">
                    <h6>Adresa</h6>

                    <p>Str. Serghei Lazo 13
                        <br>Chișinău, Republica Moldova</p>
                </div>
            </div>
            <div class="col-sm-4">
                <div class="contact-box">
                    <img src="assets/images/email-icon.png" alt="email icon" class="wow zoomIn" data-wow-delay="0.3s">
                    <h6>Contact</h6>

                    <p><a href="mailto:ajmtem@gmail.com" target="_blank">ajmtem@gmail.com</a>
                        <br> <a href="mailto:ajmtem@yahoo.com" target="_blank">ajmtem@yahoo.com</a>
                        <br> +373 22 237 149
                    </p>
                </div>
            </div>
            <div class="col-sm-4">
                <div class="contact-box">
                    <img src="assets/images/user-icon.png" alt="email icon" class="wow zoomIn" data-wow-delay="0.6s">
                    <h6>
                        Autorii proiectului:
                    </h6>

                    <p>
                        Lilia Curchi, Cristina Straton<br>

                        AJMTEM <br>
                        Elaborat de EBS
                    </p>
                </div>
            </div>
        </div>
        <!--  // end .row -->
    </div>
</div>
<!-- //  end .highlight -->


<footer>

    <div class="social-icons">
        <a href="https://play.google.com/store/apps/details?id=official.alert" target="_blank"><img
                src="assets/img/f-store-g.png" alt="" class="wow zoomIn"></a>
        <a href="https://itunes.apple.com/us/app/official-alert/id1095287387?ls=1&mt=8" target="_blank"><img
                src="assets/img/f-store-a.png" alt="" class="wow zoomIn" data-wow-delay="0.2s"></a>
    </div>
    <p>
        <small class="text-muted">Copyright © Official Alert 2015. Toate drepturile rezervate.</small>
    </p>

</footer>


<script>



</script>

