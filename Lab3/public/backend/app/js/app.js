/*!
 * 
 * Angle - Bootstrap Admin App + AngularJS
 * 
 * Author: @themicon_co
 * Website: http://themicon.co
 * License: http://support.wrapbootstrap.com/knowledge_base/topics/usage-licenses
 * 
 */

 if (typeof $ === 'undefined') {
    throw new Error('This application\'s JavaScript requires jQuery');
}

// APP START
// ----------------------------------- 

var App = angular.module('angle', [
    'ngRoute',
    'ngAnimate',
    'ngStorage',
    'ngCookies',
    'pascalprecht.translate',
    'ui.bootstrap',
    'ui.router',
    'oc.lazyLoad',
    'cfp.loadingBar',
    'ngSanitize',
    'ngResource',
    'tmh.dynamicLocale',
    'ui.utils',
    'vcRecaptcha'
    ]);
App.config(['$httpProvider', function ($httpProvider) {
        //initialize get if not there
        $httpProvider.defaults.useXDomain = true;
        if (!$httpProvider.defaults.headers.get) {
            $httpProvider.defaults.headers.get = {};
        }

        //$httpProvider.defaults.headers.get['If-Modified-Since'] = 'Mon, 26 Jul 1997 05:00:00 GMT';
        //$httpProvider.defaults.headers.get['Cache-Control'] = 'no-cache';
        //$httpProvider.defaults.headers.get['Pragma'] = 'no-cache';
    }]);

App.run(["$rootScope", "$state", "$stateParams", '$window', '$templateCache', '$location',
    function ($rootScope, $state, $stateParams, $window, $templateCache, $location) {
        // Set reference to access them from any scope
        $rootScope.$state = $state;
        $rootScope.$stateParams = $stateParams;
        $rootScope.$storage = $window.localStorage;
        // Uncomment this to disable template cache
        // $rootScope.$on('$stateChangeStart', function(event, toState, toParams, fromState, fromParams, userInfo) {
        //  if (typeof(userInfo) !== 'undefined'){
        //  $templateCache.remove(toState.templateUrl);
        //  }
        //  });
        $rootScope.$on("$stateChangeStart", function (userInfo, toUrl, fromUrl) {
            // console.log(toUrl)
        });
        $rootScope.$on("$stateChangeError", function (event, toState, toParams, fromState, fromParams, eventObj) {
            // console.log(eventObj)
            if (eventObj.authenticated === false) {
                return $state.go('page.login');
            }
        });
        $rootScope.$on("$stateNotFound", function (event, unfoundState, fromState, fromParams) {
            return $state.go('page.404');
        });
        // Scope Globals
        // -----------------------------------
        $rootScope.app = {
            name: 'Official Alert',
            description: 'Official Alert',
            year: ((new Date()).getFullYear()),
            layout: {
                isFixed: true,
                isCollapsed: false,
                isBoxed: false,
                isRTL: false,
                horizontal: false,
                isFloat: false,
                asideHover: false,
                theme: null
            },
            useFullLayout: false,
            hiddenFooter: false,
            viewAnimation: 'ng-fadeInUp'
        };
        // $rootScope.user = {
        //     name: 'John',
        //     job: 'ng-developer',
        //     picture: 'app/img/user/02.jpg'
        // };
    }]);

/**=========================================================
 * Module: config.js
 * App routes and resources configuration
 =========================================================*/

 App.config(['$stateProvider', '$locationProvider', '$urlRouterProvider', 'RouteHelpersProvider',
    function ($stateProvider, $locationProvider, $urlRouterProvider, helper) {
        'use strict';

        // Set the following to true to enable the HTML5 Mode
        // You may have to set <base> tag in index and a routing configuration in your server
        $locationProvider.html5Mode(false);

        // defaults to dashboard
        $urlRouterProvider.otherwise('/page/login');

        //
        // Application Routes
        // -----------------------------------
        $stateProvider
        .state('app', {
            url: '/app',
            abstract: true,
            templateUrl: helper.basepath('app.html'),
            controller: 'AppController',
            resolve: angular.extend(helper.resolveFor('oitozero.ngSweetAlert', 'fastclick', 'modernizr', 'icons', 'screenfull', 'animo', 'slimscroll', 'classyloader', 'toaster', 'whirl'),
            {
                auth: function ($q, authenticationSvc) {
                    var userInfo = authenticationSvc.getUserInfo();
                    if (userInfo && userInfo != null && userInfo != 0) {
                        return $q.when(userInfo);
                    } else {
                        return $q.reject({authenticated: false});
                    }
                }
            })
        })
        .state('page.login', {
            url: '/login',
            title: "Login",
            templateUrl: 'app/pages/login.html',
            controller: 'LoginFormController'
        })
        .state('app.dashboard', {
            url: '/dashboard',
            title: 'Dashboard',
            templateUrl: helper.basepath('dashboard.html'),
            resolve: angular.extend(helper.resolveFor('ngDialog'), {
                tpl: function () {
                    return {path: helper.basepath('/partials/addInstitution.html')};
                }
            }),
            controller: 'DialogIntroCtrl'
        })
        .state('app.subscribes', {
            url: '/subscribes',
            templateUrl: helper.basepath('subscribes.html'),
            resolve: helper.resolveFor('ui.grid')
        })
        .state('app.testimonials', {
            url: '/testimonials',
            templateUrl: helper.basepath('testimonials.html'),
            resolve: helper.resolveFor('ui.grid')
        })
        .state('app.blog', {
            url: '/blog',
            templateUrl: helper.basepath('blog.html'),
            resolve: helper.resolveFor('ui.grid', 'textAngular')
        })
        .state('app.comments', {
            url: '/comments',
            templateUrl: helper.basepath('comments.html'),
            resolve: helper.resolveFor('ui.grid')
        })

        .state('app.feeds', {
            url: '/feeds',
            templateUrl: helper.basepath('feeds.html'),
            resolve: helper.resolveFor('localytics.directives', 'ui.grid')
        })
        .state('app.news', {
            url: '/news',
            templateUrl: helper.basepath('news.html'),
            resolve: helper.resolveFor('localytics.directives')
        })
        .state('app.logs', {
            url: '/logs',
            templateUrl: helper.basepath('logs.html'),
            resolve: helper.resolveFor('ui.grid')
        })
        .state('app.custom-post', {
            url: '/custom-post',
            templateUrl: helper.basepath('custom_post.html'),
            resolve: helper.resolveFor('oitozero.ngSweetAlert', 'filestyle', 'textAngular', 'ui.select', 'localytics.directives')
        })
        .state('app.profile', {
            url: '/profile',
            templateUrl: helper.basepath('profile.html'),
            resolve: helper.resolveFor('oitozero.ngSweetAlert')
        })
        .state('page', {
            url: '/page',
            templateUrl: 'app/pages/page.html',
            resolve: helper.resolveFor('modernizr', 'icons'),
            controller: ["$rootScope", function ($rootScope) {
                $rootScope.app.layout.isBoxed = false;
            }]
        })
        .state('app.about-us', {
            url: '/about-us',
            templateUrl: helper.basepath('about.html'),
            resolve: helper.resolveFor('modernizr', 'icons', 'textAngular'),
        })


    }]).config(['$ocLazyLoadProvider', 'APP_REQUIRES', function ($ocLazyLoadProvider, APP_REQUIRES) {
        'use strict';

        // Lazy Load modules configuration
        $ocLazyLoadProvider.config({
            debug: false,
            events: true,
            modules: APP_REQUIRES.modules
        });

    }]).config(['$controllerProvider', '$compileProvider', '$filterProvider', '$provide',
    function ($controllerProvider, $compileProvider, $filterProvider, $provide) {
        'use strict';
        // registering components after bootstrap
        App.controller = $controllerProvider.register;
        App.directive = $compileProvider.directive;
        App.filter = $filterProvider.register;
        App.factory = $provide.factory;
        App.service = $provide.service;
        App.constant = $provide.constant;
        App.value = $provide.value;

    }]).config(['$translateProvider', function ($translateProvider) {

        $translateProvider.useStaticFilesLoader({
            prefix: 'app/i18n/',
            suffix: '.json'
        });
        $translateProvider.preferredLanguage('en');
        $translateProvider.useLocalStorage();
        $translateProvider.usePostCompiling(true);

    }]).config(['tmhDynamicLocaleProvider', function (tmhDynamicLocaleProvider) {

        tmhDynamicLocaleProvider.localeLocationPattern('vendor/angular-i18n/angular-locale_{{locale}}.js');

        // tmhDynamicLocaleProvider.useStorage('$cookieStore');

    }]).config(['cfpLoadingBarProvider', function (cfpLoadingBarProvider) {

        cfpLoadingBarProvider.includeBar = true;
        cfpLoadingBarProvider.includeSpinner = false;
        cfpLoadingBarProvider.latencyThreshold = 500;
        cfpLoadingBarProvider.parentSelector = '.wrapper > section';

    }]).config(['$tooltipProvider', function ($tooltipProvider) {

        $tooltipProvider.options({appendToBody: true});

    }])
    ;

