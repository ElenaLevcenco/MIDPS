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
                <li class="active" ><a href="/#/blog">Blog</a></li>
                <li><a href="/user">{{ isUserLogged ? 'Profil': 'Logare'}}</a></li>
                <li><a class="fb" target="_blank" href="https://www.facebook.com/officialalert.md/"><i
                            class="fa fa-facebook-square fa-2"></i></a></li>
            </ul>
        </div>
        <!--/.nav-collapse -->
    </div>
</nav>
<!-- // End Fixed navbar -->

<div ng-controller="PostController">
    <header class="header  parallax-bg" id="post" data-background="assets/img/post_bg.jpg">
        <div class="container">

            <div class="header-cell">

            </div>

        </div>
        <!-- end .container -->
    </header>


    <div class="post-content" >
        <div class="container">
            <div class="row">
                <div class="col-md-12">


                    <h2 class="article-title">
                        {{post.title}}
                    </h2>
                    <div class="article-date">
                        {{post.date.date}} {{post.date.month}},{{post.date.year}}
                    </div>

                    <p ng-bind-html="post.content"></p>

                </div>
            </div>
        </div>
        <div class="post-credentials">
            <div class="container">
                <div class="row">
                    <div class="col-md-12">
                        <ul class="post-tags">
                            <li>Tags</li>
                            <li ng-repeat="tag in post.tags"><a href="javascript:;"> {{tag}} </a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="post-comments">
            <div class="container">
                
                <div class="row comment-list">
                    
                    
                    <div class="col-md-12">
                        
                        <div class="media" ng-repeat="comment in comments">
                            <div class="media-left media-middle">
                                <a href="#">
                                    <img class="media-object" style="max-height: 90px" src="assets/img/blog-avatar-1.jpg" alt="">
                                </a>
                            </div>
                            <div class="media-body">
                                <h6 class="media-heading">{{comment.user_data.first_name}} {{comment.user_data.surname}}</h6>
                                 <p>{{comment.comment}}</p>
                            </div>
                        </div>
                 </div>
                    
                </div>
                
                  
                <div class="row" ng-show="!showInsert">
                   <div class="col-md-6 col-md-offset-3 text-center"> 
                       <h5>  Doar utilizatorii autentificați pot lăsa comentariu!</h5> 
                       <div class="col-md-8 col-md-offset-2"><button ng-click="goAuth()" class="btn btn-md btn-success btn-block">Intră în cont!</button></div>
             
                   </div>
                   
                   </div>
                
                
                <div class="row" ng-show="showInsert">
                <div class="col-md-12">
                    <strong class="say">Spune ce crezi:</strong>
                    <textarea name="comment" class="form-control" placeholder="Comentariu..." ng-model="comment.comment"></textarea>
                    <button ng-show="showSaveBtn" type="submit" class="btn btn-md btn-success" ng-click="addComment()">Trimite</button>
                    
                    
                    
                    <no-captcha
                        g-recaptcha-response="gRecaptchaResponse"
                        theme='light'
                        control="noCaptchaControl"
                        >
                    </no-captcha>
                    
                   
                    </div>
                    
                </div>
                
                
                
                
                
                
            </div>
        </div>

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

    $(document).ready(function () {
        $('#post').css({
            "background-image": "url(" + $('#post').data('background') + ")"
        });

    });

</script>




