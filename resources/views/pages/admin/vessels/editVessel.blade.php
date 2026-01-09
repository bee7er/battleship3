<?php
use App\Vessel;
?>

@extends('layouts.app')
@section('title') edit vessel @parent @endsection

@section('content')

    <div class="container is-fluid">

        <article class="panel is-success">
            <p class="panel-heading">@if (isset($vessel->id)){{'Edit Vessel'}}@endif</p>
            @include('common.msgs')
            @include('common.errors')

            <form id="vesselForm" action="" method="GET" class="form-horizontal">
                {{ csrf_field() }}

                <input type="hidden" name="vesselId" id="vesselId" value="{{$vessel->id}}" />
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
                            <td class="cell bs-status" id="vesselStatus">
                                @if (isset($vessel->id))
                                    Edit
                                @else
                                    New
                                @endif
                            </td>
                        </tr>
                        <tr class="">
                            <td class="cell bs-section-title">
                                Vessel name:
                            </td>
                            <td class="cell">
                                <input type="text" id="vesselName" name="vesselName" value="@if (isset($vessel->id)){{$vessel->name}}@endif" />
                            </td>
                        </tr>
                        <tr class="">
                            <td class="cell bs-section-title">
                                Length:
                            </td>
                            <td class="cell">
                                <input type="text" id="length" name="length" value="@if (isset($vessel->id)){{$vessel->length}}@endif" />
                            </td>
                        </tr>
                        <tr class="">
                            <td class="cell bs-section-title">
                                Points:
                            </td>
                            <td class="cell">
                                <input type="text" id="points" name="points" value="@if (isset($vessel->id)){{$vessel->points}}@endif" />
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
            let f = $('#vesselForm');
            let name = $('#vesselName');
            let length = $('#length');
            let points = $('#points');

            let errors = [];
            let atLeastOne = false;
            // NB Validate in reverse order so we focus on the first one, lower down
            if ('' == points.val()) {
                errors[errors.length] = 'Please enter points for this vessel';
                atLeastOne = true;
                points.focus();
            }

            if ('' == length.val()) {
                errors[errors.length] = 'Please enter a length for this vessel';
                atLeastOne = true;
                length.focus();
            }

            if ('' == name.val()) {
                errors[errors.length] = 'Please enter a name for this vessel';
                atLeastOne = true;
                name.focus();
            }

            if (atLeastOne) {
                let errMsgs = sep = "";
                // Again in reverse order so the messages are in sync with the focus
                for (let i=(errors.length - 1); i>=0; i--) {
                    errMsgs += (sep + errors[i]);
                    sep = '<br />';
                }
                $('#customErrors').html(errMsgs).show().delay(3000).fadeOut();
                return false;
            }

            f.attr('method', 'POST');
            f.attr('action', '{{env("BASE_URL", "/")}}admin/updateVessel');
            f.submit();
            return false;
        }

        /**
         * Cancel request
         */
        function cancelRequest()
        {
            let f = $('#vesselForm');
            f.attr('method', 'GET');
            f.attr('action', '{{env("BASE_URL", "/")}}admin/vessels');
            f.submit();
            return false;
        }

        $(document).ready( function()
        {

        });
    </script>
@endsection