/**=========================================================
 * Module: constants.js
 * Define constants to inject across the application
 =========================================================*/
 App
 .constant('APP_COLORS', {
    'primary': '#5d9cec',
    'success': '#27c24c',
    'info': '#23b7e5',
    'warning': '#ff902b',
    'danger': '#f05050',
    'inverse': '#131e26',
    'green': '#37bc9b',
    'pink': '#f532e5',
    'purple': '#7266ba',
    'dark': '#3a3f51',
    'yellow': '#fad732',
    'gray-darker': '#232735',
    'gray-dark': '#3a3f51',
    'gray': '#dde6e9',
    'gray-light': '#e4eaec',
    'gray-lighter': '#edf1f2'
})
 .constant('APP_MEDIAQUERY', {
    'desktopLG': 1200,
    'desktop': 992,
    'tablet': 768,
    'mobile': 480
})
 .constant('APP_REQUIRES', {
            // jQuery based and standalone scripts
            scripts: {
                'whirl': ['vendor/whirl/dist/whirl.css'],
                'classyloader': ['vendor/jquery-classyloader/js/jquery.classyloader.min.js'],
                'animo': ['vendor/animo.js/animo.js'],
                'fastclick': ['vendor/fastclick/lib/fastclick.js'],
                'modernizr': ['vendor/modernizr/modernizr.js'],
                'animate': ['vendor/animate.css/animate.min.css'],
                'icons': ['vendor/skycons/skycons.js',
                'vendor/fontawesome/css/font-awesome.min.css',
                'vendor/simple-line-icons/css/simple-line-icons.css',
                'vendor/weather-icons/css/weather-icons.min.css'],
                'wysiwyg': ['vendor/bootstrap-wysiwyg/bootstrap-wysiwyg.js',
                'vendor/bootstrap-wysiwyg/external/jquery.hotkeys.js'],
                'slimscroll': ['vendor/slimScroll/jquery.slimscroll.min.js'],
                'screenfull': ['vendor/screenfull/dist/screenfull.js'],
                'vector-map': ['vendor/ika.jvectormap/jquery-jvectormap-1.2.2.min.js',
                'vendor/ika.jvectormap/jquery-jvectormap-1.2.2.css'],
                'vector-map-maps': ['vendor/ika.jvectormap/jquery-jvectormap-world-mill-en.js',
                'vendor/ika.jvectormap/jquery-jvectormap-us-mill-en.js'],
                'loadGoogleMapsJS': ['app/vendor/gmap/load-google-maps.js'],
                'flot-chart': ['vendor/Flot/jquery.flot.js'],
                'flot-chart-plugins': ['vendor/flot.tooltip/js/jquery.flot.tooltip.min.js',
                'vendor/Flot/jquery.flot.resize.js',
                'vendor/Flot/jquery.flot.pie.js',
                'vendor/Flot/jquery.flot.time.js',
                'vendor/Flot/jquery.flot.categories.js',
                'vendor/flot-spline/js/jquery.flot.spline.min.js'],
                // jquery core and widgets
                'jquery-ui': ['vendor/jquery-ui/ui/core.js',
                'vendor/jquery-ui/ui/widget.js'],
                // loads only jquery required modules and touch support
                'jquery-ui-widgets': ['vendor/jquery-ui/ui/core.js',
                'vendor/jquery-ui/ui/widget.js',
                'vendor/jquery-ui/ui/mouse.js',
                'vendor/jquery-ui/ui/draggable.js',
                'vendor/jquery-ui/ui/droppable.js',
                'vendor/jquery-ui/ui/sortable.js',
                'vendor/jqueryui-touch-punch/jquery.ui.touch-punch.min.js'],
                'moment': ['vendor/moment/min/moment-with-locales.min.js'],
                'inputmask': ['vendor/jquery.inputmask/dist/jquery.inputmask.bundle.min.js'],
                'flatdoc': ['vendor/flatdoc/flatdoc.js'],
                'codemirror': ['vendor/codemirror/lib/codemirror.js',
                'vendor/codemirror/lib/codemirror.css'],
                // modes for common web files
                'codemirror-modes-web': ['vendor/codemirror/mode/javascript/javascript.js',
                'vendor/codemirror/mode/xml/xml.js',
                'vendor/codemirror/mode/htmlmixed/htmlmixed.js',
                'vendor/codemirror/mode/css/css.js'],
                'taginput': ['vendor/bootstrap-tagsinput/dist/bootstrap-tagsinput.css',
                'vendor/bootstrap-tagsinput/dist/bootstrap-tagsinput.min.js'],
                'filestyle': ['vendor/bootstrap-filestyle/src/bootstrap-filestyle.js'],
                'parsley': ['vendor/parsleyjs/dist/parsley.min.js'],
                'fullcalendar': ['vendor/fullcalendar/dist/fullcalendar.min.js',
                'vendor/fullcalendar/dist/fullcalendar.css'],
                'gcal': ['vendor/fullcalendar/dist/gcal.js'],
                'chartjs': ['vendor/Chart.js/Chart.js'],
                'morris': ['vendor/raphael/raphael.js',
                'vendor/morris.js/morris.js',
                'vendor/morris.js/morris.css'],
                'loaders.css': ['vendor/loaders.css/loaders.css'],
                'spinkit': ['vendor/spinkit/css/spinkit.css']
            },
            // Angular based script (use the right module name)
            modules: [
            {
                name: 'toaster', files: ['vendor/angularjs-toaster/toaster.js',
                'vendor/angularjs-toaster/toaster.css']
            },
            {
                name: 'localytics.directives', files: ['vendor/chosen_v1.2.0/chosen.jquery.min.js',
                'vendor/chosen_v1.2.0/chosen.min.css',
                'vendor/angular-chosen-localytics/chosen.js']
            },
            {
                name: 'ngDialog', files: ['vendor/ngDialog/js/ngDialog.min.js',
                'vendor/ngDialog/css/ngDialog.min.css',
                'vendor/ngDialog/css/ngDialog-theme-default.min.css']
            },
            {name: 'ngWig', files: ['vendor/ngWig/dist/ng-wig.min.js']},
            {
                name: 'ngTable', files: ['vendor/ng-table/dist/ng-table.min.js',
                'vendor/ng-table/dist/ng-table.min.css']
            },
            {name: 'ngTableExport', files: ['vendor/ng-table-export/ng-table-export.js']},
            {
                name: 'angularBootstrapNavTree', files: ['vendor/angular-bootstrap-nav-tree/dist/abn_tree_directive.js',
                'vendor/angular-bootstrap-nav-tree/dist/abn_tree.css']
            },
            {
                name: 'htmlSortable', files: ['vendor/html.sortable/dist/html.sortable.js',
                'vendor/html.sortable/dist/html.sortable.angular.js']
            },
            {
                name: 'xeditable', files: ['vendor/angular-xeditable/dist/js/xeditable.js',
                'vendor/angular-xeditable/dist/css/xeditable.css']
            },
            {name: 'angularFileUpload', files: ['vendor/angular-file-upload/angular-file-upload.js']},
            {
                name: 'ngImgCrop', files: ['vendor/ng-img-crop/compile/unminified/ng-img-crop.js',
                'vendor/ng-img-crop/compile/unminified/ng-img-crop.css']
            },
            {
                name: 'ui.select', files: ['vendor/angular-ui-select/dist/select.js',
                'vendor/angular-ui-select/dist/select.css']
            },
            {name: 'ui.codemirror', files: ['vendor/angular-ui-codemirror/ui-codemirror.js']},
            {
                name: 'angular-carousel', files: ['vendor/angular-carousel/dist/angular-carousel.css',
                'vendor/angular-carousel/dist/angular-carousel.js']
            },
            {
                name: 'ngGrid', files: ['vendor/ng-grid/build/ng-grid.min.js',
                'vendor/ng-grid/ng-grid.css']
            },
            {name: 'infinite-scroll', files: ['vendor/ngInfiniteScroll/build/ng-infinite-scroll.js']},
            {
                name: 'ui.bootstrap-slider', files: ['vendor/seiyria-bootstrap-slider/dist/bootstrap-slider.min.js',
                'vendor/seiyria-bootstrap-slider/dist/css/bootstrap-slider.min.css',
                'vendor/angular-bootstrap-slider/slider.js']
            },
            {
                name: 'ui.grid', files: ['vendor/angular-ui-grid/ui-grid.min.css',
                'vendor/angular-ui-grid/ui-grid.min.js']
            },
            {
                name: 'textAngular',
                files: ['vendor/textAngular/dist/textAngular.css',
                'vendor/textAngular/dist/textAngular-rangy.min.js',
                'vendor/textAngular/dist/textAngular-sanitize.js',
                'vendor/textAngular/src/globals.js',
                'vendor/textAngular/src/factories.js',
                'vendor/textAngular/src/DOM.js',
                'vendor/textAngular/src/validators.js',
                'vendor/textAngular/src/taBind.js',
                'vendor/textAngular/src/main.js',
                'vendor/textAngular/dist/textAngularSetup.js'
                ],
                serie: true
            },
            {
                name: 'angular-rickshaw', files: ['vendor/d3/d3.min.js',
                'vendor/rickshaw/rickshaw.js',
                'vendor/rickshaw/rickshaw.min.css',
                'vendor/angular-rickshaw/rickshaw.js'], serie: true
            },
            {
                name: 'angular-chartist', files: ['vendor/chartist/dist/chartist.min.css',
                'vendor/chartist/dist/chartist.js',
                'vendor/angular-chartist.js/dist/angular-chartist.js'], serie: true
            },
            {name: 'ui.map', files: ['vendor/angular-ui-map/ui-map.js']},
            {
                name: 'datatables', files: ['vendor/datatables/media/css/jquery.dataTables.css',
                'vendor/datatables/media/js/jquery.dataTables.js',
                'vendor/angular-datatables/dist/angular-datatables.js'], serie: true
            },
            {
                name: 'angular-jqcloud', files: ['vendor/jqcloud2/dist/jqcloud.css',
                'vendor/jqcloud2/dist/jqcloud.js',
                'vendor/angular-jqcloud/angular-jqcloud.js']
            },
            {
                name: 'angularGrid', files: ['vendor/ag-grid/dist/angular-grid.css',
                'vendor/ag-grid/dist/angular-grid.js',
                'vendor/ag-grid/dist/theme-dark.css',
                'vendor/ag-grid/dist/theme-fresh.css']
            },
            {
                name: 'ng-nestable', files: ['vendor/ng-nestable/src/angular-nestable.js',
                'vendor/nestable/jquery.nestable.js']
            },
            {name: 'akoenig.deckgrid', files: ['vendor/angular-deckgrid/angular-deckgrid.js']},
            {
                name: 'oitozero.ngSweetAlert', files: ['vendor/sweetalert/dist/sweetalert.css',
                'vendor/sweetalert/dist/sweetalert.min.js',
                'vendor/angular-sweetalert/SweetAlert.js']
            },
            {
                name: 'bm.bsTour', files: ['vendor/bootstrap-tour/build/css/bootstrap-tour.css',
                'vendor/bootstrap-tour/build/js/bootstrap-tour-standalone.js',
                'vendor/angular-bootstrap-tour/dist/angular-bootstrap-tour.js'], serie: true
            }
            ]
        })
;
/**=========================================================
 * Module: access-login.js
 * Demo for login api
 =========================================================*/

 App.factory('apiBaseDef', [function () {
    return {
        url: "/v1",
        headers: {
            'Content-Type': "application/x-www-form-urlencoded",
            'Authorization': 'Basic b2ZmaWNpYWxAdGVzdC5jb206YXBpT3x8aWNpYWw='
        }
    };
}])

/**=========================================================
 * Module: access-login.js
 * Demo for login api
 =========================================================*/
 App.factory("authenticationSvc", ["$http", "$q", "$window", "apiBaseDef", function ($http, $q, $window, apiBaseDef) {
    var userInfo;
    function login(userName, password) {
        var deferred = $q.defer();
            //deferred.resolve(userInfo);
            $http({
                url: apiBaseDef.url + '/backend/login',
                method: 'POST',
                data: $.param({email: userName, password: password}),
                headers: apiBaseDef.headers
            })
            .then(function (result) {
                if (result.data.code != 0) {
                    console.log(result.data.data[0].token);
                            // var rand = function() {
                            //     return Math.random().toString(36).substr(2); // remove `0.`
                            // };
                            // var token = function() {
                            //     return rand() + rand(); // to make it longer
                            // };
                            userInfo = {
                                userToken: result.data.data[0].token
                            };
                            $window.sessionStorage["userInfo"] = JSON.stringify(userInfo);
                            deferred.resolve(userInfo);
                        } else {
                            // console.log(result.data);
                            userInfo = {
                                error: result.data.message
                            }
                            deferred.reject(userInfo);
                        }
                    }, function (error) {
                        deferred.reject(error);
                    });
            return deferred.promise;
        }
        function logout() {
            var deferred = $q.defer();
            $http({
                url: apiBaseDef.url + 'ajax/buser/logout',
                method: "POST",
                // data: $.param({session: result.data.session}),
                headers: apiBaseDef.headeapiBaseDefrs
            }).then(function (result) {
                userInfo = {
                    session: 0
                };
                $window.sessionStorage["userInfo"] = JSON.stringify(userInfo);
                deferred.resolve(result);
            }, function (error) {
                deferred.reject(error);
            });
            return deferred.promise;
        }
        function getUserInfo() {
            return userInfo;
        }
        function init() {
            if ($window.sessionStorage["userInfo"]) {
                userInfo = JSON.parse($window.sessionStorage["userInfo"]);
            }
        }
        init();
        return {
            login: login,
            logout: logout,
            getUserInfo: getUserInfo
        };
    }]);
 App.controller("LoginFormController", ["$scope", "$location", "$window", "authenticationSvc",
    function ($scope, $location, $window, authenticationSvc) {


        $scope.userInfo = null;
        $scope.login = function () {
            authenticationSvc.login($scope.userName, $scope.password)
            .then(function (result) {
                console.log(result)
                $scope.userInfo = result;
                $location.path("app/dashboard");
            }, function (error) {
                console.log(error)
                if (error) {
                    $scope.wrongCredentials = error.error;
                }
            });
        };
    }]);




/**=========================================================
 * Testimonials
 =========================================================*/

 App.controller('TestimonialsController', ['$scope', 'uiGridConstants', '$http', '$window', 'apiBaseDef',
    function ($scope, uiGridConstants, $http, $window, apiBaseDef) {
        $scope.uploads_folder = '/uploads/testimonials/';

        // Basic example
        // -----------------------------------

        var token = JSON.parse($window.sessionStorage["userInfo"]);
        var userInfo = 'userToken=' + token.userToken;
        $scope.edit_data = {};
        $scope.testimonials = {};


        $scope.loadAll = function () {
            $http.get(apiBaseDef.url + '/testimonials/all?' + userInfo, {headers: apiBaseDef.headers})
            .success(function (result) {
                        //load all testimonials
                        $scope.gridOptions.data = result.data;
                        $scope.testimonials = result.data;
                    });
        }
        $scope.loadAll();


        $scope.gridOptions = {
            rowHeight: 34,
            columnDefs: [
            {
                name: "Id",
                field: 'id',
                width: 75
            },
            {
                name: 'Name',
                field: 'name',
            },
            {
                name: 'Content',
                field: 'content',
            },
            {
                name: 'Position',
                field: 'position',
            },
            {
                name: 'Action',
                field: 'action',
                cellClass: 'text-center',
                cellTemplate: '\
                <button ng-click="grid.appScope.edit_testimonial(grid.renderContainers.body.visibleRowCache.indexOf(row))" title="{{ COL_FIELD }} Deals" id="" class="btn btn-info">{{ COL_FIELD }}<em class="fa fa-pencil"></em></button> &nbsp&nbsp&nbsp&nbsp' +
                '<button class="btn btn-danger" ng-click="grid.appScope.remove_testimonial(grid.renderContainers.body.visibleRowCache.indexOf(row))"><em class="fa fa-trash"></em></>'
            }
            ]
        };

        $scope.edit_testimonial = function (index) {

            $scope.edit_data = $scope.testimonials[index];
            $scope.edit_data.position = parseInt($scope.edit_data.position);
//            console.log($scope.edit_data.id)

$('.subscribe-table').slideUp(function () {
    $('.edit-testimonial').slideDown();
})

}

$scope.uploadFile = function (files) {

    var fd = new FormData();
            //Take the first selected file
            fd.append("photo", files[0]);

            $http.post(apiBaseDef.url + '/testimonials/upload?' + userInfo, fd, {
                withCredentials: true,
                headers: {'Content-Type': undefined, 'Authorization': apiBaseDef.headers.Authorization},
                transformRequest: angular.identity
            }).then(function (response) {
                $scope.edit_data.image = response.data.data[0].file;
                $scope.insert.image = response.data.data[0].file;


            });

        };

        $scope.remove_testimonial = function (index) {
            $scope.data_remove = $scope.testimonials[index];
            $scope.to_delete = {id: $scope.data_remove.id};
            $http({
                url: apiBaseDef.url + '/backend/testimonials?' + userInfo,
                method: 'DELETE',
                headers: apiBaseDef.headers,
                data: $.param($scope.to_delete)
            })
            .then(function (data) {
                $scope.loadAll();
            });

        }

        $scope.updateTestimonial = function () {
            $scope.saveData = {
                id: $scope.edit_data.id,
                name: $scope.edit_data.name,
                content: $scope.edit_data.content,
                image: $scope.edit_data.image,
                position: $scope.edit_data.position
            };


            $http({
                url: apiBaseDef.url + '/backend/testimonials?' + userInfo,
                method: 'PUT',
                headers: apiBaseDef.headers,
                data: $.param($scope.saveData)
            })
            .then(function (data) {
                $scope.loadAll();
                $scope.cancelEdit();
            });
        }


        $scope.showInsertTestimonials = function () {
            $('.subscribe-table').slideUp(function () {
                $('.new-testimonial').slideDown();
            });
            $scope.insert = {};
        }

        $scope.insertTestimonials = function () {
            $scope.insertData = {
                name: $scope.insert.name,
                content: $scope.insert.content,
                position: $scope.insert.position,
                image: $scope.insert.image
            }

            $http({
                url: apiBaseDef.url + '/backend/testimonials?' + userInfo,
                method: 'POST',
                headers: apiBaseDef.headers,
                data: $.param($scope.insertData)
            })
            .then(function (data) {

                $('.new-testimonial, .new-email').slideUp(function () {
                    $('.subscribe-table').slideDown();
                });

                $scope.loadAll();
                $scope.edit_data.image = '';

            });



        }

        $scope.cancelEdit = function () {
            $('.edit-testimonial, .new-email').slideUp(function () {
                $('.subscribe-table').slideDown();
            })
        };

        $scope.cancelInsert = function () {
            $('.new-testimonial, .new-email').slideUp(function () {
                $('.subscribe-table').slideDown();
            });
        }

    }]);

