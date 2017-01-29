if (typeof $ === 'undefined') {
    throw new Error('This application\'s JavaScript requires jQuery');
}
var App = angular.module('alert', ['ngRoute', 'ngAnimate', 'ngStorage', 'ngCookies', 'pascalprecht.translate', 'ui.bootstrap', 'ui.router', 'oc.lazyLoad', 'cfp.loadingBar', 'ngSanitize', 'ngResource', 'tmh.dynamicLocale', 'ui.utils', 'truncate', 'noCAPTCHA']);
App.config(['$httpProvider', function ($httpProvider) {
        $httpProvider.defaults.useXDomain = true;
        if (!$httpProvider.defaults.headers.get) {
            $httpProvider.defaults.headers.get = {};
        }
    }]);

App.config(['noCAPTCHAProvider', function (noCaptchaProvider) {
        noCaptchaProvider.setSiteKey('6LeiDCoTAAAAACNmSGvpzl6J3BszjpJDXZLD2odG');
        noCaptchaProvider.setTheme('dark');
    }]);

App.config(['$stateProvider', '$locationProvider', '$urlRouterProvider', '$controllerProvider', '$compileProvider', '$filterProvider', '$provide', 'RouteHelpersProvider', function ($stateProvider, $locationProvider, $urlRouterProvider, $controllerProvider, $compileProvider, $filterProvider, $provide, helper) {
        'use strict';
        App.register = {
            controller: $controllerProvider.register,
            directive: $compileProvider.directive,
            filter: $filterProvider.register,
            factory: $provide.factory,
            service: $provide.service
        };
        $locationProvider.html5Mode(false);
        $urlRouterProvider.otherwise("/");
        $stateProvider
                .state('home', {
                    url: '/',
                    templateUrl: baseUrl + 'views/home.php',
                    resolve: helper.resolveFor('oitozero.ngSweetAlert'),
                    controller: 'HomeController'
                })
                .state('newhome', {
                    url: '/newhome',
                    templateUrl: baseUrl + 'views/newhome.php',
                    controller: 'AppController'
                })
                .state('subscribe', {url: '/subscribe', templateUrl: 'views/subscribe.html', controller: 'SubscribeController'})
                .state('blog', {url: '/blog', templateUrl: 'views/blog.php'})
                .state('post', {url: '/post/:slug', templateUrl: 'views/post.php'})


    }]).config(['$httpProvider', function ($httpProvider) {
        $httpProvider.defaults.headers.common = {};
        $httpProvider.defaults.headers.post = {};
        $httpProvider.defaults.headers.put = {};
        $httpProvider.defaults.headers.patch = {};
    }]);
App.factory('apiBaseDef', [function () {
        return {url: "/v1", headers: {'Content-Type': "application/x-www-form-urlencoded"}};
    }])
