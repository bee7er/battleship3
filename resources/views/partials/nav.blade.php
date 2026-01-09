<nav class="navbar" role="navigation" aria-label="main navigation">
    <div class="navbar-brand">
        {{--<a href="{{config('app.base_url')}}images/battleships.jpeg" target="_self">--}}
            {{--<img class="bs-navbar-item" alt="" src="{{config('app.base_url')}}images/battleships.jpeg" width="100px" height="45px">--}}
        {{--</a>--}}

        <a role="button" class="navbar-burger" aria-label="menu" aria-expanded="false" data-target="navbarBasicExample">
            <span aria-hidden="true"></span>
            <span aria-hidden="true"></span>
            <span aria-hidden="true"></span>
            <span aria-hidden="true"></span>
        </a>
    </div>

    <div id="navbarBasicExample" class="navbar-menu">
        <div class="navbar-start">
            <a class="navbar-item {{(Request::is('/') ? ' is-selected' : '')}}" href="{{ url('') }}">
                Home
            </a>

            @if(Auth::check())
                <a class="navbar-item {{(Request::is('games') ? ' is-selected' : '')}}" href="{{ url('games') }}">
                    My Games
                </a>
                <a class="navbar-item {{(Request::is('leaderboard') ? ' is-selected' : '')}}" href="{{ url('leaderboard') }}">
                    Leaderboard
                </a>
            @endif

            <a class="navbar-item {{(Request::is('about') ? ' is-selected' : '')}}" href="{{ url('about') }}">
                About
            </a>
        </div>

        <div class="navbar-end">
            <div class="navbar-item">
                <div class="buttons">
                    @if (Auth::guest())
                        <a class="navbar-item {{(Request::is('auth/register') ? ' is-selected' : '')}}" href="{{url('auth/register')}}">
                            Register
                        </a>
                        <a class="navbar-item {{(Request::is('auth/login') ? ' is-selected' : '')}}" href="{{url('auth/login')}}">
                            Login
                        </a>
                    @else
                        <div class="navbar-item has-dropdown is-hoverable">
                            <a class="navbar-link">
                                Welcome {{ucfirst(Auth::user()->name)}}
                            </a>
                            <div class="navbar-dropdown">
                                @if(Auth::check())
                                    @if(Auth::user()->admin==1)
                                        <a class="navbar-item {{(Request::is('admin/dashboard') ? ' is-selected' : '')}}" href="{{ url('admin/dashboard') }}">
                                            Admin Dashboard
                                        </a>
                                    @endif
                                @endif
                                <a class="navbar-item {{(Request::is('profile') ? ' is-selected' : '')}}" href="{{ url('profile?&from=nv') }}">
                                    Your Profile
                                </a>
                                <hr class="navbar-divider">
                                <a class="navbar-item" href="{{ url('auth/logout') }}">
                                    Logout
                                </a>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</nav>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        // Get all "navbar-burger" elements
        const $navbarBurgers = Array.prototype.slice.call(document.querySelectorAll('.navbar-burger'), 0);
        // Add a click event on each of them
        $navbarBurgers.forEach( el => {
            el.addEventListener('click', () => {
                // Get the target from the "data-target" attribute
                const target = el.dataset.target;
                const $target = document.getElementById(target);
                // Toggle the "is-active" class on both the "navbar-burger" and the "navbar-menu"
                el.classList.toggle('is-active');
                $target.classList.toggle('is-active');
            });
        });
    });
</script>