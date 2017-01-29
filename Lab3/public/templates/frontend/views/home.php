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
                <li class="active"><a href="#home">Acasă</a></li>
                <li><a href="#events">Alerte</a></li>
                <li><a href="#subscribe">Abonează-te</a></li>
                <li><a href="#about">Despre noi</a></li>
                <li><a href="/#/blog">Blog</a></li>
                <li><a href="/user">{{ isUserLogged ? 'Profil': 'Logare'}}</a></li>
                <li><a class="fb" target="_blank" href="https://www.facebook.com/officialalert.md/"><i
                            class="fa fa-facebook-square fa-2"></i></a></li>
            </ul>
        </div>
        <!--/.nav-collapse -->
    </div>
</nav>
<!-- // End Fixed navbar -->

<header class="header parallax-bg" id="home">
    <div class="container">

        <div class="header-cell">
            <div class="header_top-bg">
                <div class="logo">
                    <a href="#"><img src="assets/img/logo-big.png" alt="event-logo"></a>
                </div>
            </div>

            <h1 class="headline-support wow fadeInDown">Cu un singur click ești informat la timp!</h1>

<!--            <p class="headline wow fadeInDown" data-wow-delay="0.1s">Official Alert te ține la curent cu noutăţile
                oficiale! Aplicaţia trimite notificări atunci când sunt actualizate paginile web ale instituțiilor de
                stat:
                ministere, agenții, institute etc. OfficialAlert permite selectarea instituţiilor, prin bifare, și
                trimite
                notificări conform alegerii făcute.
            </p>-->


            <div class="header_bottom-bg">
                <a href="https://play.google.com/store/apps/details?id=official.alert" target="_blank"
                   class=" wow zoomIn" data-wow-delay="0.3s"><img src="assets/img/google-play.png" alt=""></a>
                <a href="https://itunes.apple.com/us/app/official-alert/id1095287387?ls=1&mt=8" target="_blank"
                   class=" wow zoomIn" data-wow-delay="0.3s"><img src="assets/img/app-store.png" alt=""></a>

            </div>
        </div>

    </div>
    <!-- end .container -->
</header>

<section class="speakers" id="events">
    <div class="container">

        <style>

            .cat-content .text{
                min-height: 95px;
            }

        </style>

        <div class="row">
            <div class="speaker-info wow fadeIn col-md-3 " data-wow-delay="0.{{$index}}s" 
                 ng-repeat="article in news[0]"
                 >
                   <div class="cat-content">
                       <a href="{{ article[0].url }}" target="_blank">
                           <span class="date"> {{ article[0].institution_name }} </span>
                           <p class="text" ng-bind-html='article[0].description | words: 20'/>
                        </a>
                    </div>
            </div>
        </div>
        
        
        <div class="row">
            <div class="speaker-info wow fadeIn col-md-3 "  data-wow-delay="0.{{$index}}s" ng-repeat="article in news[1] "
                  >
                <div class="cat-content" >
                   <a href="{{ article[0].url }}" target="_blank">
                       <span class="date"> {{ article[0].institution_name }} </span>

                       <p class="text" ng-bind-html='article[0].description | words: 20'/>
                    </a>
                </div>
            </div>
        </div>

        <div class="col-xs-12 col-sm-10 col-md-8 col-sm-offset-1 col-md-offset-2">
            <div class="block-title">

                <h5>Abonează-te la Official Alert și vei ști primul despre actualizările instituțiilor publice naționale și locale, vei economisi timp și îți vei mări productivitatea.</h5>

                <div class="red-bord short wow fadeInDown"></div>
