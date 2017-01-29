angular.module('OfficialAlert.controllers', [])

.controller('NewsCtrl', function($scope, News, $rootScope, AjaxLoading, $ionicPopover, $ionicModal, $ionicTabsDelegate, $stateParams, Institutions, User, $ionicPopup, $ionicPlatform, $timeout) {

    $scope.page = 'tab-news-national'

    $rootScope.hideTabs = false
    $scope.searchText = {text:''}
    $scope.newsObj = [];

    $scope.updateNews = function(){
        var currentTab =  $ionicTabsDelegate.selectedIndex()
        $scope.currentCategoryName = getCategoryNameByTab(currentTab)
        $scope.currentCategoryId = getCategoryIdByTab(currentTab)
        $scope.currentCategoryTitle = getCategoryTitleByTab(currentTab)

        $scope.searchText.text = ''
        AjaxLoading.show()

        if(User.getToken()){
            News
                .getNewsFromAPI($scope.currentCategoryId)
                .then(function(news){
                    setTimeout(function(){
                        $scope.newsObj = news
                        AjaxLoading.hide()
                    }, 1000)
                });
        }
        else{
            $ionicPlatform.ready(function() {
                if (typeof PushNotification !== 'undefined') {
                    var push = PushNotification.init({
                        android: {
                            senderID: '995138220670'
                        },
                        ios: {
                            alert: true,
                            badge: true,
                            sound: 'false',
                            senderID: '995138220670'
                        },
                        windows: {}
                    });

                    push.on('registration', function(data) {
                        User
                            .getUserFromAPI(data.registrationId)
                            .then(function(user){
                                $scope.user = user
                                Institutions.getInstitutionsFromAPI(1)
                                Institutions.getInstitutionsFromAPI(2)
                                return News.getNewsFromAPI($scope.currentCategoryId)
                            }, function(err){
                                if(err.status == -1){
                                    showConnectionError("Nu aveți acces la internet");
                                }
                                else if(err.status == 500){
                                    showConnectionError("Eroare interna a serverului");
                                }
                                console.log(err)
                                
                            })
                            .then(function(news){
                                $scope.newsObj = news
                                AjaxLoading.hide()
                            })
                    });
                }
                else{
                    var token = '79d87f33-585e-4576-89ec-806c74bdf411'
                    User
                        .getUserFromAPI(token)
                        .then(function(user){
                            $scope.user = user
                            Institutions.getInstitutionsFromAPI(1)
                            Institutions.getInstitutionsFromAPI(2)
                            return News.getNewsFromAPI($scope.currentCategoryId)
                        }, function(err){
                            console.log(err)
                            showConnectionError()
                        })
                        .then(function(news){
                            $scope.newsObj = news
                            AjaxLoading.hide()
                        })
                }
            })
        }   
    }

    $scope.visitNews = function(news){
        news.visited = '1'
        News.visitNews([news.id])
    }

    $scope.visitAllNews = function(){
        var news = []
        for(var i=0; i<$scope.newsObj.length; i++){
            $scope.newsObj[i].visited = '1'
            news.push($scope.newsObj[i].id)
        }
        News.visitNews(news)
        $scope.popover.hide()
    }

    $scope.openNews = function(news){
        var source = news.url;
        window.open(source, '_system');
    }

    $ionicPopover.fromTemplateUrl('templates/settings-popup.html', {
        scope: $scope,
    }).then(function(popover) {
        $scope.popover = popover;
    });

    $ionicModal.fromTemplateUrl('templates/search-modal.html', {
        scope: $scope,
        animation: 'none'
    }).then(function(modal) {
        $scope.modal = modal
    });

    $scope.openSearch = function() {
        $scope.modal.show()
    };
    $scope.closeSearch = function() {
        $scope.modal.hide()
    };
    $scope.searchNews = function(){
        $scope.modal.hide()
    }

    $scope.changePushNotify = function(){
        User.changePushNotify($scope.user.push_notify);
    }



    // $scope.$on('$ionicView.enter', function(e) {
        
    // });
    
    $rootScope.$on('$stateChangeStart', function(event, toState, toParams, fromState, fromParams){
        if(toState.url == '/news/national' || toState.url == '/news/local'){
            $rootScope.hideTabs = false;
        }
        else{
            $rootScope.hideTabs = true;
        }
    })

    function getCategoryNameByTab(tab){
        if(tab == 0){
            return 'national'
        }
        else if(tab == 1){
            return 'local'
        }
    }

    function getCategoryTitleByTab(tab){
        if(tab == 0){
            return 'Noutăți naționale'
        }
        else if(tab == 1){
            return 'Noutăți locale'
        }
    }

    function getCategoryIdByTab(tab){
        if(tab == 0){
            return 1
        }
        else if(tab == 1){
            return 2
        }
    }

    function showConnectionError(message){
        var alertPopup = $ionicPopup.alert({
            title: 'Eroare ! ',
            template: message,
            okType: 'botton-stable'
        });
        alertPopup.then(function(res) {
            ionic.Platform.exitApp();
        });
    };
})