App.controller('HomeController', ['$scope', '$http', 'apiBaseDef', '$q', '$timeout', '$cookies','$window',
    function ($scope, $http, apiBaseDef, $q, $timeout, $cookies, $window) {
        $scope.isUserLogged = typeof $window.sessionStorage.userInfo !== 'undefined' && $window.sessionStorage.userInfo !== null && $window.sessionStorage.userInfo !== 'null';     
        //get subscribe message cookie
        function urldecode(str) {
            return decodeURIComponent((str + '').replace(/\+/g, '%20'));
        }

        function eraseCookie(name) {
            document.cookie = name + '=; Max-Age=0'
        }
        $scope.isCookie = $cookies.cookie_message;
        $scope.subscribeMessage = urldecode($scope.isCookie);
        if (document.cookie.indexOf("cookie_message") >= 0) {
            swal({
                title: "Succes!",
                text: $scope.subscribeMessage,
                type: "success",
                showConfirmButton: true,
                confirmButtonText: "Închide"
            });
            //document.cookie = "cookie_message = null";
            eraseCookie('cookie_message');
        }

        $scope.subsOptions = {email: '', more: 0, institutions: []};
        $scope.opts = {};
        $scope.submitted = false;
        $scope.testimonials = {};
        $http.get(apiBaseDef.url + '/categories/last').then(function (result) {
            var news = result.data.data;
            $scope.news = result.data.data;
            console.log($scope.news[1]);
            $timeout(function () {
            $('.speaker-slider').slick({
                slidesToShow: 4,
                slidesToScroll: 4,
                infinite: false,
                autoplay: false,
                arrows: true,
                dots: true,
                responsive: [{breakpoint: 1200, settings: {arrows: true, slidesToShow: 3, slidesToScroll: 3}}, {
                    breakpoint: 992,
                    settings: {slidesToShow: 2, slidesToScroll: 2}
                }, {breakpoint: 520, settings: {slidesToShow: 1, slidesToScroll: 1}}]
            });
            });
        });

        $http.get(apiBaseDef.url + '/institutions/all').then(function (result) {
            $scope.subscribeData = result.data.data;
        });

        //get testimonials
        $http.get(apiBaseDef.url + '/testimonials/all').then(function (result) {
            $scope.testimonials = result.data.data;


            setTimeout(function () {
//                $('.slider-content').slick({
//                    slidesToShow: 1,
//                    slidesToScroll: 1,
//                    arrows: false,
//                    fade: false,
//                    asNavFor: '.slider-testimonials',
//                    autoplay: true,
//                    autoplaySpeed: 2000,
//                    inifinite: true,
//                    speed: 300
//
//                });
                $('.slider-testimonials').slick({
                    autoplay: true,
                    autoplaySpeed: 2000,
                    slidesToShow: 1,
                    inifinite: true,
                    speed: 300,
                    slidesToScroll: 1,
//                    asNavFor: '.slider-content',
                    dots: false,
                    centerMode: true,
                    focusOnSelect: true,
                });
            },100);



        });


        //get about us content

        $scope.about = {};

        $http.get(apiBaseDef.url + '/about/all').then(function (response) {
            $scope.about = response.data.data;
        });

        $scope.institutions = {};
        $scope.institution_type = 0;

        $scope.getInstitutions = function (type) {
            $scope.institution_type = type;

            $http.get(apiBaseDef.url + '/institutions/all?type=' + type).then(function (response) {
                $scope.institutions = response.data.data;
                $('#showInstitutions').modal('show');
            });
        };

        //get testimonials
        $http.get(apiBaseDef.url + '/testimonials/all').then(function (result) {
            $scope.testimonials = result.data.data;


            setTimeout(function () {
//                $('.slider-content').slick({
//                    slidesToShow: 1,
//                    slidesToScroll: 1,
//                    arrows: false,
//                    fade: false,
//                    asNavFor: '.slider-testimonials'
//                });
                $('.slider-testimonials').slick({
                     autoplay: true,
                    autoplaySpeed: 2000,
                    slidesToShow: 1,
                    inifinite: true,
                    speed: 300,
                    slidesToScroll: 1,
//                    asNavFor: '.slider-content',
                    dots: false,
                    centerMode: true,
                    focusOnSelect: true,
                });
            },100);



        });




        //get about us content

        $scope.about = {};

        $http.get(apiBaseDef.url + '/about/all').then(function (response) {
            $scope.about = response.data.data;
        });

        $scope.institutions = {};
        $scope.institution_type = 0;

        $scope.getInstitutions = function (type) {
            $scope.institution_type = type;

            $http.get(apiBaseDef.url + '/institutions/all?type=' + type).then(function (response) {
                $scope.institutions = response.data.data;
                $('#showInstitutions').modal('show');
            });

        }

        var closeBtn = '<a href="#">x</a>';
        $scope.subscribe = function () {
            $scope.submitted = true;
            $scope.subsOptions.institutions = [];
            angular.forEach($scope.opts, function (value, index) {
                if (value != 0) {
                    $scope.subsOptions.institutions.push(value);
                }
            })
//        if ($scope.subsOptions.institutions.length == 0) {
//            swal({
//                title: "Eroare!",
//                text: "Selectați cel puțin o categorie de noutăți!",
//                type: "error",
//                //timer: 1500,
//                showConfirmButton: true,
//                confirmButtonText: "Închide"
//            });
//        }
            if (($scope.myForm.$valid == false) && $scope.subsOptions.institutions.length > 0) {
                swal({
                    title: "Eroare!",
                    text: "Email-ul este incomplet sau nu este valid!",
                    type: "error",
                    //timer: 1500,
                    showConfirmButton: true,
                    confirmButtonText: "Închide"
                });
            }
            if ($scope.myForm.$valid) {
//            $scope.subsOptions.institutions = '[' + $scope.subsOptions.institutions + ']';
                $http({
                    method: 'POST',
                    url: apiBaseDef.url + '/user/subscribe',
                    data: $.param($scope.subsOptions),
                    headers: apiBaseDef.headers
                }).then(function (result) {
                    if (result.data.code != 1) {
                        swal({
                            title: "Eroare!",
                            text: result.data.message,
                            type: "error",
                            showConfirmButton: true,
                            confirmButtonText: "Închide"
                        });
                    } else {
                        swal({
                            title: "Succes!",
                            text: "Pe email-ul DVS. am transmis o confirmare!",
                            type: "success",
                            showConfirmButton: true,
                            confirmButtonText: "Închide"
                        });
                    }
                })
            }
        }
    }]);



