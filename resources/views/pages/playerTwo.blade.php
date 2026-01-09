@extends('layouts.app')
@section('title') player two @parent @endsection

@section('content')

    <section class="container is-fluid">

        <article class="panel is-success">
            <p class="panel-heading">Welcome Player 2</p>
            @include('common.msgs')
            @include('common.errors')

            <section class="hero">
                <div class="hero-body">
                    <div class="content">
                        <h2 class="title is-4">You have an Invitation to Play</h2>
                        <p>Someone has invited you to engage in a sea battle.</p>
                        <p>If you have registered and played before please <a class="" href="{{url('auth/login')}}">Login</a>.</p>
                        <p>If this is your first time, please <a class="" href="{{url('auth/register')}}">Register</a>. No personal details are required to register.</p>
                        <p>You will be redirected to plot your fleet vessels on the play grid.</p>
                        <p>For more details on setting up your fleet and playing the game go to the <a class="" href="{{url('/about')}}">About</a> page.</p>
                    </div>
                </div>
            </section>
        </article>
    </section>

@endsection