.controller('AboutUsCtrl', function($scope, $stateParams, $ionicHistory){
    $scope.goBack = function() {
        $ionicHistory.goBack();
    };
})

.controller('SettingsCtrl', function($scope, $stateParams, Institutions, $state, $ionicTabsDelegate){
    $scope.updateInstitutions = function(){
        var currentTab =  $ionicTabsDelegate.selectedIndex()
        $scope.institutions = Institutions.getInstitutions(getInstitutionIdByTab(currentTab))
    }

    $scope.changeInstitutionStatus = function(institution){
        Institutions.changeInstitutionStatus(institution)
    }

    function getInstitutionIdByTab(tabId){
        if(tabId == 0){
            return 1
        }
        else if(tabId == 1){
            return 2
        }
    }

    $scope.goBack = function() {
        $state.go('tab.news-national')
    };
})

.controller('NewsDetailCtrl', function($scope, $stateParams, News, $rootScope, $cordovaSocialSharing, $ionicPopup, $cordovaFacebook) {
    var selectedNews = News.getNewsById($stateParams.newsId)

    var passedMinutes = moment(moment()).diff(moment(selectedNews.published), 'minutes')
    if(passedMinutes == 0){
        selectedNews.passedTimeText = 'Acum'
    }
    else if(passedMinutes == 1){
        selectedNews.passedTimeText = '1 minută in urma'
    }
    else if(passedMinutes < 60){
        selectedNews.passedTimeText = passedMinutes + ' minute în urmă'
    }
    else if(passedMinutes >= 60 && passedMinutes <=120){
        selectedNews.passedTimeText = 'O oră în urmă'
    }
    else if(passedMinutes < 60 * 24){
        selectedNews.passedTimeText = Math.floor(passedMinutes / 60) + ' ore în urmă'
    }
    else{
        if(Math.floor(passedMinutes / (60 * 24)) == 1){
            selectedNews.passedTimeText = 'O zi în urmă'
        }
        else{
            selectedNews.passedTimeText = Math.floor(passedMinutes / (60 * 24)) + ' zile în urmă'
        }
    }

    $scope.news = selectedNews
    News.setSelectedNews(selectedNews)

    $scope.doFacebookShare = function(news){
        $cordovaFacebook.showDialog({
            method: "share",
            href: news.url,
            share_feedWeb: true, // iOS only
        }, function(){

        }, function(err){
            $ionicPopup.alert({
                title: 'Eroare ! ',
                template: "Nu aveti instalata aplicatia Facebook !",
                okType: 'botton-stable'
            });
        })
    }


    $scope.doTwitterShare = function(news){
        $cordovaSocialSharing
            .shareViaTwitter(null, null, news.url)
            .then(function(result) {
                
            }, function(err) {
                $ionicPopup.alert({
                    title: 'Eroare ! ',
                    template: "Nu aveti instalata aplicatia Twitter !",
                    okType: 'botton-stable'
                });
            });
    }

    $scope.doGoogleShare = function(news){
        $cordovaSocialSharing
            .shareViaEmail(null, null, news.url)
            .then(function(result) {

            }, function(err) {
                $ionicPopup.alert({
                    title: 'Eroare ! ',
                    template: "Nu aveti instalata aplicatia Email !",
                    okType: 'botton-stable'
                });
            });
    }

    $scope.openSource = function(news){
        var source = news.url;
        window.open(source, '_system');
    }
})

.controller('AccountCtrl', function($scope) {
    $scope.settings = {
        enableFriends: true
    };
});