App.controller('AppController', ['$scope', '$http', 'apiBaseDef', '$q', '$timeout', function ($scope, $http, apiBaseDef, $q, $timeout) {
        $http.get(apiBaseDef.url + '/institutions/all').then(function (result) {
            $scope.subscribeData = result.data.data;
        })
        var owl = $('.owl-carousel');
        $http.get(apiBaseDef.url + '/categories').then(function (result) {
            $scope.news = result.data.data;
            $timeout(function () {
                console.log()
                if ($('.item').parent('.owl-item')) {
                } else {
                }
                owl.owlCarousel({
                    loop: true,
                    margin: 10,
                    nav: true,
                    dots: false,
                    items: 4,
                    margin: 25,
                            responsive: {0: {items: 1}, 600: {items: 3}, 1000: {items: 4}}
                })
            })
        });
        var s_bl = $('.search-bl'), s_height = s_bl.height();
        $scope.s_active = function () {
            if (s_bl.is(':visible')) {
                $('.wrapper').animate({'margin-top': 0}, function () {
                    s_bl.css({'opacity': 0, 'display': 'none'});
                });
            } else {
                $('.wrapper').animate({'margin-top': s_height});
                s_bl.css({'opacity': 1, 'display': 'block'});
            }
        }
        $scope.subsOptions = {email: '', more: 0, institutions: []};
        $scope.opts = {};
        $scope.submitted = false;
        $scope.subscribe = function () {
            $scope.submitted = true;
            $scope.subsOptions.institutions = [];
            angular.forEach($scope.opts, function (value, index) {
                if (value != 0) {
                    $scope.subsOptions.institutions.push(value);
                }
            })
            if ($scope.myForm.$valid && $scope.subsOptions.institutions.length > 0) {
                $scope.subsOptions.institutions = '[' + $scope.subsOptions.institutions + ']';
                $http({
                    method: 'POST',
                    url: apiBaseDef.url + '/user/subscribe',
                    data: $.param($scope.subsOptions),
                    headers: apiBaseDef.headers
                }).then(function (result) {
                    console.log(result)
                })
            }
        }
    }]);
App.controller('SearchController', ['$scope', function ($scope) {
        $scope.search = {searchword: ''};
        $scope.goToLink = function () {
            $scope.formattedSearch = $.param($scope.search);
            window.open('http://ecopresa.md/index.php/ro/using-joomla/extensions/components/search-component/search?' + $scope.formattedSearch + '&searchphrase=all', '_blank');
        }
    }])
App.directive("a", function urlFragmentDirective($location, $anchorScroll) {
    return ({compile: compile, restrict: "E"});
    function compile(tElement, tAttributes) {
        if (tAttributes.target) {
            return;
        }
        var href = (tAttributes.href || tAttributes.ngHref || "");
        if (!href) {
            return;
        }
        if (href.charAt(0) !== "#") {
            return;
        }
        if (href.charAt(1) === "/") {
            return;
        }
        return (link);
    }

    function link(scope, element, attributes) {
        element.on("click", function handleClickEvent(event) {
            if (event.ctrlKey || event.metaKey || event.shiftKey || (event.which == 2) || (event.button == 2)) {
                return;
            }
            var href = element.attr("href");
            if (href.indexOf("#/") === 0) {
                return;
            }
            event.preventDefault();
            var fragment = href.slice(1);
            if (element[0].hash == '#c_description') {
                $('html, body').animate({scrollTop: 0}, 500);
            } else {
                if ($('#getFixed').hasClass('active')) {
                    $('html, body').animate({scrollTop: ($($(this).attr('href')).offset().top - 68)}, 500);
                } else {
                    $('html, body').animate({scrollTop: ($($(this).attr('href')).offset().top - 68)}, 500);
                }
            }
            scope.$apply();
        });
    }
});
App.provider('RouteHelpers', ['APP_REQUIRES', function (appRequires) {
        "use strict";
        this.basepath = function (uri) {
            return baseUrl + 'app/views/' + uri;
        };
        this.resolveFor = function () {
            var _args = arguments;
            return {
                deps: ['$ocLazyLoad', '$q', function ($ocLL, $q) {
                        var promise = $q.when(1);
                        for (var i = 0, len = _args.length; i < len; i++) {
                            promise = andThen(_args[i]);
                        }
                        return promise;
                        function andThen(_arg) {
                            if (typeof _arg == 'function')
                                return promise.then(_arg);
                            else
                                return promise.then(function () {
                                    var whatToLoad = getRequired(_arg);
                                    if (!whatToLoad)
                                        return $.error('Route resolve: Bad resource name [' + _arg + ']');
                                    return $ocLL.load(whatToLoad);
                                });
                        }

                        function getRequired(name) {
                            if (appRequires.modules)
                                for (var m in appRequires.modules)
                                    if (appRequires.modules[m].name && appRequires.modules[m].name === name)
                                        return appRequires.modules[m];
                            return appRequires.scripts && appRequires.scripts[name];
                        }
                    }]
            };
        };
        this.$get = function () {
            return {basepath: this.basepath}
        };
    }]);