/**=========================================================
 * About Us
 =========================================================*/

 App.controller('AboutController', ['$scope', '$http', '$window', 'apiBaseDef', function ($scope, $http, $window, apiBaseDef) {

    var token = JSON.parse($window.sessionStorage["userInfo"]);
    var userInfo = 'userToken=' + token.userToken;

    $scope.about = {};
    $scope.loadAll = function () {
        $http.get(apiBaseDef.url + '/backend/about-us?' + userInfo, {headers: apiBaseDef.headers})
        .success(function (result) {
            $scope.about = result.data;
        });
    }
    $scope.loadAll();

    $scope.update = function () {

        angular.forEach($scope.about, function (value, key) {
            $http({
                url: apiBaseDef.url + '/backend/about-us/update?' + userInfo,
                method: 'POST',
                headers: apiBaseDef.headers,
                data: $.param(value)
            })
            .then(function (data) {
                swal({
                    title: "Succes!",
                    text: "Conținutul a fost modificat cu succes!",
                    type: "success",
                    timer: 2200,
                    showConfirmButton: false
                });

            });
        });



    }


}]);



/**=========================================================
 * About Us
 =========================================================*/


 App.controller('BlogController', ['$scope', 'uiGridConstants', '$http', '$window', 'apiBaseDef',
    function ($scope, uiGridConstants, $http, $window, apiBaseDef) {
        $scope.uploads_folder = '/uploads/blog/';

        // Basic example
        // -----------------------------------

        var token = JSON.parse($window.sessionStorage["userInfo"]);
        var userInfo = 'userToken=' + token.userToken;
        $scope.edit_data = {};
        $scope.blog = {};

        $scope.loadAll = function () {
            $http.get(apiBaseDef.url + '/backend/blog?' + userInfo, {headers: apiBaseDef.headers})
            .success(function (result) {
                        //load all testimonials
                        $scope.gridOptions.data = result.data;
                        $scope.blog = result.data;
                    });
        }
        $scope.loadAll();


        $scope.gridOptions = {
            rowHeight: 34,
            columnDefs: [
            {
                name: "Id",
                field: 'id',
                width: 75
            },
            {
                name: 'Denumirea',
                field: 'title',
            },
            {
                name: 'Tag',
                field: 'tags',
            },
            {
                name: 'Adaugat',
                field: 'created_at'
            },
            {
                name: 'Action',
                field: 'action',
                cellClass: 'text-center',
                cellTemplate: '\
                <button ng-click="grid.appScope.edit_blog(grid.renderContainers.body.visibleRowCache.indexOf(row))" title="{{ COL_FIELD }} Deals" id="" class="btn btn-info">{{ COL_FIELD }}<em class="fa fa-pencil"></em></button> &nbsp&nbsp&nbsp&nbsp' +
                '<button class="btn btn-danger" ng-click="grid.appScope.remove_blog(grid.renderContainers.body.visibleRowCache.indexOf(row))"><em class="fa fa-trash"></em></>'
            }
            ]
        };

        $scope.edit_blog = function (index) {

            $scope.edit_data = $scope.blog[index];
            $scope.edit_data.position = parseInt($scope.edit_data.position);
//            console.log($scope.edit_data.id)

$('.subscribe-table').slideUp(function () {
    $('.edit-testimonial').slideDown();
})

}

$scope.uploadFile = function (files) {

    var fd = new FormData();
            //Take the first selected file
            fd.append("photo", files[0]);

            $http.post(apiBaseDef.url + '/blog/upload?' + userInfo, fd, {
                withCredentials: true,
                headers: {'Content-Type': undefined, 'Authorization': apiBaseDef.headers.Authorization},
                transformRequest: angular.identity
            }).then(function (response) {

                if ($scope.edit_data)
                {
                    $scope.edit_data.img = response.data.data[0].file;
                }
                if ($scope.insert) {
                    $scope.insert.img = response.data.data[0].file;
                }


            });

        };

        $scope.remove_blog = function (index) {
            $scope.data_remove = $scope.blog[index];
            $scope.to_delete = {id: $scope.data_remove.id};
            $http({
                url: apiBaseDef.url + '/backend/blog?' + userInfo,
                method: 'DELETE',
                headers: apiBaseDef.headers,
                data: $.param($scope.to_delete)
            })
            .then(function (data) {
                $scope.loadAll();
            });

        }

        $scope.updateBlog = function () {
            $scope.saveData = {
                id: $scope.edit_data.id,
                title: $scope.edit_data.title,
                content: $scope.edit_data.content,
                img: $scope.edit_data.img,
                tags: $scope.edit_data.tags
            };

            $http({
                url: apiBaseDef.url + '/backend/blog?' + userInfo,
                method: 'PUT',
                headers: apiBaseDef.headers,
                data: $.param($scope.saveData)
            })
            .then(function (data) {
                $scope.loadAll();
                $scope.cancelEdit();
            });
        }


        $scope.showInsertBlog = function () {
            $('.subscribe-table').slideUp(function () {
                $('.new-testimonial').slideDown();
            });
            $scope.insert = {};
        }

        $scope.insertBlog = function () {
            $scope.insertData = {
                title: $scope.insert.title,
                content: $scope.insert.content,
                tags: $scope.insert.tags,
                img: $scope.insert.img
            }

            $http({
                url: apiBaseDef.url + '/backend/blog?' + userInfo,
                method: 'POST',
                headers: apiBaseDef.headers,
                data: $.param($scope.insertData)
            })
            .then(function (data) {

                $('.new-testimonial, .new-email').slideUp(function () {
                    $('.subscribe-table').slideDown();
                });

                $scope.loadAll();
                $scope.edit_data.image = '';

            });



        }

        $scope.cancelEdit = function () {
            $('.edit-testimonial, .new-email').slideUp(function () {
                $('.subscribe-table').slideDown();
            })
        };

        $scope.cancelInsert = function () {
            $('.new-testimonial, .new-email').slideUp(function () {
                $('.subscribe-table').slideDown();
            });
        }

    }]);

App.controller('CommentController', ['$scope', 'uiGridConstants', '$http', '$window', 'apiBaseDef', function ($scope, uiGridConstants, $http, $window, apiBaseDef) {
    var token = JSON.parse($window.sessionStorage["userInfo"]);
    var userInfo = 'userToken=' + token.userToken;
    $scope.comments = {};


    $scope.gridOptions = {
        rowHeight: 34,
        paginationPageSizes: [5, 10, 25],
        paginationPageSize: 5,
        columnDefs: [
        {
            name: 'Utilizator',
            field: 'name',
        },
        {
            name: 'Comentariu',
            field: 'comment',
        },
        {
            name: 'Adaugat',
            field: 'created_at'
        },
        {
            name: 'Action',
            field: 'action',
            cellClass: 'text-center',
            cellTemplate: '\
            ' +
            '<button class="btn btn-danger" ng-click="grid.appScope.remove_comment(grid.renderContainers.body.visibleRowCache.indexOf(row))"><em class="fa fa-trash"></em></>'
        }
        ]
    };


        //get comment list
        $scope.loadAll = function () {
            $http.get(apiBaseDef.url + '/comments/all?' + userInfo, {headers: apiBaseDef.headers})
            .success(function (result) {
                $scope.gridOptions.data = result.data;
                $scope.comments = result.data;
            });

        }
        
        $scope.loadAll();

//remove comment
$scope.remove_comment = function (index) {
    $scope.data_remove = $scope.comments[index];
    $scope.to_delete = {id: $scope.data_remove.id};
    $http({
        url: apiBaseDef.url + '/backend/comment?' + userInfo,
        method: 'DELETE',
        headers: apiBaseDef.headers,
        data: $.param($scope.to_delete)
    })
    .then(function (data) {
        $scope.loadAll();
    });

}


}]);



/**=========================================================
 * Subscribes
 =========================================================*/

 App.controller('SubscribesController', ['$scope', 'uiGridConstants', '$http', '$window', 'apiBaseDef',
    function ($scope, uiGridConstants, $http, $window, apiBaseDef) {

        // Basic example
        // -----------------------------------

        var token = JSON.parse($window.sessionStorage["userInfo"]);
        var userInfo = 'userToken=' + token.userToken;

        $http.get(apiBaseDef.url + '/backend/subscribers?' + userInfo, {headers: apiBaseDef.headers})
        .success(function (result) {
            $scope.gridOptions.data = result.data;
            $scope.emailData = result.data;
            console.log(result.data)
        });

        $scope.gridOptions = {
            rowHeight: 34,
            columnDefs: [
            {
                name: "Id",
                field: 'id',
                width: 75
            },
            {
                name: 'Email',
                field: 'email',
            },
            {
                name: 'Status',
                field: 'active',
            },
            {
                name: 'Action',
                field: 'action',
                cellClass: 'text-center',
                cellTemplate: '<button ng-click="grid.appScope.edit_feed(grid.renderContainers.body.visibleRowCache.indexOf(row))" title="{{ COL_FIELD }} Deals" id="" class="btn btn-info">{{ COL_FIELD }}<em class="fa fa-pencil"></em></button> &nbsp&nbsp&nbsp&nbsp' +
                '<button class="btn btn-danger" ng-click="grid.appScope.remove_email(grid.renderContainers.body.visibleRowCache.indexOf(row))"><em class="fa fa-trash"></em></>'
            }
            ]
        };

        $scope.edit_feed = function (index) {

            $scope.edit_data = $scope.emailData[index];
            console.log($scope.edit_data.id)

            $('.subscribe-table').slideUp(function () {
                $('.edit-email').slideDown();
            })

        }

        $scope.remove_email = function (index) {
            //console.log(index);
            $data_remove = $scope.emailData[index];

            //backend/unsubscribe

            //$scope.emailData.splice(index, 1);
           
           swal({
                title: "Are you sure?",
                text: "Your will not be able to recover this user!",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Yes, delete it!",
                closeOnConfirm: false}, 
                function(){ 
                    $http({
                        url: apiBaseDef.url + '/backend/unsubscribe/'+$data_remove.id+'?' + userInfo,
                        method: 'DELETE',
                        headers: apiBaseDef.headers
                        
                        
                    }).then( function( success ){
                        console.log(success);
                        $scope.emailData.splice(index, 1);
                        swal("Success!");
                    })
                   
            });

            console.log($data_remove);
        }

        $scope.new_feed = function () {
            console.log('now');
            $('.subscribe-table,.edit-feed').slideUp(function () {
                $('.new-email').slideDown();
            })
        }

        $scope.saveEmail = function () {
            $scope.saveData = {
                id: $scope.edit_data.user_id,
                email: $scope.edit_data.content,
                status: $scope.edit_data.active
            };

            console.log($scope.saveData);

            $http({
                url: apiBaseDef.url + '/backend/subscriber?' + userInfo,
                method: 'PUT',
                headers: apiBaseDef.headers,
                data: $.param($scope.saveData)
            })
            .then(function (data) {
                if (data.data.code == 1) {
                    swal({
                        title: "Horray!",
                        text: "The changes had been successfully saved!",
                        type: "success",
                        timer: 1500,
                        showConfirmButton: false
                    });
                    $('.edit-email').slideUp(function () {
                        $('.subscribe-table').slideDown();
                    })
                } else {
                    swal({
                        title: "Sorry!",
                        text: "Something goes wrong, try again later.",
                        type: "error",
                        timer: 1500,
                        showConfirmButton: false
                    });
                }
            });
        }

        $scope.cancelEdit = function () {
            $('.edit-email, .new-email').slideUp(function () {
                $('.subscribe-table').slideDown();
            })
        };

    }]);

