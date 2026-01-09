<?php
use App\User;
?>

@extends('layouts.app')
@section('title') profile @parent @endsection

@section('content')

    <div class="container is-fluid">
        <article class="panel is-success">
            <p class="panel-heading">Your Profile</p>
            @include('common.msgs')
            @include('common.errors')

            <section class="hero">
                <div class="hero-body">
                    <div class="content">

                        <form id="userForm" action="{{env("BASE_URL", "/")}}updateUser" method="POST" class="form-horizontal">
                            {{ csrf_field() }}

                            <input type="hidden" name="userId" id="userId" value="{{$user->id}}" />

                            <table class="table is-bordered is-striped bs-form-table">
                                <tbody>
                                <tr class="">
                                    <td class="cell bs-section-title" colspan="2">
                                        <div class="cell bs-errors" id="customErrors"></div>
                                    </td>
                                </tr>
                                <tr class="">
                                    <td class="cell bs-section-title">
                                        Name:
                                    </td>
                                    <td class="cell">
                                        {{$user->name}}
                                    </td>
                                </tr>
                                @if ($loggedInUser->id == $user->id)
                                    <tr class="">
                                        <td class="cell bs-section-title">
                                            New password (or leave blank):
                                        </td>
                                        <td class="cell">
                                            <input type="text" id="password" name="password" value="" />
                                        </td>
                                    </tr>
                                    <tr class="">
                                        <td class="cell bs-section-title">
                                            Repeat password (confirm or leave blank):
                                        </td>
                                        <td class="cell">
                                            <input type="text" id="repeat" name="repeat" value="" />
                                        </td>
                                    </tr>
                                    <tr class="">
                                        <td class="cell bs-section-title">
                                            Password hint:
                                        </td>
                                        <td class="cell">
                                            <input type="text" id="passwordHint" name="passwordHint" value="{{$user->password_hint}}" />
                                        </td>
                                    </tr>
                                @endif
                                <tr class="">
                                    <td class="cell bs-section-title">
                                        Games played:
                                    </td>
                                    <td class="cell">
                                        {{$user->games_played}}
                                    </td>
                                </tr>
                                <tr class="">
                                    <td class="cell bs-section-title">
                                        Games won:
                                    </td>
                                    <td class="cell">
                                        {{$user->wins}}
                                    </td>
                                </tr>
                                <tr class="">
                                    <td class="cell bs-section-title">
                                        Vessels destroyed:
                                    </td>
                                    <td class="cell">
                                        {{$user->vessels_destroyed}}
                                    </td>
                                </tr>
                                <tr class="">
                                    <td class="cell bs-section-title">
                                        Points scored:
                                    </td>
                                    <td class="cell">
                                        {{$user->points_scored}}
                                    </td>
                                </tr>

                                <tr class="">
                                    <td class="cell" colspan="2">
                                        <input class="button is-link mr-6" type="submit" value="Submit" onclick="return submitRequest();" />
                                        <input class="button is-link is-light mr-6" type="submit" value="Cancel" onclick="return cancelRequest();" />
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </form>

                    </div>
                </div>
            </section>

        </article>

    </div>

@endsection

@section('page-scripts')
    <script type="text/javascript">
        /**
         * Validate the form and submit the add request
         */
        function submitRequest()
        {
            let f = $('#userForm');
            let userName = $('#userName');
            let errors = [];
            let atLeastOne = false;

            @if ($loggedInUser->id == $user->id)
                let password = $('#password');
                let repeat = $('#repeat');
                let passwordHint = $('#passwordHint');

                // NB Validate in reverse order so we focus on the first one, lower down
                if ('' == passwordHint.val()) {
                    errors[errors.length] = 'Please enter a password hint';
                    atLeastOne = true;
                    passwordHint.focus();
                }
                if ('' != password.val() && password.val().length < {{User::PWD_MIN_LEN}}) {
                    errors[errors.length] = 'The password is too short. It should be a minimum of {{User::PWD_MIN_LEN}} characters.';
                    atLeastOne = true;
                    password.focus();
                }
                if (('' != password.val() && '' == repeat.val())
                        || ('' == password.val() && '' != repeat.val())) {
                    errors[errors.length] = 'When entering a password, then both password and repeated password are needed';
                    atLeastOne = true;
                    password.focus();
                }
                if (('' != password.val() && '' != repeat.val())
                        && (password.val() != repeat.val())) {
                    errors[errors.length] = 'The password and repeated password do not match';
                    atLeastOne = true;
                    password.focus();
                }
            @endif

            if (atLeastOne) {
                let errMsgs = sep = "";
                // Again in reverse order so the messages are in sync with the focus
                for (let i=(errors.length - 1); i>=0; i--) {
                    errMsgs += (sep + errors[i]);
                    sep = '<br />';
                }
                $('#customErrors').html(errMsgs).show().delay(3000).fadeOut();
                return false;
            }

            f.attr('method', 'POST');
            f.attr('action', '{{env("BASE_URL", "/")}}updateProfile?from={{$from}}');
            f.submit();
            return false;
        }

        /**
         * Cancel request
         */
        function cancelRequest()
        {
            let f = $('#userForm');
            f.attr('method', 'GET');
            f.attr('action', '{{env("BASE_URL", "/")}}{{$from=="lb" ? "leaderboard": "home"}}');
            f.submit();
            return false;
        }

        $(document).ready( function()
        {

        });
    </script>
@endsection