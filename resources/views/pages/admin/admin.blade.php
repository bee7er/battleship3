@extends('layouts.app')
@section('title') admin @parent @endsection

@section('content')

    <section class="container is-fluid">

        <article class="panel is-success">
            <p class="panel-heading">Admin Dashboard</p>
            @include('common.msgs')
            @include('common.errors')

            <section class="hero">
                <div class="hero-body">
                    <div class="content">
                        <form id="adminForm" action="" method="GET" class="form-horizontal">
                            {{ csrf_field() }}
                            <div class=""> <a href="javascript: workWithUsers();">Work with Users</a></div>
                            <div class=""> <a href="javascript: workWithFleetTemplates();">Work with Fleet Templates</a></div>
                            <div class=""> <a href="javascript: workWithMessageTexts();">Work with Message Texts</a></div>
                            <div class=""> <a href="javascript: workWithVessels();">Work with Vessels</a></div>
                            <div class=""> <a href="javascript: workWithGames();">Work with Games</a></div>
                        </form>
                    </div>
                </div>
            </section>
        </article>
    </section>

@endsection

@section('page-scripts')
    <script type="text/javascript">
        /**
         * Add or change users
         */
        function workWithUsers()
        {
            let homeForm = $('#adminForm');
            homeForm.attr("action", "{{ url('admin/users')  }}");
            homeForm.submit();
        }

        /**
         * Add or change fleet templates
         */
        function workWithFleetTemplates()
        {
            let homeForm = $('#adminForm');
            homeForm.attr("action", "{{ url('admin/fleetTemplates')  }}");
            homeForm.submit();
        }

        /**
         * Add or change message texts
         */
        function workWithMessageTexts()
        {
            let homeForm = $('#adminForm');
            homeForm.attr("action", "{{ url('admin/messageTexts')  }}");
            homeForm.submit();
        }

        /**
         * Add or change vessels
         */
        function workWithVessels()
        {
            let homeForm = $('#adminForm');
            homeForm.attr("action", "{{ url('admin/vessels')  }}");
            homeForm.submit();
        }

        /**
         * Add or change games
         */
        function workWithGames()
        {
            let homeForm = $('#adminForm');
            homeForm.attr("action", "{{ url('admin/games')  }}");
            homeForm.submit();
        }
    </script>
@endsection