/**=========================================================
 * Feeds
 =========================================================*/


 App.controller('FeedsController', ['$scope', 'uiGridConstants', '$http', '$timeout', 'apiBaseDef', '$window',
    function ($scope, uiGridConstants, $http, $timeout, apiBaseDef, $window) {

        var token = JSON.parse($window.sessionStorage["userInfo"]);
        var userInfo = 'userToken=' + token.userToken;

        $scope.gridOptions = {
            columnDefs: [
            {
                name: "Id",
                field: 'id',
                width: 75
            },
            {
                name: 'Institution Name',
                field: 'institution.name'
            },
            {
                name: 'Institution Url',
                field: 'institution.url'
            },
            {
                name: 'Feed Url',
                field: 'url'
            },
            {
                name: 'Feed Time',
                field: 'scan_date'
            },
            {
                name: 'Action',
                field: 'action',
                cellClass: 'text-center',
                cellTemplate: '<button ng-click="grid.appScope.edit_feed(grid.renderContainers.body.visibleRowCache.indexOf(row))" title="{{ COL_FIELD }} Deals" id="" class="btn btn-info">{{ COL_FIELD }}<em class="fa fa-pencil"></em></button> &nbsp&nbsp&nbsp&nbsp' +
                '<button class="btn btn-danger" ng-click="grid.appScope.remove_feed(grid.renderContainers.body.visibleRowCache.indexOf(row))"><em class="fa fa-trash"></em></>'
            }
            ]
        };

        $scope.feedData = [];
        $scope.allFeeds = {};
        $http.get(apiBaseDef.url + '/feeds?' + userInfo, {headers: apiBaseDef.headers})
        .success(function (data) {
            $scope.allFeeds = data.data;
            angular.forEach(data.data, function (value) {
                $scope.feedData.push(value)
            });
        });




        //filter feeds by institution type        
        $scope.selectedType = 1;
        $scope.changeType = function (type) {
            $scope.feedData = [];
            $scope.selectedType = type;
            angular.forEach($scope.allFeeds, function (value) {
                if (value.institution.type == type)
                    $scope.feedData.push(value);
            });
            $scope.gridOptions.data = $scope.feedData;
        }


        $scope.gridOptions.data = $scope.feedData;

        $scope.edit_feed = function (index) {

            $scope.edit_data = $scope.feedData[index];
            var feed_id = $scope.edit_data.institution.id;

            $scope.selectedInst = '';

            angular.forEach($scope.states, function (state) {
                if (feed_id == state.id) {
                    $scope.selectedInst = state.id;
                } else {
                    // console.log("no")
                }
            })

            console.log($scope.selectedInst)

            $('.feeds-table').slideUp(function () {
                $('.edit-feed').slideDown();
            })

        }

        $scope.saveChanges = function () {

            $scope.saveData = {
                url: $scope.edit_data.url,
                id: $scope.edit_data.id
            }

            $http({
                url: apiBaseDef.url + '/feed?' + userInfo,
                method: 'PUT',
                headers: apiBaseDef.headers,
                data: $.param($scope.saveData)
            })
            .then(function (data) {
                if (data.data.code == 1) {
                    swal({
                        title: "Horray!",
                        text: "The changes had been successfully saved!",
                        type: "success",
                        timer: 1500,
                        showConfirmButton: false
                    });
                    $('.edit-feed').slideUp(function () {
                        $('.feeds-table').slideDown();
                    })
                } else {
                    swal({
                        title: "Sorry!",
                        text: "Something goes wrong, try again later.",
                        type: "error",
                        timer: 1500,
                        showConfirmButton: false
                    });
                }
            });

        }

        $scope.remove_feed = function (index) {
            $data_remove = $scope.feedData[index];

            $scope.feedData.splice(index, 1);
        }

        $scope.new_feed = function () {
            $('.feeds-table,.edit-feed').slideUp(function () {
                $('.new-feed').slideDown();
            })
        }

        $scope.saveFeed = function (data) {
            console.log(data);

            var formData = {
                url: data.url,
                institution: data.institution.id
            }

            var formatedData = $.param(formData);


            $http({
                url: apiBaseDef.url + '/feed?' + userInfo,
                method: 'POST',
                headers: apiBaseDef.headers,
                data: formatedData
            })
            .then(function (data, status) {
                console.log(status);

                if (data.data.code == 1) {
                    swal({
                        title: "Horray!",
                        text: "The changes had been successfully saved!",
                        type: "success",
                        timer: 1500,
                        showConfirmButton: false
                    });
                    $('.new-feed').slideUp(function () {
                        $('.feeds-table').slideDown();
                    })
                } else {
                    swal({
                        title: "Sorry!",
                        text: data.data.message,
                        type: "error",
                        timer: 1500,
                        showConfirmButton: false
                    });
                }
            });
        }

        $scope.cancelEdit = function () {
            $('.edit-feed, .new-feed').slideUp(function () {
                $('.feeds-table').slideDown();
            })
        };
        $http.get(apiBaseDef.url + '/institutions/all?' + apiBaseDef.userToken)
        .success(function (data) {

            $scope.states = data.data;

            $timeout(function () {
                $('.chosen-select').chosen({disable_search_threshold: 100});
            });

        });
    }]);

/**=========================================================
 * News
 =========================================================*/

 App.controller('NewsController', ['$scope', '$http', 'apiBaseDef', '$timeout', '$window',
    function ($scope, $http, apiBaseDef, $timeout, $window) {

        var token = JSON.parse($window.sessionStorage["userInfo"]);
        var userInfo = 'Usertoken=' + token.userToken;

        $http.get(apiBaseDef.url + '/categories?' + userInfo, {headers: apiBaseDef.headers})
        .success(function (data) {
            $scope.categories = data.data;
            console.log($scope.feeds);
        });

        $http.get(apiBaseDef.url + '/backend/feeds?' + userInfo, {headers: apiBaseDef.headers})
        .success(function (data) {
            $scope.feeds = data.data;

            $timeout(function () {
                $('.chosen-select').chosen({disable_search_threshold: 100});
            });
        });


        // $timeout(function () {
        //             $scope.defaults = angular.copy($scope.categories);
        //         });

        $scope.saveChanges = function () {

            $scope.saveData = {
                id: $scope.edit_data.id,
                name: $scope.edit_data.name
            }

            console.log($scope.edit_data.name)

            $http({
                url: apiBaseDef.url + '/category?' + userInfo,
                method: 'PUT',
                headers: apiBaseDef.headers,
                data: $.param($scope.saveData)
            })
            .then(function (data) {
                if (data.data.code == 1) {
                    swal({
                        title: "Horray!",
                        text: "The changes had been successfully saved!",
                        type: "success",
                        timer: 1500,
                        showConfirmButton: false
                    });
                    $('.edit-cat').slideUp(function () {
                        $('.cat-table').slideDown();
                    })
                } else {
                    swal({
                        title: "Sorry!",
                        text: data.data.message,
                        type: "error",
                        timer: 1500,
                        showConfirmButton: false
                    });
                }
            });

            $('.new-email').slideUp(function () {
                $scope.new_data = {};
                $('.subscribe-table').slideDown();
            })
        }

        $scope.addCat = function () {

            if ($('.edit-cat').is(':visible')) {
                $('.edit-cat').slideUp();
            }

            $('.cat-table').slideUp(function () {
                $('.new-cat').slideDown();
            })
        };

        $scope.saveNewCat = function () {

            $scope.feeds = [];
            angular.forEach($scope.new_data.feeds, function (data) {
                $scope.feeds.push(data.id);
            })

            var string = JSON.stringify($scope.feeds);

            console.log(string);

            $scope.saveData = {
                name: $scope.new_data.name,
                feeds: string
            };

            $http({
                url: apiBaseDef.url + '/category?' + userInfo,
                method: 'POST',
                headers: apiBaseDef.headers,
                data: $.param($scope.saveData)
            })
            .then(function (data) {
                if (data.data.code == 1) {
                    swal({
                        title: "Horray!",
                        text: "The changes had been successfully saved!",
                        type: "success",
                        timer: 1500,
                        showConfirmButton: false
                    });
                    $('.new-cat').slideUp(function () {
                        $('.cat-table').slideDown();
                    })
                } else {
                    swal({
                        title: "Sorry!",
                        text: data.data.message,
                        type: "error",
                        timer: 1500,
                        showConfirmButton: false
                    });
                }
            });

        }

        $scope.edit_data_cl = {"id": "", "name": "", "feeds": []}

        $scope.editCat = function (data) {

            $scope.edit_data = data;
            console.log($scope.edit_data);

            $('.cat-table').slideUp(function () {
                $('.edit-cat').slideDown();
            })
        };

        $scope.cancel = function () {
            $('.edit-cat, .new-cat').slideUp(function () {
                $('.cat-table').slideDown();
            })

            // $scope.categories.push({length: 1})

            // angular.copy($scope.defaults, $scope.edit_data);

            console.log($scope.edit_data);
        };
    }]);

/**=========================================================
 * logs
 =========================================================*/

 App.controller('logsCtrl', ['$scope', '$http', 'uiGridConstants', '$window', 'apiBaseDef',
    function ($scope, $http, uiGridConstants, $window, apiBaseDef) {

        var token = JSON.parse($window.sessionStorage["userInfo"]);
        var userInfo = 'userToken=' + token.userToken;

        $http.get(apiBaseDef.url + '/backend/logs?' + userInfo, {headers: apiBaseDef.headers})
        .success(function (data) {
            $scope.gridOptions.data = data.data;
            console.log($scope.feedData)
        });

        $scope.gridOptions = {
            columnDefs: [
            {
                name: "Code",
                field: 'code',
                width: 75
            },
            {
                name: 'Message',
                field: 'message',
            },
            {
                name: 'File',
                field: 'file',
            },
            {
                name: 'Line',
                field: 'line'
            },
            {
                name: 'Date',
                field: 'time_stamp'
            }
            ]
        };

    }]);
/**=========================================================
 * Dashboard
 =========================================================*/

 App.controller('DashboardCtrl', ['$scope', '$http', 'ngDialog', 'apiBaseDef', '$location', 'authenticationSvc', '$window','SweetAlert',
    function ($scope, $http, ngDialog, apiBaseDef, $location, authenticationSvc, $window,SweetAlert) {
        'use strict';

        var token = JSON.parse($window.sessionStorage["userInfo"]);
        var userInfo = 'userToken=' + token.userToken;

        $http.get(apiBaseDef.url + '/backend/dashboard?' + userInfo, {headers: apiBaseDef.headers})
        .success(function (data) {
            $scope.dashboard = data.data;
        });


        $scope.type = 1;

        $scope.changeType = function (type) {

            $scope.type = type;
            $http.get(apiBaseDef.url + '/institutions/all?' + userInfo + '&type=' + $scope.type, {headers: apiBaseDef.headers})
            .success(function (data) {

                $scope.institutions = data.data;

            });

        }


        $http.get(apiBaseDef.url + '/institutions/all?' + userInfo + '&type=1', {headers: apiBaseDef.headers})
        .success(function (data) {

            $scope.institutions = data.data;

        });

        $scope.types = {};
        var dialog_add;
        $scope.editInstitution = function( inst ){
            $http.get(apiBaseDef.url + '/types?' + userInfo, {headers: apiBaseDef.headers}).success(function (data) {
                $scope.types = data.data;
            });
            $scope.editableInstitution = inst;
            // for (var i=0; i<$scope.types.length; i++) {
            //     if ($scope.types[i].id == inst.id ) {
            //         $scope.editableInstitution.type = $scope.types[i];
            //     }
            // }

            
            //console.log( $scope.editableInstitution);
            //console.log($scope.types);
           // console.log($scope.editableInstitution.type);
           dialog_add = ngDialog.open({
            template: 'app/views/partials/editInstitution.html',
            className: 'ngdialog-theme-default',
            scope: $scope
        });
       }
       $scope.updateInstitution = function( data ){
           console.log(data);
        $http({
            method: 'PUT',
            url: apiBaseDef.url + '/institution?' + userInfo,
            headers: apiBaseDef.headers,
            transformRequest: function (obj) {
                var str = [];
                for (var p in obj)
                    str.push(encodeURIComponent(p) + "=" + encodeURIComponent(obj[p]));
                return str.join("&");
            },
            data: {
                id: $scope.editableInstitution.id,
                name: $scope.editableInstitution.name,
                url: $scope.editableInstitution.url,
                type: $scope.editableInstitution.type.id
            }
        }).success(function ( data ) {
            dialog_add.close();
            SweetAlert.swal("Good job!", "You update the institution!", "success");
            $scope.changeType($scope.type);
            console.log( data );
            //$scope.newinstitution = {};
        }, function(){
            SweetAlert.swal("Server error", "Try to update later!", "error");
        });
    }
    $scope.addInsitution = function () {

        $http.get(apiBaseDef.url + '/types?' + userInfo, {headers: apiBaseDef.headers}).success(function (data) {
            $scope.types = data.data;
        });


        dialog_add = ngDialog.open({
            template: $scope.tpl.path,
            className: 'ngdialog-theme-default',
            scope: $scope
        });






    };

    $scope.newinstitution = {};
    $scope.saveNewInstitution = function (data) {


        $http({
            method: 'POST',
            url: apiBaseDef.url + '/user/institutions?' + userInfo,
            headers: apiBaseDef.headers,
            transformRequest: function (obj) {
                var str = [];
                for (var p in obj)
                    str.push(encodeURIComponent(p) + "=" + encodeURIComponent(obj[p]));
                return str.join("&");
            },
            data: {
                name: $scope.newinstitution.name,
                url: $scope.newinstitution.url,
                type: $scope.newinstitution.type
            }
        }).success(function () {
            dialog_add.close();
            $scope.changeType($scope.type);
            $scope.newinstitution = {};
        });


//            $http.post({
//            method:"POST",
//            url:apiBaseDef.url +'/user/institutions?'+userInfo,
//            data:{name:$scope.newinstitution.name,url:$scope.newinstitution.name,type:$scope.newinstitution.type},
//            headers:{apiBaseDef.headers}
//        })


}
}]);

/**=========================================================
 * Custom post
 =========================================================*/
 App.controller('CustomPostController', ['$scope', '$http', 'apiBaseDef', '$window', '$timeout',
    function ($scope, $http, apiBaseDef, $window, $timeout) {

        var token = JSON.parse($window.sessionStorage["userInfo"]);
        var userInfo = 'userToken=' + token.userToken;

        $http.get(apiBaseDef.url + '/backend/feeds?' + userInfo, {headers: apiBaseDef.headers})
        .success(function (data) {
            $scope.feeds = data.data;

            $timeout(function () {
                $('.chosen-select').chosen({disable_search_threshold: 100});
            });
        });

        var base64, type;

        $scope.postType = {type: 1};

        function readImage() {
            if (this.files && this.files[0]) {
                var FR = new FileReader(),
                t = this.files[0].type.split('/');
                type = t[1];
                FR.onload = function (e) {
                    $('#img').attr("src", e.target.result);
                    base64 = e.target.result;
                };
                FR.readAsDataURL(this.files[0]);
            }
        }

        $('.img').change(readImage);

        $scope.post = function () {

            $scope.feeds = [];
            angular.forEach($scope.selectedFeeds, function (data) {
                $scope.feeds.push(data.id);
            })
            var string = JSON.stringify($scope.feeds);
            $scope.article.feeds = string;

            var data = {
                title: $scope.article.title,
                feeds_id: $scope.article.feeds,
                url: $scope.article.custom_link
            };

            if (base64 !== undefined) {
                var s = base64.split(',');
                var file = {
                    type: type,
                    base64: s[1]
                };
                var stringFile = JSON.stringify(file);
                data['file'] = stringFile;
                //delete data.description;
            }

            if ($scope.article.description) {
                data['description'] = $scope.article.description;
            }

            $('.chosen-select').on('change', function (event, params) {
                if ($scope.postType.type == 1) {
                    $scope.file = '';
                    delete data.file;
                } else if ($scope.postType.type == 2) {
                    $scope.article.description = '';
                    delete data.description;
                }
            });

            $http({
                url: apiBaseDef.url + '/backend/article?' + userInfo,
                method: 'POST',
                data: $.param(data),
                headers: apiBaseDef.headers
            }).then(function (result) {
                if (result.data.code != 1) {
                    swal({
                        title: "ERRROR!",
                        text: result.data.message,
                        type: "error",
                        timer: 1500,
                        showConfirmButton: false
                    });

                } else {
                    swal({
                        title: "Horray!",
                        text: result.data.message,
                        type: "success",
                        timer: 1500,
                        showConfirmButton: false
                    }, function () {

                        $scope.article = {
                            title: '',
                            feeds: [],
                            url: ''
                        };
                    });
                }
            })

        }

    }]);

