@extends('layouts.app')
@section('title') login @parent @endsection

@section('content')

    <section class="container is-fluid">

        <article class="panel is-success">
            <p class="panel-heading">Login</p>
            @include('common.msgs')
            @include('common.errors')

            <section class="hero">
                <div class="hero-body">
                    <div class="content">

                        <div class="cell bs-errors" id="customErrors"></div>

                        <form id="loginForm" action="{{env("BASE_URL", "/")}}auth/login" method="POST" class="form-horizontal">
                            {{ csrf_field() }}

                            <div class="field">
                                <label class="label">User name</label>
                                <div class="control">
                                    <input class="input is-success" type="text" placeholder="Enter your unique user name" name="userName" id="userName" value="{{ $userName }}">
                                </div>
                            </div>

                            <div class="field">
                                <label class="label">Password</label>
                                <div class="control">
                                    <input class="input" type="password" name="password" id="password" placeholder="Password" />
                                </div>
                            </div>

                            <div class="field">
                                <div class="control">
                                    <input type="checkbox" name="hint" id="hint" onclick="getPasswordHint(this)"> Give me a hint
                                </div>
                            </div>

                            <div class="field">
                                <div class="control">
                                    <input type="checkbox" name="remember" id="remember"> Remember Me
                                </div>
                            </div>

                            <div class="field is-grouped">
                                <div class="control">
                                    <button type="submit" class="button is-link" onclick="return validate()">Submit</button>
                                </div>
                                <div class="control">
                                    <button class="button is-link is-light" onclick="gotoUrl('loginForm', '{{env("BASE_URL", "/")}}home', 'GET')">Cancel</button>
                                </div>
                            </div>

                            <hr />
                            <div class="field">
                                <div class="control">
                                    Click <a class="" href="{{url('auth/register')}}">here to register</a>
                                </div>
                            </div>

                        </form>

                    </div>
                </div>
            </section>
        </article>
    </section>

@endsection

@section('page-scripts')
    <script type="text/javascript">
        /**
         * Validate the user register/login form and submit the request
         */
        function validate()
        {
            let userName = $('#userName');
            let password = $('#password');

            let errors = [];
            let atLeastOne = false;

            if ('' == password.val()) {
                errors[errors.length] = 'Please enter a password';
                atLeastOne = true;
                password.focus();
            }

            if ('' == userName.val()) {
                errors[errors.length] = 'Please enter a user name';
                atLeastOne = true;
                userName.focus();
            }

            if (atLeastOne) {
                let errMsgs = sep = "";
                for (let i=(errors.length - 1); i>=0; i--) {
                    errMsgs += (sep + errors[i]);
                    sep = '<br />';
                }
                let ce = $('#customErrors');
                ce.html(errMsgs).show().delay(3000).fadeOut();
                return false;
            }

            let f = $('#loginForm');
            f.submit();
            return false;
        }

        /**
         * Using the user name entered, retrieve and display the password hint
         */
        function getPasswordHint(elem)
        {
            if (!$(elem).is(':checked')) {
                return;
            }

            let userName = $('#userName');

            let errors = [];
            let atLeastOne = false;

            if ('' == userName.val()) {
                errors[errors.length] = 'Please enter a user name';
                atLeastOne = true;
                userName.focus();
            }

            if (atLeastOne) {
                let errMsgs = sep = "";
                for (let i=(errors.length - 1); i>=0; i--) {
                    errMsgs += (sep + errors[i]);
                    sep = '<br />';
                }
                let ce = $('#customErrors');
                ce.html(errMsgs).show().delay(3000).fadeOut();
                return false;
            } else {
                // Ok so far, go get the password hint
                let data = {
                    userName: userName.val()
                };
                ajaxCall('/getPasswordHint', JSON.stringify(data), setPasswordHint);
            }

            return false;
        }

        /**
         * Callback function to display the password hint
         */
        function setPasswordHint(returnedData)
        {
            let message = '';
            if (null != returnedData) {

                console.log(returnedData);

                if (null != returnedData.passwordHint) {
                    // All good, notify user of their passsword hint
                    let ce = $('#customErrors');
                    ce.html("Your password hint is '" + returnedData.passwordHint + "'.").show().delay(5000).fadeOut('slow', function() { $("#hint").prop('checked', false)});

                    return false;
                }
                // Something went wrong
                message = returnedData.error;

            } else {
                message = 'Error on call to server.  Please try again.';
            }

            let ce = $('#customErrors');
            ce.html(message).show().delay(3000).fadeOut();
            return false;
        }
    </script>
@endsection
