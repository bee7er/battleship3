<?php
use App\MessageText;
?>

@extends('layouts.app')
@section('title') fleet templates @parent @endsection

@section('content')

    <div class="container is-fluid">

        <article class="panel is-success">
            <p class="panel-heading">Broadcast Message Texts</p>
            @include('common.msgs')
            @include('common.errors')

            <div class="bs-add-link is-pulled-right" title="Add a new messageText">
                <a class="bs-admin-button" href="javascript: location.reload();">Refresh</a><a class="bs-admin-button" href="javascript: gotoAddMessageText();">Add Broadcast Message</a><a class="bs-admin-button" href="javascript: gotoDashboard();">Dashboard</a>
            </div>

            <form id="messageTextsForm" action="" method="GET" class="form-horizontal">
                <input type="hidden" name="messageTextId" id="messageTextId" value="" />

                <table class="table is-bordered is-striped bs-form-table">
                    <thead>
                    <tr>
                        <th>Id</th>
                        <th>Name</th>
                        <th>Text</th>
                        <th>Type</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                    </thead>
                    <tfoot>
                    <tr>
                        <th>Id</th>
                        <th>Name</th>
                        <th>Text</th>
                        <th>Type</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                    </tfoot>
                    <tbody>

                    @if (isset($messageTexts) && $messageTexts->count() > 0)
                        @foreach($messageTexts as $messageText)
                            <tr>
                                <td>{{$messageText->id}}</td>
                                <td>{{$messageText->name}}</td>
                                <td>{{$messageText->text}}</td>
                                <td>{{$messageText->type}}</td>
                                <td>{{$messageText->status}}</td>
                                <td>
                                    <a class="bs-games-button" href="javascript: gotoEditMessageText({{$messageText->id}})">Edit</a><a class="bs-games-button" href="javascript: gotoDeleteMessageText({{$messageText->id}})">Delete</a>
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td class="bs-no-data" colspan="99">No Message Texts found</td>
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
         * Add a new messageText
         */
        function gotoDashboard()
        {
            let f = $('#messageTextsForm');
            f.attr('action', '{{env("BASE_URL", "/")}}admin/dashboard');
            f.submit();
            return false;
        }

        /**
         * Add a new messageText
         */
        function gotoAddMessageText()
        {
            let f = $('#messageTextsForm');
            f.attr('action', '{{env("BASE_URL", "/")}}admin/addMessageText');
            f.submit();
            return false;
        }

        /**
         * MessageText action
         */
        function gotoEditMessageText(messageTextId) {
            let f = $('#messageTextsForm');
            let u = $('#messageTextId');
            u.val(messageTextId);
            f.attr('action', '{{env("BASE_URL", "/")}}admin/editMessageText');
            f.submit();
            return false;
        }

        /**
         * MessageText action
         */
        function gotoDeleteMessageText(messageTextId) {
            let f = $('#messageTextsForm');
            let u = $('#messageTextId');
            if (confirm('Are you sure you want to delete this message text entrty?'))
            {
                u.val(messageTextId);
                f.attr('action', '{{env("BASE_URL", "/")}}admin/deleteMessageText');
                f.submit();
            }
            return false;
        }

        $(document).ready( function()
        {

        });
    </script>
@endsection
