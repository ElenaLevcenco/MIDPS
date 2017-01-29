
angular.module('OfficialAlert', ['ionic', 'ngCordova', 'OfficialAlert.controllers', 'OfficialAlert.services'])

.run(function($ionicPlatform) {
    $ionicPlatform.ready(function() {
        // Hide the accessory bar by default (remove this to show the accessory bar above the keyboard
        // for form inputs)
        if (window.cordova && window.cordova.plugins && window.cordova.plugins.Keyboard) {
            cordova.plugins.Keyboard.hideKeyboardAccessoryBar(true);
            cordova.plugins.Keyboard.disableScroll(true);

        }
        if (window.StatusBar) {
            // org.apache.cordova.statusbar required
            StatusBar.styleDefault();
        }
});
})

.run(function($ionicPlatform) {
    $ionicPlatform.ready(function() {
        setTimeout(function() {
			try {
            navigator.splashscreen.hide();
			} catch (err) {};
        }, 3000);
    });
})

.config(function($ionicConfigProvider) {
    if(!ionic.Platform.isIOS()){
        $ionicConfigProvider.views.transition('none');
    }
})

.config(function($stateProvider, $urlRouterProvider) {

    // Ionic uses AngularUI Router which uses the concept of states
    // Learn more here: https://github.com/angular-ui/ui-router
    // Set up the various states which the app can be in.
    // Each state's controller can be found in controllers.js

    $stateProvider

    // setup an abstract state for the tabs directive
    .state('tab', {
        url: '/tab',
        abstract: true,
        templateUrl: 'templates/tabs.html'
    })

    .state('tab.news-national', {
        url: '/news/national',
        views: {
            'tab-news-national': {
                templateUrl: 'templates/news.html'
            }
        }
    })

    .state('tab.news-local', {
        url: '/news/local',
        views: {
            'tab-news-local': {
                templateUrl: 'templates/news.html'
            }
        }
    })

    .state('tab.news-detail-national', {
        url: '/news/national/:newsId',
        views: {
            'tab-news-national': {
                templateUrl: 'templates/news-detail.html',
                controller: 'NewsDetailCtrl'
            }
        }
    })

    .state('tab.news-detail-local', {
        url: '/news/local/:newsId',
        views: {
            'tab-news-local': {
                templateUrl: 'templates/news-detail.html',
                controller: 'NewsDetailCtrl'
            }
        }
    })

    .state('about', {
        url: '/about-us',
        templateUrl: 'templates/about-us.html',
        controller: 'AboutUsCtrl'
    })


    .state('settings', {
        url: '/settings',
        abstract: true,
        templateUrl: 'templates/settings-tabs.html'
    })

    .state('settings.national', {
        url: '/national',
        views: {
            'tab-settings-national': {
                templateUrl: 'templates/settings.html'
            }
        }
    })

    .state('settings.local', {
        url: '/local',
        views: {
            'tab-settings-local': {
                templateUrl: 'templates/settings.html'
            }
        }
    })

    // if none of the above states are matched, use this as the fallback
    $urlRouterProvider.otherwise('/tab/news/national');

});