<!--                <p>Noi îți oferim posibilitatea să alegi cum vei primi notificările: pe e-mail sau pe
                    telefonul
                    mobil.
                </p>-->
            </div>


        </div>
        <div class="row second-description">

            <div class="col-md-12">

                <div class="col-md-6">
                    <div class="col-md-12">
                        <h6>Categoria "Național"</h6>
                        <small>
                            <a ng-click="getInstitutions(1)"><i class="fa fa-plus-circle" aria-hidden="true"></i> Lista de instituții</a>
                        </small>
                    </div>

                    <div class="col-md-12">
                        <p>
                            Abonându-te la categoria <b>Național</b> vei primi notificări atunci când se actualizează paginile web ale instituțiilor de stat de nivel național: agenții, ministere, institute ș.a. Astfel, vei fi informat când au fost publicate rapoartele de activitate, când se fac consultări publice, evenimentele instituției, comunicate de presă ș.a.
                        </p>
                    </div>

                    <div class="col-md-4 col-md-offset-4">
                        <img class="img-responsive" src="/templates/frontend/assets/img/moldova-img.png">
                    </div>
                </div>

                <div class="col-md-6">

                    <div class="col-md-4 col-md-offset-4 second-column-img">
                        <img class="img-responsive" src="/templates/frontend/assets/img/local.png">
                    </div>

                    <div class="col-md-12">
                        <h6>Categoria "Local"</h6>

                        <small>
                            <a ng-click="getInstitutions(2)"><i class="fa fa-plus-circle" aria-hidden="true"></i> Lista de instituții</a>
                        </small>

                        <p>
                            Categoria <b>Local</b>  vine cu informații de la autoritățile publice locale. Astfel, prin intermediul Official Alert, vei afla primul noutăți de la primăriile și consiliile locale din R. Moldova, vei primi notificări atunci când vor fi publicate deciziile de nivel local sau regional, consultările publice, tenderele şi alte informații utile.
                        </p>
                    </div>



                </div>
            </div>

        </div>


    </div>
</section>

<section class="highlight subscribe" id="subscribe">
    <div class="container">

        <div class="row">


            <div class="block-title">
                <h5>Noi îți oferim posibilitatea să alegi cum vei urmări informațiile oficiale: pe telefonul mobil  (aplicație) sau din contul utilizatorului, cu livrare opțională pe e-mail.</h5>   
                <div class="red-bord short wow fadeInDown"></div>
            </div>


            <!--            <div class="col-md-4 description-icon-block" >
                            <div class="row text-center  icon-description">
                                <i class="fa fa-calendar-o" aria-hidden="true"></i>    
                            </div>
                            <div class="col-xs-12 text-center">
                                <h5>Pentru a te abona pe e-mail:</h5>
                            </div>
                            <div class="col-xs-12">
                                <ol>
                                    <li>Selectezi instituțiile</li>
                                    <li>Introduci adresa de e-mail</li>
                                    <li>Confirmi e-mailul</li>
                                    <li>Primeşti notificări</li>
                                </ol>
            
                            </div>
                        </div>-->

            <div class="col-md-6 description-icon-block" >

                <div class="row text-center  icon-description">
                    <i class="fa fa-clock-o" aria-hidden="true"></i> 
                </div>

                <div class="col-xs-12 text-center">
                    <h5>Pentru notificări pe telefonul mobil:</h5>
                </div>

                <div class="row">
                    <div class="col-xs-12">
                        <ol>
                            <li><a href="https://play.google.com/store/apps/details?id=official.alert">Instalezi aplicația</a></li>
                            <li>Selectezi instituțiile</li>
                            <li>Primești notificări</li>
                        </ol>
                    </div>
                </div>

            </div>

            <div class="col-md-6 description-icon-block" >
                <div class="row text-center icon-description">
                    <i class="fa fa-users" aria-hidden="true"></i>    
                </div>
                <div class="col-xs-12 text-center">
                    <h5>Pentru contul utilizatorului:</h5>
                </div>
                <div class="row">
                    <div class="col-xs-12">
                        <ol>
                            <li><a href="/user/#/page/register">Îți faci un cont</a></li>
                            <li>Selectezi instituțiile</li>
                            <li>Primești notificări</li>
                        </ol>
                    </div>
                </div>
            </div>


        </div>

    </div>
</section>

<section id="events" class="speakers ng-scope">
    <div class="container">

        <div class="section-title wow fadeInUp" id="alert">


            <div class="block-title">
                <h4>Aprecieri</h4>
            </div>
        </div>
