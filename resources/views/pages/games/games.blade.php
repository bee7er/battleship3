<?php
use App\Game;
?>

@extends('layouts.app')
@section('title') my games @parent @endsection

@section('content')

    <div class="container is-fluid">

        <article class="panel is-success">
            <p class="panel-heading">My Games</p>
            @include('common.msgs')
            @include('common.errors')

            <div class="bs-add-link is-pulled-right" title="Add a new game">
                <a class="bs-games-button" href="javascript: location.reload();">Refresh</a><a class="bs-games-button" href="javascript: gotoAddGame()">Add Game</a>
            </div>

            <form id="gamesForm" action="{{env("BASE_URL", "/")}}editGame" method="GET" class="form-horizontal">
                <input type="hidden" name="gameId" id="gameId" value="" />

                <table class="table is-bordered is-striped bs-form-table">
                    <thead>
                    <tr>
                        <th>Game Name</th>
                        <th>Player 1</th>
                        <th>Player 2</th>
                        <th>Status</th>
                        <th>Started at</th>
                        <th>Ended at</th>
                        <th>Action</th>
                    </tr>
                    </thead>
                    <tfoot>
                    <tr>
                        <th>Game Name</th>
                        <th>Player 1</th>
                        <th>Player 2</th>
                        <th>Status</th>
                        <th>Started at</th>
                        <th>Ended at</th>
                        <th>Action</th>
                    </tr>
                    </tfoot>
                    <tbody>

                    @if (isset($games) && $games->count() > 0)
                        @foreach($games as $game)
                            <tr>
                                <td><a class="bs-games-button" href="javascript: gotoEdit({{$game->id}})">{{$game->name}}</a></td>
                                <td class="{{($game->player_one_id == $game->winner_id ? 'bs-winner': '')}}">{{$game->player_one_name}}</td>
                                <td class="{{($game->player_two_id && $game->player_two_id == $game->winner_id ? 'bs-winner': '')}}">{{$game->player_two_name ? : 'Not yet set'}}</td>
                                <td>{{$game->status}}</td>
                                <td>{{getFormattedDate($game->started_at)}}</td>
                                <td>{{getFormattedDate($game->ended_at)}}</td>
                                <td>
                                    @if ($game->status == Game::STATUS_DELETED)
                                        <div title="Visualize the game by replaying all the moves"><a class="bs-games-button" href="javascript: gotoReplay('{{$game->id}}')">Replay</a></div>
                                    @else
                                        @if ($game->status == Game::STATUS_EDIT
                                         || $game->status == Game::STATUS_WAITING)
                                            @if ($game->player_one_id == $userId)
                                                <div title="Edit the game grid"><a class="bs-games-button" href="javascript: gotoEditGrid({{$game->id}})">Edit Grid</a></div>
                                            @else
                                                {{--Current user is an opponent in this game--}}
                                                @if (isset($game->player_two_fleet))
                                                    <div title="Edit the game grid"><a class="bs-games-button" href="javascript: gotoEditGrid({{$game->id}})">Edit Grid</a></div>
                                                @else
                                                    <div title="Accept the game"><a class="bs-games-button" href="javascript: gotoAccept({{$game->id}})">Accept</a></div>
                                                @endif
                                            @endif
                                        @else
                                            @if ($game->status == Game::STATUS_READY || $game->status == Game::STATUS_ENGAGED)
                                                <div title="Start the game"><a class="bs-games-button" href="javascript: gotoEngage({{$game->id}})">Engage</a></div>
                                            @endif
                                        @endif
                                        @if ($game->player_one_id == $userId)
                                            <div title="Delete the game"><a class="bs-games-button" href="javascript: gotoDelete('{{$game->id}}', '{{$game->name}}')">Delete</a></div>
                                        @endif
                                        @if ($game->status == Game::STATUS_COMPLETED)
                                            <div title="Visualize the game by replaying all the moves"><a class="bs-games-button" href="javascript: gotoReplay('{{$game->id}}')">Replay</a></div>
                                        @endif
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td class="bs-no-data" colspan="99">You have not yet created any games</td>
                        </tr>
                    @endif
                    </tbody>
                </table>
                <div class="is-pulled-right mr-6 bs-show-deleted">
                    <label class="checkbox">
                        <input type="checkbox" class="" name="showDeletedGames" id="showDeletedGames" value="1" {{$showDeletedGames ? 'checked': ''}} onclick="gotoShowDeletedGames(this)" />
                        Show deleted games
                    </label>
                </div>
            </form>

        </article>
    </div>

@endsection

@section('page-scripts')
    <script type="text/javascript">
        /**
         * Add a new game
         */
        function gotoAddGame()
        {
            let f = $('#gamesForm');
            f.attr('action', '{{env("BASE_URL", "/")}}addGame');
            f.submit();
            return false;
        }
        /**
         * Engage in the game battle
         */
        function gotoEngage(gameId) {
            let f = $('#gamesForm');
            let h = $('#gameId');
            h.val(gameId);
            f.attr('action', '{{env("BASE_URL", "/")}}playGrid');
            f.submit();
            return false;
        }

        /**
         * Accept as an opponent to the requested game
         */
        function gotoAccept(gameId)
        {
            let f = $('#gamesForm');
            let h = $('#gameId');
            h.val(gameId);
            f.attr('action', '{{env("BASE_URL", "/")}}acceptGame');
            f.submit();
            return false;
        }

        /**
         * Edit the requested game
         */
        function gotoEdit(gameId)
        {
            let f = $('#gamesForm');
            let h = $('#gameId');
            h.val(gameId);
            f.attr('action', '{{env("BASE_URL", "/")}}editGame');
            f.submit();
            return false;
        }

        /**
         * Edit the requested game grid
         */
        function gotoEditGrid(gameId) {
            let f = $('#gamesForm');
            let h = $('#gameId');
            h.val(gameId);
            f.attr('action', '{{env("BASE_URL", "/")}}editGrid');
            f.submit();
            return false;
        }

        /**
         * Delete the game
         */
        function gotoDelete(gameId, gameName)
        {
            if (confirm("Are you sure you want to delete '" + gameName + "'")) {
                let f = $('#gamesForm');
                let h = $('#gameId');
                h.val(gameId);
                f.attr('action', '{{env("BASE_URL", "/")}}deleteGame');
                f.submit();
            }
            return false;
        }

        /**
         * Replay all the moves of the game
         */
        function gotoReplay(gameId)
        {
            let f = $('#gamesForm');
            let h = $('#gameId');
            h.val(gameId);
            f.attr('action', '{{env("BASE_URL", "/")}}replay');
            f.submit();
            return false;
        }

        /**
         * Reload games with showing deleted ones, or not
         */
        function gotoShowDeletedGames(elem)
        {
            let f = $('#gamesForm');
            let checked = '0';
            if ($(elem).is(':checked')) {
                checked = '1';
            }
            f.attr('action', '{{env("BASE_URL", "/")}}games?showDeletedGames=' + checked);
            f.submit();
            return false;
        }

        $(document).ready( function()
        {

        });
    </script>
@endsection
