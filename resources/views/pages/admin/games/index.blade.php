<?php
use App\Game;
?>

@extends('layouts.app')
@section('title') games @parent @endsection

@section('content')

    <div class="container is-fluid">

        <article class="panel is-success">
            <p class="panel-heading">Games</p>
            @include('common.msgs')
            @include('common.errors')

            <div class="bs-add-link is-pulled-right" title="Add a new game">
                <a class="bs-admin-button" href="javascript: location.reload();">Refresh</a><a class="bs-admin-button" href="javascript: gotoAddGame();">Add Game</a><a class="bs-admin-button" href="javascript: gotoDashboard();">Dashboard</a>
            </div>

            <form id="gamesForm" action="{{env("BASE_URL", "/")}}admin/editGame" method="GET" class="form-horizontal">
                <input type="hidden" name="gameId" id="gameId" value="" />

                <table class="table is-bordered is-striped bs-form-table">
                    <thead>
                    <tr>
                        <th>Id</th>
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
                        <th>Id</th>
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
                                <td>{{$game->id}}</td>
                                <td>{{$game->name}}</td>
                                <td>{{$game->player_one_name}} {{($game->player_one_id == $game->winner_id ? '*': '')}}</td>
                                <td>{{$game->player_two_name}} {{($game->player_two_id == $game->winner_id ? '*': '')}}</td>
                                <td>{{$game->status}}</td>
                                <td>{{getFormattedDate($game->started_at)}}</td>
                                <td>{{getFormattedDate($game->ended_at)}}</td>
                                <td>
                                    <a class="bs-games-button" href="javascript: gotoEditGame({{$game->id}})">Edit</a><a class="bs-games-button" href="javascript: gotoDeleteGame({{$game->id}})">Delete</a><a class="bs-games-button" href="javascript: gotoUndeleteGame({{$game->id}})">Undelete</a>
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td class="bs-no-data" colspan="99">There are no games</td>
                        </tr>
                    @endif
                    </tbody>
                </table>
            </form>

        </article>
    </div>

@endsection

@section('page-scripts')
    <script type="text/javascript">
        /**
         * Add a new game
         */
        function gotoDashboard()
        {
            let f = $('#gamesForm');
            f.attr('action', '{{env("BASE_URL", "/")}}admin/dashboard');
            f.submit();
            return false;
        }

        /**
         * Add a new game
         */
        function gotoAddGame()
        {
            let f = $('#gamesForm');
            f.attr('action', '{{env("BASE_URL", "/")}}admin/addGame');
            f.submit();
            return false;
        }

        /**
         * Game action edit
         */
        function gotoEditGame(gameId) {
            let f = $('#gamesForm');
            let u = $('#gameId');
            u.val(gameId);
            f.attr('action', '{{env("BASE_URL", "/")}}admin/editGame');
            f.submit();
            return false;
        }

        /**
         * Game action delete
         */
        function gotoDeleteGame(gameId) {
            let f = $('#gamesForm');
            let u = $('#gameId');
            u.val(gameId);
            f.attr('action', '{{env("BASE_URL", "/")}}admin/deleteGame');
            f.submit();
            return false;
        }

        /**
         * Game action undelete
         */
        function gotoUndeleteGame(gameId) {
            let f = $('#gamesForm');
            let u = $('#gameId');
            u.val(gameId);
            f.attr('action', '{{env("BASE_URL", "/")}}admin/undeleteGame');
            f.submit();
            return false;
        }

        $(document).ready( function()
        {

        });
    </script>
@endsection