/**=========================================================
 * Profile Controller
 =========================================================*/
 App.controller('ProfileController', ['$scope', '$http', 'apiBaseDef', '$window',
    function ($scope, $http, apiBaseDef, $window) {

        var token = JSON.parse($window.sessionStorage["userInfo"]);
        var userInfo = 'userToken=' + token.userToken;

        $scope.changePass = function () {
            if ($scope.profileForm.$valid) {
                $http({
                    url: apiBaseDef.url + '/backend/profile?' + userInfo,
                    method: "PUT",
                    data: $.param({
                        password: $scope.profile.password,
                        new_password: $scope.profile.new_password,
                        verify_password: $scope.profile.new_password_confirm
                    }),
                    headers: apiBaseDef.headers
                }).then(function (result) {
                    if (result.data.code != 1) {
                        swal({
                            title: "ERRROR!",
                            text: result.data.message,
                            type: "error",
                            timer: 1500,
                            showConfirmButton: false
                        });

                    } else {
                        swal({
                            title: "Horray!",
                            text: result.data.message,
                            type: "success",
                            timer: 1500,
                            showConfirmButton: false
                        });
                    }
                });
            }
        }


    }]);

/**=========================================================
 * DialogIntroCtrl
 =========================================================*/

// Called from the route state. 'tpl' is resolved before
App.controller('DialogIntroCtrl', ['$scope', 'ngDialog', 'tpl', function ($scope, ngDialog, tpl) {
    'user strict';
    $scope.tpl = tpl;

}]);

App.filter('propsFilter', function () {
    return function (items, props) {
        var out = [];

        if (angular.isArray(items)) {
            items.forEach(function (item) {
                var itemMatches = false;

                var keys = Object.keys(props);
                for (var i = 0; i < keys.length; i++) {
                    var prop = keys[i];
                    var text = props[prop].toLowerCase();
                    if (item[prop].toString().toLowerCase().indexOf(text) !== -1) {
                        itemMatches = true;
                        break;
                    }
                }

                if (itemMatches) {
                    out.push(item);
                }
            });
        } else {
            // Let the output be the input untouched
            out = items;
        }

        return out;
    };
});

/**=========================================================
 * Module: main.js
 * Main Application Controller
 =========================================================*/

 App.controller('AppController',
    ['$rootScope', '$scope', '$state', '$translate', '$window', '$localStorage', '$timeout', 'colors', 'browser', 'cfpLoadingBar', '$location',
    function ($rootScope, $scope, $state, $translate, $window, $localStorage, $timeout, colors, browser, cfpLoadingBar, $location) {
        "use strict";

        $scope.logout = function () {
                    // authenticationSvc.logout();

                    var userInfo = null;
                    $window.sessionStorage["userInfo"] = JSON.stringify(userInfo);

                    $location.path("page/login");
                }

                // Setup the layout mode
                $rootScope.app.layout.horizontal = ($rootScope.$stateParams.layout == 'app-h');

                // Loading bar transition
                // -----------------------------------
                var thBar;
                $rootScope.$on('$stateChangeStart', function (event, toState, toParams, fromState, fromParams) {
                    if ($('.wrapper > section').length) // check if bar container exists
                        thBar = $timeout(function () {
                            cfpLoadingBar.start();
                        }, 0); // sets a latency Threshold
                });
                $rootScope.$on('$stateChangeSuccess', function (event, toState, toParams, fromState, fromParams) {
                    event.targetScope.$watch("$viewContentLoaded", function () {
                        $timeout.cancel(thBar);
                        cfpLoadingBar.complete();
                    });
                });


                // Hook not found
                $rootScope.$on('$stateNotFound',
                    function (event, unfoundState, fromState, fromParams) {
                            console.log(unfoundState.to); // "lazy.state"
                            console.log(unfoundState.toParams); // {a:1, b:2}
                            console.log(unfoundState.options); // {inherit:false} + default options
                        });
                // Hook error
                $rootScope.$on('$stateChangeError',
                    function (event, toState, toParams, fromState, fromParams, error) {
                        console.log(error);
                    });
                // Hook success
                $rootScope.$on('$stateChangeSuccess',
                    function (event, toState, toParams, fromState, fromParams) {
                            // display new view from top
                            $window.scrollTo(0, 0);
                            // Save the route title
                            $rootScope.currTitle = $state.current.title;
                        });

                $rootScope.currTitle = $state.current.title;
                $rootScope.pageTitle = function () {
                    var title = $rootScope.app.name + ' - ' + ($rootScope.currTitle || $rootScope.app.description);
                    document.title = title;
                    return title;
                };

                // iPad may presents ghost click issues
                // if( ! browser.ipad )
                // FastClick.attach(document.body);

                // Close submenu when sidebar change from collapsed to normal
                $rootScope.$watch('app.layout.isCollapsed', function (newValue, oldValue) {
                    if (newValue === false)
                        $rootScope.$broadcast('closeSidebarMenu');
                });

                // Restore layout settings
                if (angular.isDefined($localStorage.layout))
                    $scope.app.layout = $localStorage.layout;
                else
                    $localStorage.layout = $scope.app.layout;

                $rootScope.$watch("app.layout", function () {
                    $localStorage.layout = $scope.app.layout;
                }, true);


                // Allows to use branding color with interpolation
                // {{ colorByName('primary') }}
                $scope.colorByName = colors.byName;

                // Hides/show user avatar on sidebar
                $scope.toggleUserBlock = function () {
                    $scope.$broadcast('toggleUserBlock');
                };

                // Internationalization
                // ----------------------

                $scope.language = {
                    // Handles language dropdown
                    listIsOpen: false,
                    // list of available languages
                    available: {
                        'en': 'English',
                        'es_AR': 'Español'
                    },
                    // display always the current ui language
                    init: function () {
                        var proposedLanguage = $translate.proposedLanguage() || $translate.use();
                        var preferredLanguage = $translate.preferredLanguage(); // we know we have set a preferred one in app.config
                        $scope.language.selected = $scope.language.available[(proposedLanguage || preferredLanguage)];
                    },
                    set: function (localeId, ev) {
                        // Set the new idiom
                        $translate.use(localeId);
                        // save a reference for the current language
                        $scope.language.selected = $scope.language.available[localeId];
                        // finally toggle dropdown
                        $scope.language.listIsOpen = !$scope.language.listIsOpen;
                    }
                };

                $scope.language.init();

                // cancel click event easily
                $rootScope.cancel = function ($event) {
                    $event.stopPropagation();
                };

            }]);


// NOTE: We add the service definition here for quick reference
App.service('ngTableDataService', function () {

    var TableData = {
        cache: null,
        getData: function ($defer, params, api) {
            // if no cache, request data and filter
            if (!TableData.cache) {
                if (api) {
                    api.get(function (data) {
                        TableData.cache = data;
                        filterdata($defer, params);
                    });
                }
            } else {
                filterdata($defer, params);
            }

            function filterdata($defer, params) {
                var from = (params.page() - 1) * params.count();
                var to = params.page() * params.count();
                var filteredData = TableData.cache.result.slice(from, to);

                params.total(TableData.cache.total);
                $defer.resolve(filteredData);
            }

        }
    };

    return TableData;

});

/**=========================================================
 * Module: sidebar-menu.js
 * Handle sidebar collapsible elements
 =========================================================*/

 App.controller('SidebarController', ['$rootScope', '$scope', '$state', '$http', '$timeout', 'Utils',
    function ($rootScope, $scope, $state, $http, $timeout, Utils) {

        var collapseList = [];

        // demo: when switch from collapse to hover, close all items
        $rootScope.$watch('app.layout.asideHover', function (oldVal, newVal) {
            if (newVal === false && oldVal === true) {
                closeAllBut(-1);
            }
        });

        // Check item and children active state
        var isActive = function (item) {

            if (!item)
                return;

            if (!item.sref || item.sref == '#') {
                var foundActive = false;
                angular.forEach(item.submenu, function (value, key) {
                    if (isActive(value))
                        foundActive = true;
                });
                return foundActive;
            } else
            return $state.is(item.sref) || $state.includes(item.sref);
        };

        // Load menu from json file
        // -----------------------------------

        $scope.getMenuItemPropClasses = function (item) {
            return (item.heading ? 'nav-heading' : '') +
            (isActive(item) ? ' active' : '');
        };

        $scope.loadSidebarMenu = function () {

            var menuJson = 'server/sidebar-menu.json',
                    menuURL = menuJson + '?v=' + (new Date().getTime()); // jumps cache
                    $http.get(menuURL)
                    .success(function (items) {
                        $scope.menuItems = items;
                    })
                    .error(function (data, status, headers, config) {
                        alert('Failure loading menu');
                    });
                };

                $scope.loadSidebarMenu();

        // Handle sidebar collapse items
        // -----------------------------------

        $scope.addCollapse = function ($index, item) {
            collapseList[$index] = $rootScope.app.layout.asideHover ? true : !isActive(item);
        };

        $scope.isCollapse = function ($index) {
            return (collapseList[$index]);
        };

        $scope.toggleCollapse = function ($index, isParentItem) {


            // collapsed sidebar doesn't toggle drodopwn
            if (Utils.isSidebarCollapsed() || $rootScope.app.layout.asideHover)
                return true;

            // make sure the item index exists
            if (angular.isDefined(collapseList[$index])) {
                if (!$scope.lastEventFromChild) {
                    collapseList[$index] = !collapseList[$index];
                    closeAllBut($index);
                }
            } else if (isParentItem) {
                closeAllBut(-1);
            }

            $scope.lastEventFromChild = isChild($index);

            return true;

        };

        function closeAllBut(index) {
            index += '';
            for (var i in collapseList) {
                if (index < 0 || index.indexOf(i) < 0)
                    collapseList[i] = true;
            }
        }

        function isChild($index) {
            return (typeof $index === 'string') && !($index.indexOf('-') < 0);
        }

    }]);


/**=========================================================
 * Module: anchor.js
 * Disables null anchor behavior
 =========================================================*/

 App.directive('href', function () {

    return {
        restrict: 'A',
        compile: function (element, attr) {
            return function (scope, element) {
                if (attr.ngClick || attr.href === '' || attr.href === '#') {
                    if (!element.hasClass('dropdown-toggle'))
                        element.on('click', function (e) {
                            e.preventDefault();
                            e.stopPropagation();
                        });
                }
            };
        }
    };
});
/**=========================================================
 * Module: animate-enabled.js
 * Enable or disables ngAnimate for element with directive
 =========================================================*/

 App.directive("animateEnabled", ["$animate", function ($animate) {
    return {
        link: function (scope, element, attrs) {
            scope.$watch(function () {
                return scope.$eval(attrs.animateEnabled, scope);
            }, function (newValue) {
                $animate.enabled(!!newValue, element);
            });
        }
    };
}]);
/**=========================================================
 * Module: chart.js
 * Wrapper directive for chartJS.
 * Based on https://gist.github.com/AndreasHeiberg/9837868
 =========================================================*/

 var ChartJS = function (type) {
    return {
        restrict: "A",
        scope: {
            data: "=",
            options: "=",
            id: "@",
            width: "=",
            height: "=",
            resize: "=",
            chart: "@",
            segments: "@",
            responsive: "=",
            tooltip: "=",
            legend: "="
        },
        link: function ($scope, $elem) {
            var ctx = $elem[0].getContext("2d");
            var autosize = false;

            $scope.size = function () {
                if ($scope.width <= 0) {
                    $elem.width($elem.parent().width());
                    ctx.canvas.width = $elem.width();
                } else {
                    ctx.canvas.width = $scope.width || ctx.canvas.width;
                    autosize = true;
                }

                if ($scope.height <= 0) {
                    $elem.height($elem.parent().height());
                    ctx.canvas.height = ctx.canvas.width / 2;
                } else {
                    ctx.canvas.height = $scope.height || ctx.canvas.height;
                    autosize = true;
                }
            };

            $scope.$watch("data", function (newVal, oldVal) {
                if (chartCreated)
                    chartCreated.destroy();

                // if data not defined, exit
                if (!newVal) {
                    return;
                }
                if ($scope.chart) {
                    type = $scope.chart;
                }

                if (autosize) {
                    $scope.size();
                    chart = new Chart(ctx);
                }

                if ($scope.responsive || $scope.resize)
                    $scope.options.responsive = true;

                if ($scope.responsive !== undefined)
                    $scope.options.responsive = $scope.responsive;

                chartCreated = chart[type]($scope.data, $scope.options);
                chartCreated.update();
                if ($scope.legend)
                    angular.element($elem[0]).parent().after(chartCreated.generateLegend());
            }, true);

            $scope.$watch("tooltip", function (newVal, oldVal) {
                if (chartCreated)
                    chartCreated.draw();
                if (newVal === undefined || !chartCreated.segments)
                    return;
                if (!isFinite(newVal) || newVal >= chartCreated.segments.length || newVal < 0)
                    return;
                var activeSegment = chartCreated.segments[newVal];
                activeSegment.save();
                activeSegment.fillColor = activeSegment.highlightColor;
                chartCreated.showTooltip([activeSegment]);
                activeSegment.restore();
            }, true);

            $scope.size();
            var chart = new Chart(ctx);
            var chartCreated;
        }
    };
};

