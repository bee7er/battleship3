@extends('layouts.app')
@section('title') home @parent @endsection

@section('content')

    <section class="container is-fluid">

        <article class="panel is-success">
            <p class="panel-heading">Sink My Boats</p>
            @include('common.msgs')
            @include('common.errors')
            <input type="hidden" id="userToken" value="{{$userToken}}" />

            <section class="hero">
                <div class="hero-body">
                    <div class="content">
                        <p class="home-text">The classical game of two battling fleets, now played across the web.</p>
                        <p class="home-text">You will find all the old favourites, aircraft carrier, battleship, destroyer and more.</p>
                        <p class="home-text">Create a game, plot where your fleet goes on a grid and send an invitation link to a friend.</p>
                        <p class="home-text">Once you've both plotted the locations of your fleets you can engage in battle.</p>
                        <p class="home-text">Hope you enjoy the challenge.</p>
                    </div>
                    <div class="content">
                        <p class="is-size-4">What You Have To Do</p>
                        <ol class="is-size-5">
                            <li>If this is your first visit then you must <span class="bs-text">Register</span>, with a simple name and password.</li>
                            <li>Once you've logged in, go to <span class="bs-text">My Games</span>.</li>
                            <li>Click on <span class="bs-text">Add Game</span>.</li>
                            <li>Click on <span class="bs-text">Edit Grid</span>.</li>
                            <li>Click on the  <span class="bs-text">Vessel Location Grid</span> to plot each vessel.</li>
                            <li>If you want to get the game to plot the vessels just click on the  <span class="bs-text">Go Random</span> button.</li>
                            <li>When you have finished plotting your vessels the game status will change to <span class="bs-text">Waiting</span>.</li>
                            <li>Invite a friend to join you in battle by sending the <span class="bs-text">Player 2</span> link to them.</li>
                        </ol>
                    </div>
                </div>
            </section>
        </article>
    </section>

@endsection

@section('page-scripts')
    <script type="text/javascript">
        $(document).ready( function()
        {
            setCookie('user_token', $('#userToken').val(), 1);
        });
    </script>
@endsection