<!--        <p class="lead text-center">Din lista de mai jos bifați sursele preferate și introduceți emailul:</p>-->
        <!--slider-->
        <div class="row testimonial-container">
            <div class="slider-testimonials">
                <div class="col-md-6 col-lg-6 col-sm-12 col-xs-12 testim-icon" ng-repeat="user_img in testimonials" >
                    <div class="circle-avatar" style="background-image:url(/uploads/testimonials/{{user_img.image}});max-height:80px;"></div>
                     
                    <div class="col-md-10 col-md-offset-1 col-lg-10 col-lg-offset-1 col-sm-10 col-sm-offset-1 col-xs-10 col-xs-offset-1">
                    <div class="text-center">
                        <h6>{{user_img.name}}</h6>
                        {{user_img.content}}
                    </div>
                    </div>
                    
                </div>
              
                
            </div>

<!--            <div class="col-md-10 col-md-offset-1">
                <div class="slider-content">
                    <div class="text-center" ng-repeat="testimonial in testimonials">
                        <h6>{{testimonial.name}}</h6>
                        <p>{{testimonial.content}}</p>
                    </div>
                </div>
            </div>-->

        </div>
    </div>
</section>







<section class="highlight subscribe" id="subscribe">
    <div class="container">

        <form action="" name="myForm" novalidate>
            <!--            <div class="row">
                            <div class="checkbox-itm col-sm-6 col-md-4 wow zoomIn" data-wow-delay="{{$index/40}}s"
                                 data-wow-duration="0.3s"
                                 ng-repeat="item in subscribeData">
                                <input type="checkbox" name="checkboxG{{$index + 1}}" id="checkboxG{{$index + 1}}"
                                       class="css-checkbox"
                                       ng-model="opts[item.id]"
                                       ng-true-value="'{{ item.id }}'" ng-false-value="'0'"/>
                                <label for="checkboxG{{$index + 1}}" class="css-label">{{ item.name }}</label>
                            </div>
                        </div>-->


            <div class="row m-t">
                <div class="form-group col-md-3 col-sm-12">
                    <h6 class="susbcribe-head wow fadeInLeft" data-wow-delay="{{(subscribeData.length + 1) / 40}}s">
                        <span>Abonează-te pentru notificări</span>
                        <small>de la Official Alert:</small>
                    </h6>
                </div>
                <div class="form-group col-sm-8 col-md-6 wow fadeInRight"
                     data-wow-delay="{{(subscribeData.length + 1) / 40}}s">
                    <label class="sr-only">Email address</label>
                    <input type="email" class="form-control input-lg" placeholder="Email"
                           name="email"
                           id="email" ng-model='subsOptions.email' required>

                    <div class="checkbox-itm ">
                        <input type="checkbox" name="sbsc" id="sbsc" class="css-checkbox" ng-model="subsOptions.more"
                               ng-true-value="'1'" ng-false-value="'0'"/>

                        <label for="sbsc" class="css-label">Doresc o listă mai mare de surse</label>

                    </div>
                </div>
                <div class="form-group col-sm-4 col-md-3">
                    <a href="javascript:;" class="btn btn-lg btn-success btn-block" id="js-subscribe-btn"
                       ng-click="subscribe()"
                       ng-disabled="">
                        Abonează-te acum
                    </a>
                </div>
            </div>
        </form>

    </div>
</section>

<section class="schedule" id="about">

    <div class="container">
        <div class="section-title wow fadeInUp">
            <h3>Despre noi</h3>
        </div>


        <div class="row">
            <div class="col-md-6">
                <div class="thumbnail wow fadeInUp">
                    <img src="assets/img/odd-img.jpg" alt="">

                    <div class="caption" ng-bind-html="about[0].value">
                    </div>
                </div>
            </div>
            <div class="col-md-6 ">
                <div class="thumbnail wow fadeInUp" data-wow-delay="0.3s">
                    <img src="assets/img/even-img.jpg" alt="">

                    <div class="caption" ng-bind-html="about[1].value">

                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- end .container -->

</section>


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


<div class="modal fade" tabindex="-1" role="dialog" id="showInstitutions">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h5 class="modal-title">{{institution_type == 2 ?'Instituțiile Locale':'Instituțiile Naționale'}}</h5>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <ul>
                            <li class="institution_item" ng-repeat="institution in institutions" ng-bind="institution.name"></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>




<!--<a href="#top" class="back_to_top"><img src="assets/images/back_to_top.png" alt="back to top"></a>-->