/* Aliases for various chart types */
App.directive("chartjs", function () {
    return ChartJS();
});
App.directive("linechart", function () {
    return ChartJS("Line");
});
App.directive("barchart", function () {
    return ChartJS("Bar");
});
App.directive("radarchart", function () {
    return ChartJS("Radar");
});
App.directive("polarchart", function () {
    return ChartJS("PolarArea");
});
App.directive("piechart", function () {
    return ChartJS("Pie");
});
App.directive("doughnutchart", function () {
    return ChartJS("Doughnut");
});
App.directive("donutchart", function () {
    return ChartJS("Doughnut");
});

/**=========================================================
 * Module: classy-loader.js
 * Enable use of classyloader directly from data attributes
 =========================================================*/

 App.directive('classyloader', ["$timeout", "Utils", function ($timeout, Utils) {
    'use strict';

    var $scroller = $(window),
                inViewFlagClass = 'js-is-in-view'; // a classname to detect when a chart has been triggered after scroll

                return {
                    restrict: 'A',
                    link: function (scope, element, attrs) {
                // run after interpolation
                $timeout(function () {

                    var $element = $(element),
                    options = $element.data();

                    // At lease we need a data-percentage attribute
                    if (options) {
                        if (options.triggerInView) {

                            $scroller.scroll(function () {
                                checkLoaderInVIew($element, options);
                            });
                            // if the element starts already in view
                            checkLoaderInVIew($element, options);
                        } else
                        startLoader($element, options);
                    }

                }, 0);

                function checkLoaderInVIew(element, options) {
                    var offset = -20;
                    if (!element.hasClass(inViewFlagClass) &&
                        Utils.isInView(element, {topoffset: offset})) {
                        startLoader(element, options);
                }
            }

            function startLoader(element, options) {
                element.ClassyLoader(options).addClass(inViewFlagClass);
            }
        }
    };
}]);

/**=========================================================
 * Module: clear-storage.js
 * Removes a key from the browser storage via element click
 =========================================================*/

 App.directive('resetKey', ['$state', '$rootScope', function ($state, $rootScope) {
    'use strict';

    return {
        restrict: 'A',
        scope: {
            resetKey: '='
        },
        link: function (scope, element, attrs) {

            scope.resetKey = attrs.resetKey;

        },
        controller: ["$scope", "$element", function ($scope, $element) {

            $element.on('click', function (e) {
                e.preventDefault();

                if ($scope.resetKey) {
                    delete $rootScope.$storage[$scope.resetKey];
                    $state.go($state.current, {}, {reload: true});
                } else {
                    $.error('No storage key specified for reset.');
                }
            });

        }]

    };

}]);
/**=========================================================
 * Module: filestyle.js
 * Initializes the fielstyle plugin
 =========================================================*/

 App.directive('filestyle', function () {
    return {
        restrict: 'A',
        controller: ["$scope", "$element", function ($scope, $element) {
            var options = $element.data();

                // old usage support
                options.classInput = $element.data('classinput') || options.classInput;

                $element.filestyle(options);
            }]
        };
    });

/**=========================================================
 * Module: flatdoc.js
 * Creates the flatdoc markup and initializes the plugin
 =========================================================*/

 App.directive('flatdoc', ['$location', function ($location) {
    return {
        restrict: "EA",
        template: "<div role='flatdoc'><div role='flatdoc-menu'></div><div role='flatdoc-content'></div></div>",
        link: function (scope, element, attrs) {

            Flatdoc.run({
                fetcher: Flatdoc.file(attrs.src)
            });

            var $root = $('html, body');
            $(document).on('flatdoc:ready', function () {
                var docMenu = $('[role="flatdoc-menu"]');
                docMenu.find('a').on('click', function (e) {
                    e.preventDefault();
                    e.stopPropagation();

                    var $this = $(this);

                    docMenu.find('a.active').removeClass('active');
                    $this.addClass('active');

                    $root.animate({
                        scrollTop: $(this.getAttribute('href')).offset().top - ($('.topnavbar').height() + 10)
                    }, 800);
                });

            });
        }
    };

}]);
/**=========================================================
 * Module: flot.js
 * Initializes the Flot chart plugin and handles data refresh
 =========================================================*/

 App.directive('flot', ['$http', '$timeout', function ($http, $timeout) {
    'use strict';
    return {
        restrict: 'EA',
        template: '<div></div>',
        scope: {
            dataset: '=?',
            options: '=',
            series: '=',
            callback: '=',
            src: '='
        },
        link: linkFunction
    };

    function linkFunction(scope, element, attributes) {
        var height, plot, plotArea, width;
        var heightDefault = 220;

        plot = null;

        width = attributes.width || '100%';
        height = attributes.height || heightDefault;

        plotArea = $(element.children()[0]);
        plotArea.css({
            width: width,
            height: height
        });

        function init() {
            var plotObj;
            if (!scope.dataset || !scope.options)
                return;
            plotObj = $.plot(plotArea, scope.dataset, scope.options);
            scope.$emit('plotReady', plotObj);
            if (scope.callback) {
                scope.callback(plotObj, scope);
            }

            return plotObj;
        }

        function onDatasetChanged(dataset) {
            if (plot) {
                plot.setData(dataset);
                plot.setupGrid();
                return plot.draw();
            } else {
                plot = init();
                onSerieToggled(scope.series);
                return plot;
            }
        }

        scope.$watchCollection('dataset', onDatasetChanged, true);

        function onSerieToggled(series) {
            if (!plot || !series)
                return;
            var someData = plot.getData();
            for (var sName in series) {
                angular.forEach(series[sName], toggleFor(sName));
            }

            plot.setData(someData);
            plot.draw();

            function toggleFor(sName) {
                return function (s, i) {
                    if (someData[i] && someData[i][sName])
                        someData[i][sName].show = s;
                };
            }
        }

        scope.$watch('series', onSerieToggled, true);

        function onSrcChanged(src) {

            if (src) {

                $http.get(src)
                .success(function (data) {

                    $timeout(function () {
                        scope.dataset = data;
                    });

                }).error(function () {
                    $.error('Flot chart: Bad request.');
                });

            }
        }

        scope.$watch('src', onSrcChanged);
    }

}]);

/**=========================================================
 * Module: form-wizard.js
 * Handles form wizard plugin and validation
 =========================================================*/

 App.directive('formWizard', ["$parse", function ($parse) {
    'use strict';

    return {
        restrict: 'A',
        scope: true,
        link: function (scope, element, attribute) {
            var validate = $parse(attribute.validateSteps)(scope),
            wiz = new Wizard(attribute.steps, !!validate, element);
            scope.wizard = wiz.init();

        }
    };

    function Wizard(quantity, validate, element) {

        var self = this;
        self.quantity = parseInt(quantity, 10);
        self.validate = validate;
        self.element = element;

        self.init = function () {
            self.createsteps(self.quantity);
                self.go(1); // always start at fist step
                return self;
            };

            self.go = function (step) {

                if (angular.isDefined(self.steps[step])) {

                    if (self.validate && step !== 1) {
                        var form = $(self.element),
                        group = form.children().children('div').get(step - 2);

                        if (false === form.parsley().validate(group.id)) {
                            return false;
                        }
                    }

                    self.cleanall();
                    self.steps[step] = true;
                }
            };

            self.active = function (step) {
                return !!self.steps[step];
            };

            self.cleanall = function () {
                for (var i in self.steps) {
                    self.steps[i] = false;
                }
            };

            self.createsteps = function (q) {
                self.steps = [];
                for (var i = 1; i <= q; i++)
                    self.steps[i] = false;
            };

        }

    }]);

/**=========================================================
 * Module: fullscreen.js
 * Toggle the fullscreen mode on/off
 =========================================================*/

 App.directive('toggleFullscreen', ['browser', function (browser) {
    'use strict';

    return {
        restrict: 'A',
        link: function (scope, element, attrs) {

                // Not supported under IE
                if (browser.msie) {
                    element.addClass("hide");
                } else {
                    element.on('click', function (e) {
                        e.preventDefault();

                        if (screenfull.enabled) {

                            screenfull.toggle();

                            // Switch icon indicator
                            if (screenfull.isFullscreen)
                                $(this).children('em').removeClass('fa-expand').addClass('fa-compress');
                            else
                                $(this).children('em').removeClass('fa-compress').addClass('fa-expand');

                        } else {
                            $.error('Fullscreen not enabled');
                        }

                    });
                }
            }
        };

    }]);


/**=========================================================
 * Module: load-css.js
 * Request and load into the current page a css file
 =========================================================*/

 App.directive('loadCss', function () {
    'use strict';

    return {
        restrict: 'A',
        link: function (scope, element, attrs) {
            element.on('click', function (e) {
                if (element.is('a'))
                    e.preventDefault();
                var uri = attrs.loadCss,
                link;

                if (uri) {
                    link = createLink(uri);
                    if (!link) {
                        $.error('Error creating stylesheet link element.');
                    }
                } else {
                    $.error('No stylesheet location defined.');
                }

            });

        }
    };

    function createLink(uri) {
        var linkId = 'autoloaded-stylesheet',
        oldLink = $('#' + linkId).attr('id', linkId + '-old');

        $('head').append($('<link/>').attr({
            'id': linkId,
            'rel': 'stylesheet',
            'href': uri
        }));

        if (oldLink.length) {
            oldLink.remove();
        }

        return $('#' + linkId);
    }


});
/**=========================================================
 * Module: masked,js
 * Initializes the masked inputs
 =========================================================*/

 App.directive('masked', function () {
    return {
        restrict: 'A',
        controller: ["$scope", "$element", function ($scope, $element) {
            var $elem = $($element);
            if ($.fn.inputmask)
                $elem.inputmask();
        }]
    };
});

/**=========================================================
 * Module: morris.js
 * AngularJS Directives for Morris Charts
 =========================================================*/

 (function () {
    "use strict";

    App.directive('morrisBar', morrisChart('Bar'));
    App.directive('morrisDonut', morrisChart('Donut'));
    App.directive('morrisLine', morrisChart('Line'));
    App.directive('morrisArea', morrisChart('Area'));

    function morrisChart(type) {
        return function () {
            return {
                restrict: 'EA',
                scope: {
                    morrisData: '=',
                    morrisOptions: '='
                },
                link: function ($scope, elem, attrs) {
                    // start ready to watch for changes in data
                    $scope.$watch("morrisData", function (newVal, oldVal) {
                        if (newVal) {
                            $scope.morrisInstance.setData(newVal);
                            $scope.morrisInstance.redraw();
                        }
                    }, true);
                    // the element that contains the chart
                    $scope.morrisOptions.element = elem;
                    // If data defined copy to options
                    if ($scope.morrisData)
                        $scope.morrisOptions.data = $scope.morrisData;
                    // Init chart
                    $scope.morrisInstance = new Morris[type]($scope.morrisOptions);

                }
            }
        }
    }

})();

/**=========================================================
 * Module: navbar-search.js
 * Navbar search toggler * Auto dismiss on ESC key
 =========================================================*/

 App.directive('searchOpen', ['navSearch', function (navSearch) {
    'use strict';

    return {
        restrict: 'A',
        controller: ["$scope", "$element", function ($scope, $element) {
            $element
            .on('click', function (e) {
                e.stopPropagation();
            })
            .on('click', navSearch.toggle);
        }]
    };

}]).directive('searchDismiss', ['navSearch', function (navSearch) {
    'use strict';

    var inputSelector = '.navbar-form input[type="text"]';

    return {
        restrict: 'A',
        controller: ["$scope", "$element", function ($scope, $element) {

            $(inputSelector)
            .on('click', function (e) {
                e.stopPropagation();
            })
            .on('keyup', function (e) {
                                if (e.keyCode == 27) // ESC
                                    navSearch.dismiss();
                            });

                    // click anywhere closes the search
                    $(document).on('click', navSearch.dismiss);
                    // dismissable options
                    $element
                    .on('click', function (e) {
                        e.stopPropagation();
                    })
                    .on('click', navSearch.dismiss);
                }]
            };

        }]);


/**=========================================================
 * Module: notify.js
 * Directive for notify plugin
 =========================================================*/

 App.directive('notify', ["$window", "Notify", function ($window, Notify) {

    return {
        restrict: 'A',
        scope: {
            options: '=',
            message: '='
        },
        link: function (scope, element, attrs) {

            element.on('click', function (e) {
                e.preventDefault();
                Notify.alert(scope.message, scope.options);
            });

        }
    };

}]);


