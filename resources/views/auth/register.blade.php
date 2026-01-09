<?php
use App\User;
?>

@extends('layouts.app')
@section('title') register @parent @endsection

@section('content')

    <section class="container is-fluid">

        <article class="panel is-success">
            <p class="panel-heading">Register</p>
            @include('common.msgs')
            @include('common.errors')

            <section class="hero">
                <div class="hero-body">
                    <div class="content">

                        <div class="cell bs-errors" id="customErrors"></div>

                        <form id="registerForm" action="{{env("BASE_URL", "/")}}auth/register" method="POST" class="form-horizontal">
                            {{ csrf_field() }}

                            <input type="hidden" name="obfNumber" id="obfNumber" value="{{$obfNumber}}" />

                            <div class="field">
                                <label class="label">User name (any {{User::USR_MIN_LEN}} or more characters)</label>
                                <div class="control">
                                    <input class="input is-success" type="text" placeholder="Choose a unique user name" name="userName" id="userName" value="{{ $userName }}">
                                </div>
                            </div>

                            <div class="field">
                                <label class="label">Password (any {{User::PWD_MIN_LEN}} or more characters)</label>
                                <div class="control">
                                    <input class="input" type="password" name="password" id="password" placeholder="Password" />
                                </div>
                            </div>

                            <div class="field">
                                <label class="label">Confirm password</label>
                                <div class="control">
                                    <input class="input" type="password" name="confirm" id="confirm" placeholder="Repeat the password" />
                                </div>
                            </div>

                            <div class="field">
                                <label class="label">Password hint</label>
                                <div class="control">
                                    <input class="input" type="text" name="passwordHint" id="passwordHint" placeholder="Password hint in case you forget" />
                                </div>
                            </div>

                            <div class="field">
                                <label class="label">Are you human? Which number is this?</label>
                                <img src="/getCaptchaImage?n={{$obfNumber}}" width="15px" />
                                <div class="control">
                                    <input class="input" type="text" name="displayedNumber" id="displayedNumber" placeholder="Enter the displayed number" size="1" />
                                </div>
                            </div>

                            <div class="field is-grouped">
                                <div class="control">
                                    <button type="submit" class="button is-link" onclick="return validate()">Submit</button>
                                </div>
                                <div class="control">
                                    <button type="button" class="button is-link is-light" onclick="gotoUrl('registerForm', '{{env("BASE_URL", "/")}}home', 'GET')">Cancel</button>
                                </div>
                            </div>

                            <hr />
                            <div class="field">
                                No personal details are recorded on this website.
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
            let confirm = $('#confirm');
            let passwordHint = $('#passwordHint');
            let displayedNumber = $('#displayedNumber');

            let errors = [];
            let atLeastOne = false;

            if ('' == displayedNumber.val()) {
                errors[errors.length] = 'Please enter the displayed number';
                atLeastOne = true;
                displayedNumber.focus();
            }

            if ('' == passwordHint.val()) {
                errors[errors.length] = 'Please enter a password hint, which should help you remember later';
                atLeastOne = true;
                passwordHint.focus();
            }

            if ('' == confirm.val()) {
                errors[errors.length] = 'Please enter a password confirmation';
                atLeastOne = true;
                confirm.focus();
            }

            if ('' == password.val()) {
                errors[errors.length] = 'Please enter a password';
                atLeastOne = true;
                password.focus();
            } else {
                if (password.val().length < {{User::PWD_MIN_LEN}}) {
                    errors[errors.length] = 'The password is too short. It should be a minimum of {{User::PWD_MIN_LEN}} characters.';
                    atLeastOne = true;
                    password.focus();
                } else {
                    if ('' != confirm.val() && confirm.val() != password.val()) {
                        errors[errors.length] = 'The password and password confirmation do not match';
                        atLeastOne = true;
                        password.focus();
                    }
                }
            }

            if ('' == userName.val()) {
                errors[errors.length] = 'Please enter a user name';
                atLeastOne = true;
                userName.focus();
            } else {
                if (userName.val().length < {{User::USR_MIN_LEN}}) {
                    errors[errors.length] = 'The user name is too short. It should be a minimum of {{User::USR_MIN_LEN}} characters.';
                    atLeastOne = true;
                    userName.focus();
                }
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
                // Ok so far, check that the name is unique
                let data = {
                    userName: userName.val()
                };
                ajaxCall('/isUserNameUnique', JSON.stringify(data), uniqueNameCheck);
            }

            return false;
        }

        /**
         * Callback function check the result of the unique name check
         */
        function uniqueNameCheck(returnedData)
        {
            let message = '';
            if (null != returnedData) {
                if (true == returnedData.isUnique) {
                    // All good, submite details
                    let f = $('#registerForm');
                    f.submit();

                    return false;
                }
                // Name is not unique
                let userName = $('#userName');
                message = "The name '" + userName.val() + "' has already been taken.  Please choose another name.";
                userName.select().focus();

            } else {
                message = 'Error on call to server';
            }

            let ce = $('#customErrors');
            ce.html(message).show().delay(3000).fadeOut();
            return false;
        }
    </script>
@endsection
