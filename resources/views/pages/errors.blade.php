@extends('layouts.app')
@section('title') errors @parent @endsection
@section('content')

    <section class="container is-fluid">

        <article class="panel is-success">
            <p class="panel-heading">There was a Problem</p>
            @include('common.msgs')
            @include('common.errors')

            <section class="hero">
                <div class="hero-body">&nbsp;</div>
            </section>
        </article>
    </section>

@endsection