/**=========================================================
 * Module: now.js
 * Provides a simple way to display the current time formatted
 =========================================================*/

 App.directive("now", ['dateFilter', '$interval', function (dateFilter, $interval) {
    return {
        restrict: 'E',
        link: function (scope, element, attrs) {

            var format = attrs.format;

            function updateTime() {
                var dt = dateFilter(new Date(), format);
                element.text(dt);
            }

            updateTime();
            $interval(updateTime, 1000);
        }
    };
}]);
/**=========================================================
 * Module panel-tools.js
 * Directive tools to control panels.
 * Allows collapse, refresh and dismiss (remove)
 * Saves panel state in browser storage
 =========================================================*/

 App.directive('paneltool', ["$compile", "$timeout", function ($compile, $timeout) {
    var templates = {
        /* jshint multistr: true */
        collapse: "<a href='#' panel-collapse='' tooltip='Collapse Panel' ng-click='{{panelId}} = !{{panelId}}'> \
        <em ng-show='{{panelId}}' class='fa fa-plus'></em> \
        <em ng-show='!{{panelId}}' class='fa fa-minus'></em> \
        </a>",
        dismiss: "<a href='#' panel-dismiss='' tooltip='Close Panel'>\
        <em class='fa fa-times'></em>\
        </a>",
        refresh: "<a href='#' panel-refresh='' data-spinner='{{spinner}}' tooltip='Refresh Panel'>\
        <em class='fa fa-refresh'></em>\
        </a>"
    };

    function getTemplate(elem, attrs) {
        var temp = '';
        attrs = attrs || {};
        if (attrs.toolCollapse)
            temp += templates.collapse.replace(/{{panelId}}/g, (elem.parent().parent().attr('id')));
        if (attrs.toolDismiss)
            temp += templates.dismiss;
        if (attrs.toolRefresh)
            temp += templates.refresh.replace(/{{spinner}}/g, attrs.toolRefresh);
        return temp;
    }

    return {
        restrict: 'E',
        scope: false,
        link: function (scope, element, attrs) {

            var tools = scope.panelTools || attrs;

            $timeout(function () {
                element.html(getTemplate(element, tools)).show();
                $compile(element.contents())(scope);

                element.addClass('pull-right');
            });

        }
    };
}])
        /**=========================================================
         * Dismiss panels * [panel-dismiss]
         =========================================================*/
         .directive('panelDismiss', ["$q", "Utils", function ($q, Utils) {
            'use strict';
            return {
                restrict: 'A',
                controller: ["$scope", "$element", function ($scope, $element) {
                    var removeEvent = 'panel-remove',
                    removedEvent = 'panel-removed';

                    $element.on('click', function () {

                                // find the first parent panel
                                var parent = $(this).closest('.panel');

                                removeElement();

                                function removeElement() {
                                    var deferred = $q.defer();
                                    var promise = deferred.promise;

                                    // Communicate event destroying panel
                                    $scope.$emit(removeEvent, parent.attr('id'), deferred);
                                    promise.then(destroyMiddleware);
                                }

                                // Run the animation before destroy the panel
                                function destroyMiddleware() {
                                    if (Utils.support.animation) {
                                        parent.animo({animation: 'bounceOut'}, destroyPanel);
                                    } else
                                    destroyPanel();
                                }

                                function destroyPanel() {

                                    var col = parent.parent();
                                    parent.remove();
                                    // remove the parent if it is a row and is empty and not a sortable (portlet)
                                    col
                                    .filter(function () {
                                        var el = $(this);
                                        return (el.is('[class*="col-"]:not(.sortable)') && el.children('*').length === 0);
                                    }).remove();

                                    // Communicate event destroyed panel
                                    $scope.$emit(removedEvent, parent.attr('id'));

                                }
                            });
                }]
            };
        }])
        /**=========================================================
         * Collapse panels * [panel-collapse]
         =========================================================*/
         .directive('panelCollapse', ['$timeout', function ($timeout) {
            'use strict';

            var storageKeyName = 'panelState',
            storage;

            return {
                restrict: 'A',
                scope: false,
                controller: ["$scope", "$element", function ($scope, $element) {

                            // Prepare the panel to be collapsible
                            var $elem = $($element),
                                    parent = $elem.closest('.panel'), // find the first parent panel
                                    panelId = parent.attr('id');

                                    storage = $scope.$storage;

                            // Load the saved state if exists
                            var currentState = loadPanelState(panelId);
                            if (typeof currentState !== 'undefined') {
                                $timeout(function () {
                                    $scope[panelId] = currentState;
                                },
                                10);
                            }

                            // bind events to switch icons
                            $element.bind('click', function () {

                                savePanelState(panelId, !$scope[panelId]);

                            });
                        }]
                    };

                    function savePanelState(id, state) {
                        if (!id)
                            return false;
                        var data = angular.fromJson(storage[storageKeyName]);
                        if (!data) {
                            data = {};
                        }
                        data[id] = state;
                        storage[storageKeyName] = angular.toJson(data);
                    }

                    function loadPanelState(id) {
                        if (!id)
                            return false;
                        var data = angular.fromJson(storage[storageKeyName]);
                        if (data) {
                            return data[id];
                        }
                    }

                }])
        /**=========================================================
         * Refresh panels
         * [panel-refresh] * [data-spinner="standard"]
         =========================================================*/
         .directive('panelRefresh', ["$q", function ($q) {
            'use strict';

            return {
                restrict: 'A',
                scope: false,
                controller: ["$scope", "$element", function ($scope, $element) {

                    var refreshEvent = 'panel-refresh',
                    whirlClass = 'whirl',
                    defaultSpinner = 'standard';


                            // catch clicks to toggle panel refresh
                            $element.on('click', function () {
                                var $this = $(this),
                                panel = $this.parents('.panel').eq(0),
                                spinner = $this.data('spinner') || defaultSpinner
                                ;

                                // start showing the spinner
                                panel.addClass(whirlClass + ' ' + spinner);

                                // Emit event when refresh clicked
                                $scope.$emit(refreshEvent, panel.attr('id'));

                            });

                            // listen to remove spinner
                            $scope.$on('removeSpinner', removeSpinner);

                            // method to clear the spinner when done
                            function removeSpinner(ev, id) {
                                if (!id)
                                    return;
                                var newid = id.charAt(0) == '#' ? id : ('#' + id);
                                angular
                                .element(newid)
                                .removeClass(whirlClass);
                            }
                        }]
                    };
                }]);

/**=========================================================
 * Module: play-animation.js
 * Provides a simple way to run animation with a trigger
 * Requires animo.js
 =========================================================*/

 App.directive('animate', ["$window", "Utils", function ($window, Utils) {

    'use strict';

    var $scroller = $(window).add('body, .wrapper');

    return {
        restrict: 'A',
        link: function (scope, elem, attrs) {

                // Parse animations params and attach trigger to scroll
                var $elem = $(elem),
                offset = $elem.data('offset'),
                        delay = $elem.data('delay') || 100, // milliseconds
                        animation = $elem.data('play') || 'bounce';

                        if (typeof offset !== 'undefined') {

                    // test if the element starts visible
                    testAnimation($elem);
                    // test on scroll
                    $scroller.scroll(function () {
                        testAnimation($elem);
                    });

                }

                // Test an element visibilty and trigger the given animation
                function testAnimation(element) {
                    if (!element.hasClass('anim-running') &&
                        Utils.isInView(element, {topoffset: offset})) {
                        element
                    .addClass('anim-running');

                    setTimeout(function () {
                        element
                        .addClass('anim-done')
                        .animo({animation: animation, duration: 0.7});
                    }, delay);

                }
            }

                // Run click triggered animations
                $elem.on('click', function () {

                    var $elem = $(this),
                    targetSel = $elem.data('target'),
                    animation = $elem.data('play') || 'bounce',
                    target = $(targetSel);

                    if (target && target.length) {
                        target.animo({animation: animation});
                    }

                });
            }
        };

    }]);

/**=========================================================
 * Module: scroll.js
 * Make a content box scrollable
 =========================================================*/

 App.directive('scrollable', function () {
    return {
        restrict: 'EA',
        link: function (scope, elem, attrs) {
            var defaultHeight = 250;
            elem.slimScroll({
                height: (attrs.height || defaultHeight)
            });
        }
    };
});
/**=========================================================
 * Module: sidebar.js
 * Wraps the sidebar and handles collapsed state
 =========================================================*/

 App.directive('sidebar', ['$rootScope', '$timeout', '$window', 'Utils', function ($rootScope, $timeout, $window, Utils) {

    var $win = $($window);
    var $body = $('body');
    var $scope;
    var $sidebar;
    var currentState = $rootScope.$state.current.name;

    return {
        restrict: 'EA',
        template: '<nav class="sidebar" ng-transclude></nav>',
        transclude: true,
        replace: true,
        link: function (scope, element, attrs) {

            $scope = scope;
            $sidebar = element;

            var eventName = Utils.isTouch() ? 'click' : 'mouseenter';
            var subNav = $();
            $sidebar.on(eventName, '.nav > li', function () {

                if (Utils.isSidebarCollapsed() || $rootScope.app.layout.asideHover) {

                    subNav.trigger('mouseleave');
                    subNav = toggleMenuItem($(this));

                        // Used to detect click and touch events outside the sidebar
                        sidebarAddBackdrop();

                    }

                });

            scope.$on('closeSidebarMenu', function () {
                removeFloatingNav();
            });

                // Normalize state when resize to mobile
                $win.on('resize', function () {
                    if (!Utils.isMobile())
                        asideToggleOff();
                });

                // Adjustment on route changes
                $rootScope.$on('$stateChangeStart', function (event, toState, toParams, fromState, fromParams) {
                    currentState = toState.name;
                    // Hide sidebar automatically on mobile
                    asideToggleOff();

                    $rootScope.$broadcast('closeSidebarMenu');
                });

                // Autoclose when click outside the sidebar
                if (angular.isDefined(attrs.sidebarAnyclickClose)) {

                    var wrapper = $('.wrapper');
                    var sbclickEvent = 'click.sidebar';

                    $rootScope.$watch('app.asideToggled', watchExternalClicks);

                }

                //////

                function watchExternalClicks(newVal) {
                    // if sidebar becomes visible
                    if (newVal === true) {
                        $timeout(function () { // render after current digest cycle
                            wrapper.on(sbclickEvent, function (e) {
                                // if not child of sidebar
                                if (!$(e.target).parents('.aside').length) {
                                    asideToggleOff();
                                }
                            });
                        });
                    } else {
                        // dettach event
                        wrapper.off(sbclickEvent);
                    }
                }

                function asideToggleOff() {
                    $rootScope.app.asideToggled = false;
                    if (!$scope.$$phase)
                        $scope.$apply(); // anti-pattern but sometimes necessary
                }
            }
        };

        function sidebarAddBackdrop() {
            var $backdrop = $('<div/>', {'class': 'dropdown-backdrop'});
            $backdrop.insertAfter('.aside-inner').on("click mouseenter", function () {
                removeFloatingNav();
            });
        }

        // Open the collapse sidebar submenu items when on touch devices
        // - desktop only opens on hover
        function toggleTouchItem($element) {
            $element
            .siblings('li')
            .removeClass('open')
            .end()
            .toggleClass('open');
        }

        // Handles hover to open items under collapsed menu
        // -----------------------------------
        function toggleMenuItem($listItem) {

            removeFloatingNav();

            var ul = $listItem.children('ul');

            if (!ul.length)
                return $();
            if ($listItem.hasClass('open')) {
                toggleTouchItem($listItem);
                return $();
            }

            var $aside = $('.aside');
            var $asideInner = $('.aside-inner'); // for top offset calculation
            // float aside uses extra padding on aside
            var mar = parseInt($asideInner.css('padding-top'), 0) + parseInt($aside.css('padding-top'), 0);
            var subNav = ul.clone().appendTo($aside);

            toggleTouchItem($listItem);

            var itemTop = ($listItem.position().top + mar) - $sidebar.scrollTop();
            var vwHeight = $win.height();

            subNav
            .addClass('nav-floating')
            .css({
                position: $scope.app.layout.isFixed ? 'fixed' : 'absolute',
                top: itemTop,
                bottom: (subNav.outerHeight(true) + itemTop > vwHeight) ? 0 : 'auto'
            });

            subNav.on('mouseleave', function () {
                toggleTouchItem($listItem);
                subNav.remove();
            });

            return subNav;
        }

        function removeFloatingNav() {
            $('.dropdown-backdrop').remove();
            $('.sidebar-subnav.nav-floating').remove();
            $('.sidebar li.open').removeClass('open');
        }

    }]);
/**=========================================================
 * Module: skycons.js
 * Include any animated weather icon from Skycons
 =========================================================*/

 App.directive('skycon', function () {

    return {
        restrict: 'A',
        link: function (scope, element, attrs) {

            var skycons = new Skycons({'color': (attrs.color || 'white')});

            element.html('<canvas width="' + attrs.width + '" height="' + attrs.height + '"></canvas>');

            skycons.add(element.children()[0], attrs.skycon);

            skycons.play();

        }
    };
});
/**=========================================================
 * Module: sparkline.js
 * SparkLines Mini Charts
 =========================================================*/

// App.directive('sparkline', ['$timeout', '$window', function ($timeout, $window) {

//     'use strict';

//     return {
//         restrict: 'EA',
//         controller: ["$scope", "$element", function ($scope, $element) {
//             var runSL = function () {
//                 initSparLine($element);
//             };

//             $timeout(runSL);
//         }]
//     };

//     function initSparLine($element) {
//         var options = $element.data();

//         options.type = options.type || 'bar'; // default chart is bar
//         options.disableHiddenCheck = true;

//         $element.sparkline('html', options);

//         if (options.resize) {
//             $(window).resize(function () {
//                 $element.sparkline('html', options);
//             });
//         }
//     }

// }]);

/**=========================================================
 * Module: table-checkall.js
 * Tables check all checkbox
 =========================================================*/

 App.directive('checkAll', function () {
    'use strict';

    return {
        restrict: 'A',
        controller: ["$scope", "$element", function ($scope, $element) {

            $element.on('change', function () {
                var $this = $(this),
                index = $this.index() + 1,
                checkbox = $this.find('input[type="checkbox"]'),
                table = $this.parents('table');
                    // Make sure to affect only the correct checkbox column
                    table.find('tbody > tr > td:nth-child(' + index + ') input[type="checkbox"]')
                    .prop('checked', checkbox[0].checked);

                });
        }]
    };

});
/**=========================================================
 * Module: tags-input.js
 * Initializes the tag inputs plugin
 =========================================================*/

 App.directive('tagsinput', ["$timeout", function ($timeout) {
    return {
        restrict: 'A',
        require: 'ngModel',
        link: function (scope, element, attrs, ngModel) {

            element.on('itemAdded itemRemoved', function () {
                    // check if view value is not empty and is a string
                    // and update the view from string to an array of tags
                    if (ngModel.$viewValue && ngModel.$viewValue.split) {
                        ngModel.$setViewValue(ngModel.$viewValue.split(','));
                        ngModel.$render();
                    }
                });

            $timeout(function () {
                element.tagsinput();
            });

        }
    };
}]);

