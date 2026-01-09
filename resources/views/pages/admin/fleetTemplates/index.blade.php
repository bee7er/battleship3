<?php
use App\FleetTemplate;
?>

@extends('layouts.app')
@section('title') fleet templates @parent @endsection

@section('content')

    <div class="container is-fluid">

        <article class="panel is-success">
            <p class="panel-heading">Fleet Templates</p>
            @include('common.msgs')
            @include('common.errors')

            <div class="bs-add-link is-pulled-right" title="Add a new fleetTemplate">
                <a class="bs-admin-button" href="javascript: location.reload();">Refresh</a><a class="bs-admin-button" href="javascript: gotoAddFleetTemplate();">Add Fleet Template</a><a class="bs-admin-button" href="javascript: gotoDashboard();">Dashboard</a>
            </div>

            <form id="fleetTemplatesForm" action="" method="GET" class="form-horizontal">
                <input type="hidden" name="fleetTemplateId" id="fleetTemplateId" value="" />

                <table class="table is-bordered is-striped bs-form-table">
                    <thead>
                    <tr>
                        <th>Id</th>
                        <th>Vessel Id</th>
                        <th>Vessel Name</th>
                        <th>Action</th>
                    </tr>
                    </thead>
                    <tfoot>
                    <tr>
                        <th>Id</th>
                        <th>Vessel Id</th>
                        <th>Vessel Name</th>
                        <th>Action</th>
                    </tr>
                    </tfoot>
                    <tbody>

                    @if (isset($fleetTemplates) && $fleetTemplates->count() > 0)
                        @foreach($fleetTemplates as $fleetTemplate)
                            <tr>
                                <td>{{$fleetTemplate->id}}</td>
                                <td>{{$fleetTemplate->vessel_id}}</td>
                                <td>{{$fleetTemplate->vessel_name}}</td>
                                <td>
                                    <a class="bs-games-button" href="javascript: gotoEditFleetTemplate({{$fleetTemplate->id}})">Edit</a><a class="bs-games-button" href="javascript: gotoDeleteFleetTemplate({{$fleetTemplate->id}})">Delete</a>
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td class="bs-no-data" colspan="99">No Fleet Templates found</td>
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
         * Add a new fleetTemplate
         */
        function gotoDashboard()
        {
            let f = $('#fleetTemplatesForm');
            f.attr('action', '{{env("BASE_URL", "/")}}admin/dashboard');
            f.submit();
            return false;
        }

        /**
         * Add a new fleetTemplate
         */
        function gotoAddFleetTemplate()
        {
            let f = $('#fleetTemplatesForm');
            f.attr('action', '{{env("BASE_URL", "/")}}admin/addFleetTemplate');
            f.submit();
            return false;
        }

        /**
         * FleetTemplate action
         */
        function gotoEditFleetTemplate(fleetTemplateId) {
            let f = $('#fleetTemplatesForm');
            let u = $('#fleetTemplateId');
            u.val(fleetTemplateId);
            f.attr('action', '{{env("BASE_URL", "/")}}admin/editFleetTemplate');
            f.submit();
            return false;
        }

        /**
         * FleetTemplate action
         */
        function gotoDeleteFleetTemplate(fleetTemplateId) {
            let f = $('#fleetTemplatesForm');
            let u = $('#fleetTemplateId');
            if (confirm('Are you sure you want to delete this fleet template entrty?'))
            {
                u.val(fleetTemplateId);
                f.attr('action', '{{env("BASE_URL", "/")}}admin/deleteFleetTemplate');
                f.submit();
            }
            return false;
        }

        $(document).ready( function()
        {

        });
    </script>
@endsection
