<?php
use App\User;
?>

@extends('layouts.app')
@section('title') edit user @parent @endsection

@section('content')

    <div class="container is-fluid">

        <article class="panel is-success">
            <p class="panel-heading">@if (isset($user->id)){{'Edit User'}}@else{{'Add User'}}@endif</p>
            @include('common.msgs')
            @include('common.errors')

            <form id="userForm" action="" method="GET" class="form-horizontal">
                {{ csrf_field() }}

                <input type="hidden" name="userId" id="userId" value="{{$user->id}}" />
                <input type="hidden" name="mode" id="mode" value="{{$mode}}" />

                <table class="table is-bordered is-striped bs-form-table">
                    <tbody>
                        <tr class="">
                            <td class="cell bs-section-title" colspan="2">
                                <div class="cell bs-errors" id="customErrors"></div>
                            </td>
                        </tr>
                        <tr class="">
                            <td class="cell bs-section-title">
                                Status:
                            </td>
                            <td class="cell bs-status" id="userStatus">
                                @if (isset($user->id))
                                    Edit
                                @else
                                    New
                                @endif
                            </td>
                        </tr>
                        <tr class="">
                            <td class="cell bs-section-title">
                                User name (any {{User::USR_MIN_LEN}} or more characters):
                            </td>
                            <td class="cell">
                                <input type="text" id="userName" name="userName" value="@if (isset($user->id)){{$user->name}}@endif" />
                            </td>
                        </tr>
                        <tr class="">
                            <td class="cell bs-section-title">
                                New password (leave blank or any {{User::PWD_MIN_LEN}} or more characters):
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
                        <tr class="">
                            <td class="cell bs-section-title">
                                Games played:
                            </td>
                            <td class="cell">
                                <input type="text" id="games_played" name="games_played" value="@if (isset($user->id)){{$user->games_played}}@endif" />
                            </td>
                        </tr>
                        <tr class="">
                            <td class="cell bs-section-title">
                                Vessels destroyed:
                            </td>
                            <td class="cell">
                                <input type="text" id="vessels_destroyed" name="vessels_destroyed" value="@if (isset($user->id)){{$user->vessels_destroyed}}@endif" />
                            </td>
                        </tr>
                        <tr class="">
                            <td class="cell bs-section-title">
                                Points scored:
                            </td>
                            <td class="cell">
                                <input type="text" id="points_scored" name="points_scored" value="@if (isset($user->id)){{$user->points_scored}}@endif" />
                            </td>
                        </tr>

                        <tr class="">
                            <td class="cell" colspan="2">
                                <input class="button is-pulled-right mr-6" type="submit" value="Submit input" onclick="return submitRequest();" />
                                <input class="button is-pulled-right mr-6" type="submit" value="Cancel" onclick="return cancelRequest();" />
                            </td>
                        </tr>
                    </tbody>
                </table>
            </form>

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
            let passwordHint = $('#passwordHint');
            let password = $('#password');
            let repeat = $('#repeat');

            let errors = [];
            let atLeastOne = false;
            // NB Validate in reverse order so we focus on the first one, lower down
            if ('' == passwordHint.val()) {
                errors[errors.length] = 'Please enter a password hint for this user';
                atLeastOne = true;
                passwordHint.focus();
            }
            if ('{{$mode}}' == 'add' && '' == password.val()) {
                errors[errors.length] = 'Password is required for a new user';
                atLeastOne = true;
                password.focus();
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

            if ('' == userName.val()) {
                errors[errors.length] = 'Please enter a name for this user';
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
                // Again in reverse order so the messages are in sync with the focus
                for (let i=(errors.length - 1); i>=0; i--) {
                    errMsgs += (sep + errors[i]);
                    sep = '<br />';
                }
                $('#customErrors').html(errMsgs).show().delay(3000).fadeOut();
                return false;
            }

            f.attr('method', 'POST');
            f.attr('action', '{{env("BASE_URL", "/")}}admin/updateUser');
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
            f.attr('action', '{{env("BASE_URL", "/")}}admin/users');
            f.submit();
            return false;
        }

        $(document).ready( function()
        {

        });
    </script>
@endsection