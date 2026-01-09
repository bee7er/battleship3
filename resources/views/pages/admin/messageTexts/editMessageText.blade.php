<?php
use App\MessageText;
?>

@extends('layouts.app')
@section('title') edit broadcast message @parent @endsection

@section('content')

    <div class="container is-fluid">

        <article class="panel is-success">
            <p class="panel-heading">@if (isset($messageText->id)){{'Edit Broadcast Message'}}@else{{'Add Broadcast Message'}}@endif</p>
            @include('common.msgs')
            @include('common.errors')

            <form id="messageTextForm" action="" method="GET" class="form-horizontal">
                {{ csrf_field() }}

                <input type="hidden" name="messageTextId" id="messageTextId" value="{{$messageText->id}}" />
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
                            <td class="cell bs-status" id="messageTextStatus">
                                @if (isset($messageText->id))
                                    Edit
                                @else
                                    New
                                @endif
                            </td>
                        </tr>
                        <tr class="">
                            <td class="cell bs-section-title">
                                Name:
                            </td>
                            <td class="cell">
                                <input type="text" id="name" name="name" value="@if (isset($messageText->id)){{$messageText->name}}@endif" />
                            </td>
                        </tr>
                        <tr class="">
                            <td class="cell bs-section-title">
                                Status:
                            </td>
                            <td class="cell">
                                <select name="status" id="status" aria-label="Message status" class="bs-listbox">
                                    <option value="" class="">Select status</option>
                                    @if (isset($statuses) && count($statuses) > 0)
                                        @foreach($statuses as $status)
                                            <option value="{{$status}}" @if ($status == $messageText->status) {{'selected'}}@endif>{{$status}}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </td>
                        </tr>
                        <tr class="">
                            <td class="cell bs-section-title">
                                Text:
                            </td>
                            <td class="cell">
                                <textarea id="text" name="text" rows="4" cols="64">@if (isset($messageText->id)){{$messageText->text}}@endif</textarea>

                            </td>
                        </tr>
                        <tr class="">
                            <td class="cell bs-section-title">
                                Type:
                            </td>
                            <td class="cell">
                                {{'broadcast'}}
                            </td>
                        </tr>
                        <tr class="">
                            <td class="cell bs-section-title">
                                Status:
                            </td>
                            <td class="cell">
                                {{'ready'}}
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
            let f = $('#messageTextForm');
            let name = $('#name');
            let text = $('#text');
            let status = $('#status');

            let errors = [];
            let atLeastOne = false;
            // NB Validate in reverse order so we focus on the first one, lower down
            if ('' == text.val()) {
                errors[errors.length] = 'Please enter text for this message';
                atLeastOne = true;
                text.focus();
            }

            if ('' == status.val()) {
                errors[errors.length] = 'Please select a status for this message';
                atLeastOne = true;
                status.focus();
            }

            if ('' == name.val()) {
                errors[errors.length] = 'Please enter a name for this message';
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
            f.attr('action', '{{env("BASE_URL", "/")}}admin/updateMessageText');
            f.submit();
            return false;
        }

        /**
         * Cancel request
         */
        function cancelRequest()
        {
            let f = $('#messageTextForm');
            f.attr('method', 'GET');
            f.attr('action', '{{env("BASE_URL", "/")}}admin/messageTexts');
            f.submit();
            return false;
        }

        $(document).ready( function()
        {

        });
    </script>
@endsection