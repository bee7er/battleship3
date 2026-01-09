@extends('layouts.app')
@section('title') leaderboard @parent @endsection

@section('content')

    <div class="container is-fluid">

        <article class="panel is-success">
            <p class="panel-heading">Leaderboard</p>
            @include('common.msgs')
            @include('common.errors')

            <div class="bs-add-link is-pulled-right" title="Add a new game">
                <a class="bs-games-button" href="javascript: location.reload();">Refresh</a>
            </div>

            <form id="gamesForm" action="{{env("BASE_URL", "/")}}" method="GET" class="form-horizontal">

                <table class="table is-fullwidth is-bordered is-striped">
                    <thead>
                    <tr>
                        <th>Name</th>
                        <th>Games Played</th>
                        <th>Games Won</th>
                        <th>Vessels Destroyed</th>
                        <th>Points Scored</th>
                        <th>Action</th>
                    </tr>
                    </thead>
                    <tfoot>
                    <tr>
                        <th>Name</th>
                        <th>Games Played</th>
                        <th>Games Won</th>
                        <th>Vessels Destroyed</th>
                        <th>Points Scored</th>
                        <th>Action</th>
                    </tr>
                    </tfoot>
                    <tbody>

                    <?php
                        $first = 'bs-leaderboard-first';
                        $second = 'bs-leaderboard-second';
                        $third = 'bs-leaderboard-third';
                    ?>
                    @if (isset($users) && $users->count() > 0)
                        @foreach($users as $user)
                            <?php
                                    $class = '';
                                    if ($first) { $class = $first; $first = null; }
                                    elseif ($second) { $class = $second; $second = null; }
                                    elseif ($third) { $class = $third; $third = null; }
                            ?>
                            <tr class="{{$class}}">
                                <td>{{$user->name}}</td>
                                <td>{{$user->games_played}}</td>
                                <td>{{$user->wins}}</td>
                                <td>{{$user->vessels_destroyed}}</td>
                                <td>{{$user->points_scored}}</td>
                                <td><div title="User profile"><a class="bs-games-button" href="javascript: location.href='/profile?userId={{$user->id}}&from=lb'">Profile</a></div></td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td class="bs-no-data" colspan="99">No users found</td>
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
        $(document).ready( function()
        {

        });
    </script>
@endsection