App.constant('APP_REQUIRES', {
    modules: [{
            name: 'oitozero.ngSweetAlert',
            files: ['assets/js/sweetalert/dist/sweetalert.css', 'assets/js/sweetalert/dist/sweetalert.min.js']
        }]
});

App.controller('BlogController', ['$scope', '$http', 'apiBaseDef', '$q', '$timeout', '$cookies',
    function ($scope, $http, apiBaseDef, $q, $timeout, $cookies) {


        $scope.page = 1;
        $scope.pages = 0;
        $scope.limit = 10;
        $scope.posts = {};
        $scope.pagesContent = [];

        $scope.loadPosts = function () {
            $http.get(apiBaseDef.url + '/blog/all?page=' + $scope.page).then(function (response) {
                $scope.posts = response.data.data.posts;
                $scope.pages = parseInt(response.data.data.count / $scope.limit);
                if ($scope.pages < 1)
                {
                    $scope.pages = 1;
                }
                setTimeout(function () {
                    $('.posts-grid').masonry({
                        columnWidth: '.post-item',
                        itemSelector: '.post-item',
                        percentPosition: true
                    });
                }, 100);
            });


        }


        $scope.getNumber = function (num) {
            return new Array(num);
        }

        $scope.loadPages = function () {
            $http.get(apiBaseDef.url + '/blog/all?page=' + $scope.page).then(function (response) {
                $scope.posts = response.data.data.posts;
                $scope.pages = response.data.count * $scope.limit;

            });
        }



        $scope.loadPosts();


        $scope.nextPage = function () {
            if ($scope.page < $scope.pages)
            {
                $scope.page += 1;
                $scope.loadPages();
            }
        }

        $scope.prevPage = function () {
            if ($scope.page > 1)
            {
                $scope.page -= 1;
                $scope.loadPages();
            }
        }


        $scope.setpage = function (page) {
            $scope.page = page;
            $scope.loadPages();
        }





    }]);
App.controller('PostController', ['$scope', '$http', 'apiBaseDef', '$q', '$timeout', '$cookies', '$stateParams', '$window','$location',
    function ($scope, $http, apiBaseDef, $q, $timeout, $cookies, $stateParams, $window,$location) {

        $scope.gRecaptchaResponse = '';
        $scope.showInsert = false;
        $scope.validated = false;

        //check if user is logged
        var session = {};
        if ($window.sessionStorage['userInfo'] !== 'null'
                && $window.sessionStorage['userInfo'] != 'undefined' && $window.sessionStorage['userInfo']) {
            var user = JSON.parse($window.sessionStorage['userInfo']);
            session = user.userToken.content;
            $scope.showInsert = true;
        }


        var headers = {
            url: "/v1",
            headers: {
                'Content-Type': "application/x-www-form-urlencoded",
                'userToken': session
            }
        };

        $scope.showSaveBtn = false;

        //get post content
        $scope.post = {};
        $scope.comments = {};
        $scope.comment = {};

        $http.get(apiBaseDef.url + '/post?slug=' + $stateParams.slug).then(function (response) {
            $scope.post = response.data.data;
            if ($scope.post[0])
            {
                $scope.post = $scope.post[0];
            }

            $scope.getComments();

        });
        
        //redirect user for auth
        $scope.goAuth = function(){
            var url = '/post/'+$stateParams.slug;
            $window.sessionStorage.blogUrl = url;
            $window.location.href = '/user';
        }


        //get post comments
        $scope.getComments = function () {

            $http.get(apiBaseDef.url + '/comments' + '?post=' + $scope.post.id).then(function (response) {
                $scope.comments = response.data.data;
            });
        };
        
        
        

        $scope.$watch('gRecaptchaResponse', function () {
            if ($scope.gRecaptchaResponse)
                $scope.showSaveBtn = true;
            });


        $scope.addComment = function () {

        if(!$scope.validated)
        {
            $http({
                url: apiBaseDef.url + '/check/captcha',
                method: 'POST',
                data: $.param({secret: "6LeiDCoTAAAAACpVfM3LrhNYA2vQ78uH2nDrh1UD", response: $scope.gRecaptchaResponse}),
                headers: headers.headers
            }).then(function (response) {
                if (response.data.data[0].success)
                {
                    $scope.validated = true;
                    $scope.saveComment();

                }
            });
            
        }
        else{
             $scope.saveComment();
        }

        }


        $scope.saveComment = function () {
           
            $scope.comment.post = $scope.post.id;
            //save comment
            $http({
                url: apiBaseDef.url + '/comment',
                method: 'POST',
                data: $.param($scope.comment),
                headers: headers.headers
            }).then(function (response) {
               $scope.comment.comment = '';

                $scope.getComments();
            });
        }



    }]);