/**=========================================================
 * Module: trigger-resize.js
 * Triggers a window resize event from any element
 =========================================================*/

 App.directive("triggerResize", ['$window', '$timeout', function ($window, $timeout) {
    return {
        restrict: 'A',
        link: function (scope, element, attrs) {
            element.on('click', function () {
                $timeout(function () {
                    $window.dispatchEvent(new Event('resize'))
                });
            });
        }
    };
}]);

/**=========================================================
 * Module: validate-form.js
 * Initializes the validation plugin Parsley
 =========================================================*/

 App.directive('validateForm', function () {
    return {
        restrict: 'A',
        controller: ["$scope", "$element", function ($scope, $element) {
            var $elem = $($element);
            if ($.fn.parsley)
                $elem.parsley();
        }]
    };
});

/**=========================================================
 * Module: vector-map.js.js
 * Init jQuery Vector Map plugin
 =========================================================*/

 App.directive('vectorMap', ['vectorMap', function (vectorMap) {
    'use strict';

    var defaultColors = {
            markerColor: '#23b7e5', // the marker points
            bgColor: 'transparent', // the background
            scaleColors: ['#878c9a'], // the color of the region in the serie
            regionFill: '#bbbec6'       // the base region color
        };

        return {
            restrict: 'EA',
            link: function (scope, element, attrs) {

                var mapHeight = attrs.height || '300',
                options = {
                    markerColor: attrs.markerColor || defaultColors.markerColor,
                    bgColor: attrs.bgColor || defaultColors.bgColor,
                    scale: attrs.scale || 1,
                    scaleColors: attrs.scaleColors || defaultColors.scaleColors,
                    regionFill: attrs.regionFill || defaultColors.regionFill,
                    mapName: attrs.mapName || 'world_mill_en'
                };

                element.css('height', mapHeight);

                vectorMap.init(element, options, scope.seriesData, scope.markersData);

            }
        };

    }]);
/**=========================================================
 * Module: browser.js
 * Browser detection
 =========================================================*/

 (function () {
    'use strict';

    App.service('browser', Browser);

    function Browser() {
        return window.jQBrowser;
    }

})();

/**=========================================================
 * Module: colors.js
 * Services to retrieve global colors
 =========================================================*/

 App.factory('colors', ['APP_COLORS', function (colors) {

    return {
        byName: function (name) {
            return (colors[name] || '#fff');
        }
    };

}]);

/**=========================================================
 * Module: nav-search.js
 * Services to share navbar search functions
 =========================================================*/

 App.service('navSearch', function () {
    var navbarFormSelector = 'form.navbar-form';
    return {
        toggle: function () {

            var navbarForm = $(navbarFormSelector);

            navbarForm.toggleClass('open');

            var isOpen = navbarForm.hasClass('open');

            navbarForm.find('input')[isOpen ? 'focus' : 'blur']();

        },
        dismiss: function () {
            $(navbarFormSelector)
                    .removeClass('open')      // Close control
                    .find('input[type="text"]').blur() // remove focus
                    .val('')                    // Empty input
                    ;
                }
            };

        });
/**=========================================================
 * Module: notify.js
 * Create a notifications that fade out automatically.
 * Based on Notify addon from UIKit (http://getuikit.com/docs/addons_notify.html)
 =========================================================*/

 App.service('Notify', ["$timeout", function ($timeout) {
    this.alert = alert;

        ////////////////

        function alert(msg, opts) {
            if (msg) {
                $timeout(function () {
                    $.notify(msg, opts || {});
                });
            }
        }

    }]);


/**
 * Notify Addon definition as jQuery plugin
 * Adapted version to work with Bootstrap classes
 * More information http://getuikit.com/docs/addons_notify.html
 */

 (function ($, window, document) {

    var containers = {},
    messages = {},
    notify = function (options) {

        if ($.type(options) == 'string') {
            options = {message: options};
        }

        if (arguments[1]) {
            options = $.extend(options, $.type(arguments[1]) == 'string' ? {status: arguments[1]} : arguments[1]);
        }

        return (new Message(options)).show();
    },
    closeAll = function (group, instantly) {
        if (group) {
            for (var id in messages) {
                if (group === messages[id].group)
                    messages[id].close(instantly);
            }
        } else {
            for (var id in messages) {
                messages[id].close(instantly);
            }
        }
    };

    var Message = function (options) {

        var $this = this;

        this.options = $.extend({}, Message.defaults, options);

        this.uuid = "ID" + (new Date().getTime()) + "RAND" + (Math.ceil(Math.random() * 100000));
        this.element = $([
            // @geedmo: alert-dismissable enables bs close icon
            '<div class="uk-notify-message alert-dismissable">',
            '<a class="close">&times;</a>',
            '<div>' + this.options.message + '</div>',
            '</div>'

            ].join('')).data("notifyMessage", this);

        // status
        if (this.options.status) {
            this.element.addClass('alert alert-' + this.options.status);
            this.currentstatus = this.options.status;
        }

        this.group = this.options.group;

        messages[this.uuid] = this;

        if (!containers[this.options.pos]) {
            containers[this.options.pos] = $('<div class="uk-notify uk-notify-' + this.options.pos + '"></div>').appendTo('body').on("click", ".uk-notify-message", function () {
                $(this).data("notifyMessage").close();
            });
        }
    };


    $.extend(Message.prototype, {
        uuid: false,
        element: false,
        timout: false,
        currentstatus: "",
        group: false,
        show: function () {

            if (this.element.is(":visible"))
                return;

            var $this = this;

            containers[this.options.pos].show().prepend(this.element);

            var marginbottom = parseInt(this.element.css("margin-bottom"), 10);

            this.element.css({
                "opacity": 0,
                "margin-top": -1 * this.element.outerHeight(),
                "margin-bottom": 0
            }).animate({"opacity": 1, "margin-top": 0, "margin-bottom": marginbottom}, function () {

                if ($this.options.timeout) {

                    var closefn = function () {
                        $this.close();
                    };

                    $this.timeout = setTimeout(closefn, $this.options.timeout);

                    $this.element.hover(
                        function () {
                            clearTimeout($this.timeout);
                        },
                        function () {
                            $this.timeout = setTimeout(closefn, $this.options.timeout);
                        }
                        );
                }

            });

            return this;
        },
        close: function (instantly) {

            var $this = this,
            finalize = function () {
                $this.element.remove();

                if (!containers[$this.options.pos].children().length) {
                    containers[$this.options.pos].hide();
                }

                delete messages[$this.uuid];
            };

            if (this.timeout)
                clearTimeout(this.timeout);

            if (instantly) {
                finalize();
            } else {
                this.element.animate({
                    "opacity": 0,
                    "margin-top": -1 * this.element.outerHeight(),
                    "margin-bottom": 0
                }, function () {
                    finalize();
                });
            }
        },
        content: function (html) {

            var container = this.element.find(">div");

            if (!html) {
                return container.html();
            }

            container.html(html);

            return this;
        },
        status: function (status) {

            if (!status) {
                return this.currentstatus;
            }

            this.element.removeClass('alert alert-' + this.currentstatus).addClass('alert alert-' + status);

            this.currentstatus = status;

            return this;
        }
    });

    Message.defaults = {
        message: "",
        status: "normal",
        timeout: 5000,
        group: null,
        pos: 'top-center'
    };


    $["notify"] = notify;
    $["notify"].message = Message;
    $["notify"].closeAll = closeAll;

    return notify;

}(jQuery, window, document));

/**=========================================================
 * Module: helpers.js
 * Provides helper functions for routes definition
 =========================================================*/

 App.provider('RouteHelpers', ['APP_REQUIRES', function (appRequires) {
    "use strict";

        // Set here the base of the relative path
        // for all app views
        this.basepath = function (uri) {
            return baseUrl + 'app/views/' + uri;
        };

        // Generates a resolve object by passing script names
        // previously configured in constant.APP_REQUIRES
        this.resolveFor = function () {
            var _args = arguments;
            return {
                deps: ['$ocLazyLoad', '$q', function ($ocLL, $q) {
                        // Creates a promise chain for each argument
                        var promise = $q.when(1); // empty promise
                        for (var i = 0, len = _args.length; i < len; i++) {
                            promise = andThen(_args[i]);
                        }
                        return promise;

                        // creates promise to chain dynamically
                        function andThen(_arg) {
                            // also support a function that returns a promise
                            if (typeof _arg == 'function')
                                return promise.then(_arg);
                            else
                                return promise.then(function () {
                                    // if is a module, pass the name. If not, pass the array
                                    var whatToLoad = getRequired(_arg);
                                    // simple error check
                                    if (!whatToLoad)
                                        return $.error('Route resolve: Bad resource name [' + _arg + ']');
                                    // finally, return a promise
                                    return $ocLL.load(whatToLoad);
                                });
                        }

                        // check and returns required data
                        // analyze module items with the form [name: '', files: []]
                        // and also simple array of script files (for not angular js)
                        function getRequired(name) {
                            if (appRequires.modules)
                                for (var m in appRequires.modules)
                                    if (appRequires.modules[m].name && appRequires.modules[m].name === name)
                                        return appRequires.modules[m];
                                    return appRequires.scripts && appRequires.scripts[name];
                                }

                            }]
                        };
        }; // resolveFor

        // not necessary, only used in config block for routes
        this.$get = function () {

            return {
                basepath: this.basepath
            }
        };

    }]);


/**=========================================================
 * Module: utils.js
 * Utility library to use across the theme
 =========================================================*/

 App.service('Utils', ["$window", "APP_MEDIAQUERY", function ($window, APP_MEDIAQUERY) {
    'use strict';

    var $html = angular.element("html"),
    $win = angular.element($window),
    $body = angular.element('body');

    return {
            // DETECTION
            support: {
                transition: (function () {
                    var transitionEnd = (function () {

                        var element = document.body || document.documentElement,
                        transEndEventNames = {
                            WebkitTransition: 'webkitTransitionEnd',
                            MozTransition: 'transitionend',
                            OTransition: 'oTransitionEnd otransitionend',
                            transition: 'transitionend'
                        }, name;

                        for (name in transEndEventNames) {
                            if (element.style[name] !== undefined)
                                return transEndEventNames[name];
                        }
                    }());

                    return transitionEnd && {end: transitionEnd};
                })(),
                animation: (function () {

                    var animationEnd = (function () {

                        var element = document.body || document.documentElement,
                        animEndEventNames = {
                            WebkitAnimation: 'webkitAnimationEnd',
                            MozAnimation: 'animationend',
                            OAnimation: 'oAnimationEnd oanimationend',
                            animation: 'animationend'
                        }, name;

                        for (name in animEndEventNames) {
                            if (element.style[name] !== undefined)
                                return animEndEventNames[name];
                        }
                    }());

                    return animationEnd && {end: animationEnd};
                })(),
                requestAnimationFrame: window.requestAnimationFrame ||
                window.webkitRequestAnimationFrame ||
                window.mozRequestAnimationFrame ||
                window.msRequestAnimationFrame ||
                window.oRequestAnimationFrame ||
                function (callback) {
                    window.setTimeout(callback, 1000 / 60);
                },
                touch: (
                    ('ontouchstart' in window && navigator.userAgent.toLowerCase().match(/mobile|tablet/)) ||
                    (window.DocumentTouch && document instanceof window.DocumentTouch) ||
                        (window.navigator['msPointerEnabled'] && window.navigator['msMaxTouchPoints'] > 0) || //IE 10
                        (window.navigator['pointerEnabled'] && window.navigator['maxTouchPoints'] > 0) || //IE >=11
                        false
                        ),
                mutationobserver: (window.MutationObserver || window.WebKitMutationObserver || window.MozMutationObserver || null)
            },
            // UTILITIES
            isInView: function (element, options) {

                var $element = $(element);

                if (!$element.is(':visible')) {
                    return false;
                }

                var window_left = $win.scrollLeft(),
                window_top = $win.scrollTop(),
                offset = $element.offset(),
                left = offset.left,
                top = offset.top;

                options = $.extend({topoffset: 0, leftoffset: 0}, options);

                if (top + $element.height() >= window_top && top - options.topoffset <= window_top + $win.height() &&
                    left + $element.width() >= window_left && left - options.leftoffset <= window_left + $win.width()) {
                    return true;
            } else {
                return false;
            }
        },
        langdirection: $html.attr("dir") == "rtl" ? "right" : "left",
        isTouch: function () {
            return $html.hasClass('touch');
        },
        isSidebarCollapsed: function () {
            return $body.hasClass('aside-collapsed');
        },
        isSidebarToggled: function () {
            return $body.hasClass('aside-toggled');
        },
        isMobile: function () {
            return $win.width() < APP_MEDIAQUERY.tablet;
        }
    };
}]);
/**=========================================================
 * Module: vector-map.js
 * Services to initialize vector map plugin
 =========================================================*/

 App.service('vectorMap', function () {
    'use strict';
    return {
        init: function ($element, opts, series, markers) {
            $element.vectorMap({
                map: opts.mapName,
                backgroundColor: opts.bgColor,
                zoomMin: 1,
                zoomMax: 8,
                zoomOnScroll: false,
                regionStyle: {
                    initial: {
                        'fill': opts.regionFill,
                        'fill-opacity': 1,
                        'stroke': 'none',
                        'stroke-width': 1.5,
                        'stroke-opacity': 1
                    },
                    hover: {
                        'fill-opacity': 0.8
                    },
                    selected: {
                        fill: 'blue'
                    },
                    selectedHover: {}
                },
                focusOn: {x: 0.4, y: 0.6, scale: opts.scale},
                markerStyle: {
                    initial: {
                        fill: opts.markerColor,
                        stroke: opts.markerColor
                    }
                },
                onRegionLabelShow: function (e, el, code) {
                    if (series && series[code])
                        el.html(el.html() + ': ' + series[code] + ' visitors');
                },
                markers: markers,
                series: {
                    regions: [{
                        values: series,
                        scale: opts.scaleColors,
                        normalizeFunction: 'polynomial'
                    }]
                },
            });
        }
    };
});

 App.controller('LogsController', ["$scope", function ($scope) {
    /* controller code */
}]);
