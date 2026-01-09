<?php
use App\User;
?>

@extends('layouts.app')
@section('title') users @parent @endsection

@section('content')

    <div class="container is-fluid">

        <article class="panel is-success">
            <p class="panel-heading">Users</p>
            @include('common.msgs')
            @include('common.errors')

            <div class="bs-add-link is-pulled-right" title="Add a new user">
                <a class="bs-admin-button" href="javascript: location.reload();">Refresh</a><a class="bs-admin-button" href="javascript: gotoAddUser();">Add User</a><a class="bs-admin-button" href="javascript: gotoDashboard();">Dashboard</a>
            </div>

            <form id="usersForm" action="" method="GET" class="form-horizontal">
                <input type="hidden" name="userId" id="userId" value="" />

                <table class="table is-bordered is-striped bs-form-table">
                    <thead>
                    <tr>
                        <th>Id</th>
                        <th>User Name</th>
                        <th>Email</th>
                        <th>Games Played</th>
                        <th>Vessels Destroyed</th>
                        <th>Points Scored</th>
                        <th>Action</th>
                    </tr>
                    </thead>
                    <tfoot>
                    <tr>
                        <th>Id</th>
                        <th>User Name</th>
                        <th>Email</th>
                        <th>Games Played</th>
                        <th>Vessels Destroyed</th>
                        <th>Points Scored</th>
                        <th>Action</th>
                    </tr>
                    </tfoot>
                    <tbody>

                    @if (isset($users) && $users->count() > 0)
                        @foreach($users as $user)
                            <tr>
                                <td>{{$user->id}}</td>
                                <td>{{$user->name}}</td>
                                <td>{{$user->email}}</td>
                                <td>{{$user->games_played}}</td>
                                <td>{{$user->vessels_destroyed}}</td>
                                <td>{{$user->points_scored}}</td>
                                <td>
                                    <div title=""><a class="bs-games-button" href="javascript: gotoEditUser({{$user->id}})">Edit</a></div>
                                </td>
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
        /**
         * Add a new user
         */
        function gotoDashboard()
        {
            let f = $('#usersForm');
            f.attr('action', '{{env("BASE_URL", "/")}}admin/dashboard');
            f.submit();
            return false;
        }

        /**
         * Add a new user
         */
        function gotoAddUser()
        {
            let f = $('#usersForm');
            f.attr('action', '{{env("BASE_URL", "/")}}admin/addUser');
            f.submit();
            return false;
        }

        /**
         * User action
         */
        function gotoEditUser(userId) {
            let f = $('#usersForm');
            let u = $('#userId');
            u.val(userId);
            f.attr('action', '{{env("BASE_URL", "/")}}admin/editUser');
            f.submit();
            return false;
        }

        $(document).ready( function()
        {

        });
    </script>
@endsection
