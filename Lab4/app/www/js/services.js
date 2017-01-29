
angular.module('OfficialAlert.services', [])

.factory('News', function($q, $http, User, $httpParamSerializer, UrlHelper) {
    var news = []
    var selectedNews
    var currentType

    return {
        getNews: function(){
            return news
        },
        getNewsFromAPI: function(type){
            if(type){
                currentType = type
            }
            else{
                type = currentType
            }
            var deferred = $q.defer()
            $http({
                url: UrlHelper.url('user/articles?userToken='+User.getToken()+'&type='+type),
                method: "GET",
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded' ,
                    'Authorization': 'Basic b2ZmaWNpYWxAdGVzdC5jb206YXBpT3x8aWNpYWw=',
                }
            }).then(function(data){
                news = data.data.data
                deferred.resolve(news)
            })

            return deferred.promise
        },
        getNewsById: function(id){
            for(var i=0; i<news.length; i++){
                if(news[i].id == id){
                    return news[i]
                }
            }
        },
        visitNews: function(news) {
            var data = $httpParamSerializer({'articles': '['+news.join()+']'})
            return $http({
                    url: UrlHelper.url('user/articles?userToken='+User.getToken()),
                    method: "PUT",
                    data: data,
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded' ,
                        'Authorization': 'Basic b2ZmaWNpYWxAdGVzdC5jb206YXBpT3x8aWNpYWw='
                    }
            })
        },
        setSelectedNews: function(news){
            selectedNews = news
        },
        getSelectedNews: function(){
            return selectedNews
        },
        getCurrentType: function(){
            return currentType
        }
    }
})

.factory('Institutions', function($http, $q, $httpParamSerializer, User, UrlHelper) {
    var institutions = {}
    return {
        getInstitutionsFromAPI: function(type){
            //    Create a deferred operation.
            var deferred = $q.defer()
            
            //    If we already have the name, we can resolve the promise.
            $http({
                url: UrlHelper.url('dashboard/institutions?type='+type+'&userToken='+User.getToken()),
                method: "GET",
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded' ,
                    'Authorization': 'Basic b2ZmaWNpYWxAdGVzdC5jb206YXBpT3x8aWNpYWw='
                }
            })
            .then(function(data) {
                institutions[type] = data.data.data
                console.log(institutions[type])
                deferred.resolve(institutions[type])                
            });
            
            //    Now return the promise.
            return deferred.promise;
        },
        getInstitutions: function(type){
            return institutions[type]
        },
        changeInstitutionStatus: function(institution) {
            return $http({
                url: UrlHelper.url('user/institution?userToken='+User.getToken()),
                method: "PUT",
                data: $httpParamSerializer({'institution': +institution.id, 'status': institution.selected}),
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded' ,
                    'Authorization': 'Basic b2ZmaWNpYWxAdGVzdC5jb206YXBpT3x8aWNpYWw='
                }
            });
        }
    };
})

.factory('User', function($http, $q, $httpParamSerializer, UrlHelper){
    var user = null
    var token = null
    return {
        getUserFromAPI: function(t){
            var deferred = $q.defer();
            $http({
                url: UrlHelper.url('user/login'),
                method: "POST",
                data: $httpParamSerializer({'token': t}),
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded' ,
                    'Authorization': 'Basic b2ZmaWNpYWxAdGVzdC5jb206YXBpT3x8aWNpYWw='
                }
            })
            .then(function(data){
                user = data.data.data[0]
                token = t
                deferred.resolve(user)
            }, function(err){
                deferred.reject(err)
            })
            return deferred.promise
        },
        getUser: function(){
            return user
        },
        getToken: function(){
            return token
        },
        changePushNotify: function(push){
            return $http({
                url: UrlHelper.url('user/push?userToken='+token),
                method: 'PUT',
                data: $httpParamSerializer({push: push}),
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded' ,
                    'Authorization': 'Basic b2ZmaWNpYWxAdGVzdC5jb206YXBpT3x8aWNpYWw='
                }
            });
        }
    }
})

.factory('AjaxLoading', function($ionicLoading){

    return {
        show: function() {
            $ionicLoading.show({
                template: '<ion-spinner icon="dots"></ion-spinner>'
            });
        },
        hide: function(){
            $ionicLoading.hide();
        }
    }
})

.factory('UrlHelper', function(){
    return {
        url: function(url){
            return 'http://officialalert.md/v1/'+url
        }
    }
})