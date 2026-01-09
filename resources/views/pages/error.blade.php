@extends('layouts.app')
@section('title') error @parent @endsection

@section('content')

    <div class="container is-fluid">
        <div class="notification">

            <section class="hero">
                <div class="hero-head">
                    <p class="title">Whoops Something Went Wrong!</p>
                </div>
                <div class="hero-body">
                    <form id="errorForm" action="" method="POST" class="form-horizontal">
                        {{ csrf_field() }}

                        <div class="field is-grouped">
                            <div class="control">
                                <p>Sorry, you need to be logged in to access that page</p>
                                <p>This may be due to your session having timed out after a period of inactivity</p>
                                <p>To renew the session just return to the <a class="bs-error-link" href="javascript: gotoUrl('errorForm', '{{env("BASE_URL", "/")}}home')">home page</a></p>
                                <p>You may have to login once more</p>
                            </div>
                        </div>

                        <div class="field is-grouped">
                            <div class="control">
                                <button class="button is-link is-light" onclick="gotoUrl('errorForm', '{{env("BASE_URL", "/")}}home')">Continue ...</button>
                            </div>
                        </div>

                    </form>
                </div>
            </section>
        </div>

    </div>

@endsection

