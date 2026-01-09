<?php
use App\Vessel;
?>

@extends('layouts.app')
@section('title') vessels @parent @endsection

@section('content')

    <div class="container is-fluid">

        <article class="panel is-success">
            <p class="panel-heading">Vessels</p>
            @include('common.msgs')
            @include('common.errors')

            <div class="bs-add-link is-pulled-right" title="Add a new vessel">
                <a class="bs-admin-button" href="javascript: location.reload();">Refresh</a><a class="bs-admin-button" href="javascript: gotoAddVessel();">Add Vessel</a><a class="bs-admin-button" href="javascript: gotoDashboard();">Dashboard</a>
            </div>

            <form id="vesselsForm" action="" method="GET" class="form-horizontal">
                <input type="hidden" name="vesselId" id="vesselId" value="" />

                <table class="table is-bordered is-striped bs-form-table">
                    <thead>
                    <tr>
                        <th>Id</th>
                        <th>Name</th>
                        <th>Length</th>
                        <th>Points</th>
                        <th>Action</th>
                    </tr>
                    </thead>
                    <tfoot>
                    <tr>
                        <th>Id</th>
                        <th>Name</th>
                        <th>Length</th>
                        <th>Points</th>
                        <th>Action</th>
                    </tr>
                    </tfoot>
                    <tbody>

                    @if (isset($vessels) && $vessels->count() > 0)
                        @foreach($vessels as $vessel)
                            <tr>
                                <td>{{$vessel->id}}</td>
                                <td>{{$vessel->name}}</td>
                                <td>{{$vessel->length}}</td>
                                <td>{{$vessel->points}}</td>
                                <td>
                                    <div title=""><a class="bs-games-button" href="javascript: gotoEditVessel({{$vessel->id}})">Edit</a></div>
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td class="bs-no-data" colspan="99">No vessels found</td>
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
         * Add a new vessel
         */
        function gotoDashboard()
        {
            let f = $('#vesselsForm');
            f.attr('action', '{{env("BASE_URL", "/")}}admin/dashboard');
            f.submit();
            return false;
        }

        /**
         * Add a new vessel
         */
        function gotoAddVessel()
        {
            alert('Since the vessel name is an enum, one cannot add a new vessel this way. Raw SQL is possible. Or a new migration.');
            return false;
        }

        /**
         * Vessel action
         */
        function gotoEditVessel(vesselId) {
            let f = $('#vesselsForm');
            let u = $('#vesselId');
            u.val(vesselId);
            f.attr('action', '{{env("BASE_URL", "/")}}admin/editVessel');
            f.submit();
            return false;
        }

        $(document).ready( function()
        {

        });
    </script>
@endsection
