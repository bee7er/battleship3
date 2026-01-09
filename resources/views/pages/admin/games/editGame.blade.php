<?php
use App\Game;
?>

@extends('layouts.app')
@section('title') edit game @parent @endsection

@section('content')

    <div class="container is-fluid">

        <article class="panel is-success">
            <p class="panel-heading">@if (isset($game->id)){{'Edit Game'}}@else{{'Add Game'}}@endif</p>
            @include('common.msgs')
            @include('common.errors')

            <form id="gameForm" action="{{env("BASE_URL", "/")}}updateGame" method="POST" class="form-horizontal">
                {{ csrf_field() }}

                <input type="hidden" name="gameId" id="gameId" value="{{$game->id}}" />

                <table class="table is-bordered is-striped bs-form-table">
                    <tbody>
                    <tr class="">
                        <td class="cell bs-section-title" colspan="2">
                            <div class="cell bs-errors" id="customErrors"></div>
                        </td>
                    </tr>
                    <tr class="">
                        <td class="cell bs-section-title">
                            Game status:
                        </td>
                        <td class="cell bs-status" id="gameStatus">
                            @if (isset($game->id))
                                {{$game->status}}
                            @else
                                New
                            @endif
                        </td>
                    </tr>
                    <tr class="">
                        <td class="cell bs-section-title">
                            Game name:
                        </td>
                        <td class="cell">
                            <input type="text" id="gameName" name="gameName" value="@if (isset($game->id)){{$game->name}}@endif" />
                        </td>
                    </tr>
                    <tr class="">
                        <td class="cell bs-section-title">
                            Status:
                        </td>
                        <td class="cell">
                            <select name="status" id="status" aria-label="Game status" class="bs-listbox">
                                <option value="" class="">Select status</option>
                                @if (isset($statuses) && count($statuses) > 0)
                                    @foreach($statuses as $status)
                                        <option value="{{$status}}" @if ($status == $game->status) {{'selected'}}@endif>{{$status}}</option>
                                    @endforeach
                                @endif
                            </select>
                        </td>
                    </tr>
                    <tr class="">
                        <td class="cell bs-section-title">
                            Player 1:
                        </td>
                        <td class="cell">
                            <select name="playerOneId" id="playerOneId" aria-label="Player 1" class="bs-listbox">
                                <option value="" class="">Select player 1</option>
                                @if (isset($users) && $users->count() > 0)
                                    @foreach($users as $user)
                                        <option value="{{$user->id}}" @if ($user->id == $game->player_one_id) {{'selected'}}@endif>{{$user->name}}</option>
                                    @endforeach
                                @endif
                            </select>
                        </td>
                    </tr>
                    <tr class="">
                        <td class="cell bs-section-title">
                            Player 2:
                        </td>
                        <td class="cell">
                            <select name="playerTwoId" id="playerTwoId" aria-label="Player 2" class="bs-listbox">
                                <option value="" class="">Select player two</option>
                                @if (isset($users) && $users->count() > 0)
                                    @foreach($users as $user)
                                        <option value="{{$user->id}}" @if ($user->id == $game->player_two_id) {{'selected'}}@endif>{{$user->name}}</option>
                                    @endforeach
                                @endif
                            </select>
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
            let f = $('#gameForm');
            let gameName = $('#gameName');
            let playerOneId = $('#playerOneId');
            let playerTwoId = $('#playerTwoId');
            let status = $('#status');

            let errors = [];
            let atLeastOne = false;
            if ('' == playerTwoId.val()) {
                errors[errors.length] = 'Please select player 2 for this game';
                atLeastOne = true;
                playerTwoId.focus();
            }
            if ('' == playerOneId.val()) {
                errors[errors.length] = 'Please select player 1 for this game';
                atLeastOne = true;
                playerOneId.focus();
            }
            if (playerTwoId.val() == playerOneId.val()) {
                errors[errors.length] = 'Players 1 and 2 cannot be the same user';
                atLeastOne = true;
                playerOneId.focus();
            }

            if ('' == status.val()) {
                errors[errors.length] = 'Please select a status for this game';
                atLeastOne = true;
                status.focus();
            }

            if ('' == gameName.val()) {
                errors[errors.length] = 'Please enter a name for this game';
                atLeastOne = true;
                gameName.focus();
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
            f.attr('action', '{{env("BASE_URL", "/")}}admin/updateGame');
            f.submit();
            return false;
        }

        /**
         * Cancel request
         */
        function cancelRequest()
        {
            let f = $('#gameForm');
            f.attr('method', 'GET');
            f.attr('action', '{{env("BASE_URL", "/")}}admin/games');
            f.submit();
            return false;
        }

        $(document).ready( function()
        {

        });
    </script>
@endsection