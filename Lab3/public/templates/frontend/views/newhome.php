<main class="home" ng-controller="AppController">


    <section class="st-section">
        <div class="container">
            <header>
                <img src="assets/img/logo-big.png" alt="Official Alert" class="logo">
                <div class="col-md-6 col-sm-6 col-xs-12 pull-right navigation">
                    <a href="#">Subscribe</a>
                    <a href="#">Despre noi</a>
                    <a href="/#" ng-click="s_active()" class="search-btn"><i class="fa fa-search"></i></a>
                    <a href="#" class="fb-btn"><i class="fa fa-facebook-official"></i></a>
                </div>
            </header>

            <div class="section-heading text-center">
                <h2 class="main-head">Cu un singur click ești informat la timp!</h2>

                <h2 class="main-descr">
                    Official Alert te ține la curent cu noutăţile oficiale! Aplicaţia trimite notificări atunci când sunt actualizate paginile web ale instituțiilor de stat: ministere, agenții, institute etc. OfficialAlert permite selectarea instituţiilor, prin bifare, și trimite notificări conform alegerii făcute.
                </h2>
            </div>

            <div class="store text-center">
                <a href="#"><img src="assets/img/google-play.png" alt=""></a>
                <a href="#"><img src="assets/img/app-store.png" alt=""></a>
            </div>

        </div>
    </section>
    <!-- end banner -->

    <section class="categories">
        <div class="container">
            <div class="owl-carousel">
                <div class="item ss" ng-repeat="article in news" ng-if="article.articles.length != 0">
                    <h4 class="cat-head">{{ article.name }}</h4>
                    <div class="cat-content" ng-repeat="item in article.articles" >
                        <a href="{{ item.url }}">
                            <span class="date"> {{ item.published }} </span>
                            <p class="text" ng-bind-html='item.description | words: 20' />
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- end categories -->

    <section class="subscribe">
        <div class="container">
            <div class="heading clearfix">
                <h3 class="col-md-6 clearfix">
                    Cu un singur click ești <br> informat la timp!
                </h3>
                <h5 class="col-md-6 clearfix">
                    Din lista de mai jos bifați sursele preferate și indroduceți emailul:
                </h5>

                <div class="red-bord"></div>
            </div>
            <form action="" name="myForm" novalidate>
                <div class="institution-list clearfix">

                    <div class="checkbox-itm col-md-4" ng-repeat="item in subscribeData">
                        <input type="checkbox" name="checkboxG{{$index + 1}}" id="checkboxG{{$index + 1}}" class="css-checkbox"
                               ng-model="opts[item.id]"
                               ng-true-value="'{{ item.id }}'" ng-false-value="'0'" />
                        <label for="checkboxG{{$index + 1}}" class="css-label">{{ item.name }}</label>
                    </div>
                    <div class="text-danger clearfix" ng-show="submitted && subsOptions.institutions.length <= 0 ">Selectati cel putin o categorie!</div>

                </div>

                <div class="subscribe-email text-center">
                    <h3>Abonează-te pentru notificări de la Official Alert:</h3>
                    <div class="form-group">
                        <input type="email" placeholder="Email" name="email" ng-model='subsOptions.email' required/ >
                        <a href="javascript:;" class="btn" ng-click="subscribe()"
                           ng-disabled="" >Subscribe</a>
                        <div class="" ng-show="submitted">
                            <div class="text-danger" ng-show="myForm.email.$error.required">Acest camp este necesar!</div>
                            <div class="text-danger" ng-show="myForm.email.$error.email">Va rugam sa introduceti un email valid!</div>
                        </div>
                    </div>
                    <input type="checkbox" name="sbsc" id="sbsc" class="css-checkbox" ng-model="subsOptions.more" ng-true-value="'1'" ng-false-value="'0'" />
                    <label for="sbsc" class="css-label">Doresc o listă mai mare de surse</label>
                </div>
            </form>

        </div>
    </section>
    <!-- end subscribe -->

    <h2 class="interlude-head text-center text-uppercase">Despre noi</h2>

    <section class="about-us">
        <div class="container">
            <div class="about-in clearfix">
                <div class="col-md-6 no-padding text-center">
                    <img src="assets/img/odd-img.jpg" alt="" class="">
                </div>
                <p class="col-md-6">
                    <b>Official Alert este aplicația care permite utilizatorilor să primească notificări atunci când sunt actualizate paginile web ale instituțiilor oficiale: ministere, agenții etc. </b> <br>

                    Scopul aplicației este de a facilita munca jurnaliștilor și, totodată, de a spori accesul cetățenilor la informația oficială.

                    Un alt obiectiv este creșterea gradului de informare a publicului larg privind activitățile, rapoartele, proiectele legislative şi, implicit, sporirea interesului asupra transparenței decizionale.
                </p>
            </div>
            <div class="about-in clearfix">
                <div class="col-md-6 image no-padding text-center">
                    <img src="assets/img/even-img.jpg" alt="" class="">
                </div>
                <p class="col-md-6" style="padding-top: 20px;">
                    Publicul țintă al aplicației sunt jurnaliștii. Totodată, aplicația este disponibilă publicului larg, întrucât orice persoană  care utilizează produsul este informat din prima sursă privitor la activitatea instituțiilor guvernamentale.

                    Proiectul Official Alert este proprietatea  Asociației Jurnaliștilor de Mediu și Turism Ecologic din Republica Moldova.

                    Realizarea aplicației și a paginii web a fost posibilă datorită unui grant  câștigat în cadrul  primului hackathon de jurnalism civic din Moldova „Puterea a cincea”, organizat de Centrul pentru Jurnalism Independent din Republica Moldova.
                </p>
            </div>
        </div>
    </section>
    <!-- end about-us -->

    <footer>
        <div class="container">
            <div class="col-md-4 clearfix ">
                <div class="col-md-12 f-logo text-center">
                    <img src="assets/img/logo-big.png" alt="Official Alert">
                </div>
                <div class="col-md-11 text-center">
                    <ul>
                        <li>Autorii proiectului:  </li>
                        <li>Lilia Curchi și Cristina Straton</li>
                        <li>Elaborat de EBS</li>
                    </ul>
                </div>
            </div>

            <div class="col-md-8 clearfix text-center">
                <div class="col-md-4 col-sm-5 col-xs-12 text-center responsive-bl">
                    <ul>
                        <li>
                            <b>Telefon:</b>
                        </li>
                        <li>+373 22 237149</li>
                        <li>079406812</li>
                    </ul>
                </div>
                <div class="col-md-4 col-sm-5 col-xs-12 text-center responsive-bl">
                    <ul>
                        <li>
                            <b>Adresa:</b>
                        </li>
                        <li>Str. Serghei Lazo 13 </li>
                        <li>Chișinău, Republica Moldova</li>
                    </ul>
                </div>
                <div class="col-md-4 col-sm-6 col-xs-12 text-center responsive-bl">
                    <ul>
                        <li>
                            <b>Email:</b>
                        </li>
                        <li>info@officialalert.md</li>
                        <li>ajmtem@yahoo.com</li>
                    </ul>
                </div>
            </div>

            <div class="col-md-8 f-stores text-center">
                <img src="assets/img/f-store-g.png" alt="">
                <img src="assets/img/f-store-a.png" alt="">
            </div>

            <div class="copyright col-md-12 text-center">
                <p class="">
                    Copyright ©  Official Alert 2015. Toate drepturile rezervate.
                </p>
            </div>
        </div>

    </footer>
    <!-- end footer -->

</main>
