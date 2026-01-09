<?php
use App\FleetTemplate;
?>

@extends('layouts.app')
@section('title') edit fleet template @parent @endsection

@section('content')

    <div class="container is-fluid">

        <article class="panel is-success">
            <p class="panel-heading">@if (isset($fleetTemplate->id)){{'Edit Fleet Template'}}@else{{'Add Fleet Template'}}@endif</p>
            @include('common.msgs')
            @include('common.errors')

            <form id="fleetTemplateForm" action="" method="GET" class="form-horizontal">
                {{ csrf_field() }}

                <input type="hidden" name="fleetTemplateId" id="fleetTemplateId" value="{{$fleetTemplate->id}}" />
                <input type="hidden" name="mode" id="mode" value="{{$mode}}" />

                <table class="table is-bordered is-striped bs-form-table">
                    <tbody>
                        <tr class="">
                            <td class="cell bs-section-title" colspan="2">
                                <div class="cell bs-errors" id="customErrors"></div>
                            </td>
                        </tr>
                        <tr class="">
                            <td class="cell bs-section-title">
                                Status:
                            </td>
                            <td class="cell bs-status" id="fleetTemplateStatus">
                                @if (isset($fleetTemplate->id))
                                    Edit
                                @else
                                    New
                                @endif
                            </td>
                        </tr>
                        <tr class="">
                            <td class="cell bs-section-title">
                                Vessel id:
                            </td>
                            <td class="cell">
                                @if (isset($fleetTemplate->id)){{$fleetTemplate->vessel_id}}@else{{'tbd'}}@endif
                            </td>
                        </tr>
                        <tr class="">
                            <td class="cell bs-section-title">
                                Vessel name:
                            </td>
                            <td class="cell">
                                @if (isset($fleetTemplate->id)){{$fleetTemplate->vessel_name}}@else{{'tbd'}}@endif
                            </td>
                        </tr>
                        <tr class="">
                            <td class="cell bs-section-title">
                                Vessel
                            </td>
                            <td class="cell">
                                <select name="vesselId" id="vesselId" aria-label="Vessel" class="bs-listbox">
                                    <option value="" class="">Select vessel</option>
                                    @if (isset($vessels) && $vessels->count() > 0)
                                        @foreach($vessels as $vessel)
                                            <option value="{{$vessel->id}}" @if ($vessel->id == $fleetTemplate->vessel_id) {{'selected'}}@endif>{{$vessel->name}}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </td>
                        </tr>

                        <tr class="">
                            <td class="cell" colspan="2">
                                <input class="button is-pulled-right mr-6" type="submit" value="Submit input" onclick="return submitRequest();" />
                                <input class="button is-pulled-right mr-6" type="submit" value="Cancel" onclick="return cancelRequest();" />
                            </td>
                        </tr>
                    </tbody>
                </table>
            </form>

        </article>
    </div>

@endsection

@section('page-scripts')
    <script type="text/javascript">
        /**
         * Validate the form and submit the add request
         */
        function submitRequest()
        {
            let f = $('#fleetTemplateForm');
            let vesselId = $('#vesselId');

            let errors = [];
            let atLeastOne = false;

            if ('' == vesselId.val()) {
                errors[errors.length] = 'Please select a vessel for this fleet template';
                atLeastOne = true;
                vesselId.focus();
            }
            if (atLeastOne) {
                let errMsgs = sep = "";
                for (let i=0; i<errors.length; i++) {
                    errMsgs += (sep + errors[i]);
                    sep = '<br />';
                }
                $('#customErrors').html(errMsgs).show().delay(3000).fadeOut();
                return false;
            }
            f.attr('method', 'POST');
            f.attr('action', '{{env("BASE_URL", "/")}}admin/updateFleetTemplate');
            f.submit();
            return false;
        }

        /**
         * Cancel request
         */
        function cancelRequest()
        {
            let f = $('#fleetTemplateForm');
            f.attr('method', 'GET');
            f.attr('action', '{{env("BASE_URL", "/")}}admin/fleetTemplates');
            f.submit();
            return false;
        }

        $(document).ready( function()
        {

        });
    </script>
@endsection