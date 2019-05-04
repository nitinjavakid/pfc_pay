<html lang="{{ app()->getLocale() }}">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <!-- CSRF Token -->
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Styles -->
        <link href="{{ asset('css/app.css') }}" rel="stylesheet">
        <style>
          pre {
                white-space: pre-wrap;
          }
        </style>
        <script>
           var _registration = null;
           function registerServiceWorker() {
             return navigator.serviceWorker.register('/js/service-worker.js')
                .then(function(registration) {
                    console.log('Service worker successfully registered.');
                    _registration = registration;
                    return registration;
               })
               .catch(function(err) {
                    console.error('Unable to register service worker.', err);
               });
           }

           function askPermission() {
              return new Promise(function(resolve, reject) {
                 const permissionResult = Notification.requestPermission(function(result) {
                    resolve(result);
                 });
                 if (permissionResult) {
                    permissionResult.then(resolve, reject);
                 }
              })
              .then(function(permissionResult) {
                 if (permissionResult !== 'granted') {
                    throw new Error('We weren\'t granted permission.');
                 }
                 else{
                    subscribeUserToPush();
                 }
              });
           }

           function urlBase64ToUint8Array(base64String) {
              const padding = '='.repeat((4 - base64String.length % 4) % 4);
              const base64 = (base64String + padding)
                  .replace(/\-/g, '+')
                  .replace(/_/g, '/');
              const rawData = window.atob(base64);
              const outputArray = new Uint8Array(rawData.length);
              for (let i = 0; i < rawData.length; ++i) {
                 outputArray[i] = rawData.charCodeAt(i);
              }
              return outputArray;
           }

           function getSWRegistration(){
              var promise = new Promise(function(resolve, reject) {
                 // do a thing, possibly async, then…
                 if (_registration != null) {
                    resolve(_registration);
                 }
                 else {
                    reject(Error("It broke"));
                 }
              });
              return promise;
           }

           function unsubscribeUserToPush() {
               getSWRegistration()
               .then(function(registration) {
                  registration.pushManager.getSubscription()
                  .then(function(subscription) {
                     fetch('/notification/delete-subscription', {
                        method: 'POST',
                        headers: {
                           'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                           endpoint: subscription.endpoint
                        })
                     })
                     .then(function(response) {
                        if(response.ok) {
                           alert("Unsubscription successful");
                        } else {
                           alert("Unsubscription unsuccessful");
                        }
                     });
                  });
               });
           }

           function subscribeUserToPush() {
              getSWRegistration()
              .then(function(registration) {
                 console.log(registration);
                 const subscribeOptions = {
                    userVisibleOnly: true,
                    applicationServerKey: urlBase64ToUint8Array(
                        "{{env('VAPID_PUBLIC_KEY')}}"
                    )
                 };
                 return registration.pushManager.subscribe(subscribeOptions);
              })
              .then(function(pushSubscription) {
                 console.log('Received PushSubscription: ', JSON.stringify(pushSubscription));
                 sendSubscriptionToBackEnd(pushSubscription);
                 return pushSubscription;
              });
           }

           function sendSubscriptionToBackEnd(subscription) {
              return fetch('/notification/save-subscription', {
                 method: 'POST',
                 headers: {
                    'Content-Type': 'application/json'
                 },
                 body: JSON.stringify(subscription)
              })
              .then(function(response) {
                 if (response.ok) {
                    alert("Subscription successful");
                 } else {
                    alert("Subscription unsuccessful");
                 }
              });
           }

           registerServiceWorker();
        </script>
    </head>
    <body>
    <div id="app">
        <nav class="navbar navbar-default navbar-static-top">
            <div class="container">
                <div class="navbar-header">

                    <!-- Collapsed Hamburger -->
                    <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#app-navbar-collapse" aria-expanded="false">
                        <span class="sr-only">Toggle Navigation</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>

                    <!-- Branding Image -->
                    <a class="navbar-brand" href="{{ url('/') }}">
                        {{ config('app.name', 'Laravel') }}
                    </a>
                </div>

                <div class="collapse navbar-collapse" id="app-navbar-collapse">
                    <ul class="nav navbar-nav">
                        <li><a href="{{ route('events.index') }}">Events</a></li>
                        <li><a href="{{ route('attendees.index') }}">Attendees</a></li>
                        <li><a href="{{ route('reports.index') }}">Reports</a></li>
                    </ul>
                    <!-- Right Side Of Navbar -->
                    <ul class="nav navbar-nav navbar-right">
                        @guest
                            <li><a href="{{ route('login', 'meetup') }}">Login</a></li>
                        @else
                            <li class="dropdown">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false" aria-haspopup="true" v-pre>
                                    {{ Auth::user()->name }} <span class="caret"></span>
                                </a>

                                <ul class="dropdown-menu">
                                    <li>
                                        <a href="#"
                                            onclick="askPermission()">
                                            Subscribe to notification
                                        </a>
                                        <a href="#"
                                            onclick="unsubscribeUserToPush()">
                                            Unsubscribe to notification
                                        </a>
                                        <a href="{{ route('logout') }}"
                                            onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                            Logout
                                        </a>

                                        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                            {{ csrf_field() }}
                                        </form>
                                    </li>
                                </ul>
                            </li>
                        @endguest
                    </ul>
                </div>
            </div>
        </nav>

        @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif
        <div class="container">
            @yield('content')
        </div>
    </div>
    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}"></script>
    </body>
</html>
