<?php
use App\FleetVessel;
use App\Game;

$fleetId = 0;
?>

@extends('layouts.app')
@section('title') edit grid @parent @endsection

@section('content')

    <div class="container is-fluid">

        <article class="panel is-success">
            <p class="panel-heading">Edit Game Grid</p>
            @include('common.msgs')
            @include('common.errors')

            <input type="hidden" name="gameId" id="gameId" value="{{$game->id}}" />
            <input type="hidden" id="userToken" value="{{$userToken}}" />

            <table class="table is-bordered is-striped bs-form-table">
                <tbody>
                    <tr class="">
                        <td class="cell bs-section-title" width="50%">
                            Game status:
                        </td>
                        <td class="cell bs-status">
                            <span id="gameStatus">{{ucfirst($game->status)}}</span>
                            <span id="engageLink" class="is-pulled-right">
                                <span class="bs-ready-to-play">The game is ready to play &gt;&gt;</span><a class="bs-games-button" href="javascript: location.href='{{env("BASE_URL", "/")}}playGrid?gameId={{$game->id}}'">Engage</a>
                            </span>
                        </td>
                    </tr>
                    <tr class="">
                        <td class="cell bs-section-title">
                            Game name:
                        </td>
                        <td class="cell">
                            {{$game->game_name}}
                        </td>
                    </tr>
                    <tr class="">
                        <td class="cell bs-section-title">
                            Player 1:
                        </td>
                        <td class="cell{{$game->player_one_id==$userId ? 'bs-play-status': ''}}">
                            {{$game->player_one_name}}
                        </td>
                    </tr>
                    <tr class="">
                        <td class="cell bs-section-title">
                            @if (null != $game->player_two_name)
                                Player 2:
                            @else
                                Copy link and send it to player 2:
                            @endif
                        </td>
                        <td class="cell {{$game->player_two_id==$userId ? 'bs-play-status': ''}}">
                            @if (null != $game->player_two_id)
                                {{$game->player_two_name}}
                            @else
                                @include('partials.player_two')
                            @endif
                        </td>
                    </tr>
                    @if (null == $game->player_two_id)
                        <tr class="">
                            <td class="cell bs-section-title">
                                    Play against the machine, once you have plotted your vessels:
                            </td>
                            <td class="cell">
                                <button id="singlePlayerButtonId" class="button is-link bs-random_button" onclick="return singlePlayerGame();">Single Player Game</button>
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>

        </article>
        <div class="field">
            <div class="bs-section-help">Select each vessel and plot its positions on the grid. Each vessel has a length corresponding with the number of positions which must be plotted. These changes are <b>saved automatically</b>.</div>
            <div class="bs-section-help">Click the <b>Go Random</b> button to have the game generate a random set of positions. Click <b>Save Random</b> when you see a combination of locations you like.</div>
            <div class="bs-section-help">Click on the <b>Copy link</b> and send it to a friend to play against, <b><u>or</u></b> click the <b>Single Player Game</b> button to play against the machine.</div>
        </div>

        @include('partials.show_notification')

        <div class="columns">

            <div class="column">

                <table class="table is-bordered is-striped bs-plot-table">
                    <tbody>
                    <tr class="">
                        <th class="bs-grid-title" colspan="99">Fleet Vessels:</th>
                    </tr>

                    <tr class=" bs-grid-title">
                        <th class="cell">Select</th>
                        <th class="cell">Name</th>
                        <th class="cell">Status</th>
                        <th class="cell">Length</th>
                        <th class="cell">Points</th>
                    </tr>

                    @if (isset($fleet) && count($fleet) > 0)
                        @foreach ($fleet as $fleetVessel)
                            <?php $fleetId = $fleetVessel->id; ?>
                            <tr class="" onclick="selectRow('{{$fleetVessel->fleet_vessel_id}}')">
                                <td class="cell">
                                    <input type="radio" id="radio_id_{{$fleetVessel->fleet_vessel_id}}"
                                           name="vessel" value="{{$fleetVessel->fleet_vessel_id}}" onclick="onClickSelectVessel(this);" />
                                </td>
                                <td class="cell" id="name_{{$fleetVessel->fleet_vessel_id}}">{{$fleetVessel->vessel_name}}</td>
                                <td class="cell" id="status_{{$fleetVessel->fleet_vessel_id}}">{{$fleetVessel->status}}</td>
                                <td class="cell" id="length_{{$fleetVessel->fleet_vessel_id}}">{{$fleetVessel->length}}</td>
                                <td class="cell" id="points_{{$fleetVessel->fleet_vessel_id}}">{{$fleetVessel->points}}</td>
                            </tr>
                        @endforeach
                    @endif

                    </tbody>
                </table>
            </div>


            <div class="column has-text-centered is-one-quarter">
                <table class="table is-bordered bs-plot-table">
                    <tbody>
                    <tr class=""><td class="bs-grid-title" colspan="2">Key to colours:</td></tr>
                    <tr class=""><td class="bs-pos-key-available">&nbsp;</td><td class="bs-pos-key-blank">Available location</td></tr>
                    <tr class=""><td class="bs-pos-key-started">&nbsp;</td><td class="bs-pos-key-blank">Vessel started</td></tr>
                    <tr class=""><td class="bs-pos-key-plotted">&nbsp;</td><td class="bs-pos-key-blank">Vessel plotted</td></tr>
                    </tbody>
                </table>
                <hr />
                <div>
                    <button id="goRandomButtonId" class="button is-link bs-random_button" onclick="return goRandom();">Go Random</button>
                </div>
                <div>
                    <button id="cancelRandomButtonId" class="button is-link is-light bs-random_button" disabled="disabled" onclick="return cancelRandom();">Cancel Random</button>
                </div>
                <div>
                    <button id="saveRandomButtonId" class="button is-link bs-random_button" disabled="disabled" onclick="return saveRandom();">Save Random</button>
                </div>

                <hr />
                <div>
                    <button id="startAgainButtonId" class="button is-link bs-random_button" onclick="return startAgain();">Start Again</button>
                </div>
            </div>


            <div class="column">

                <table class="table is-bordered is-striped bs-plot-table">
                    <tbody>
                    <tr class="">
                        <th class="bs-grid-title" colspan="99">Vessel Locations Grid:</th>
                    </tr>

                    @for ($row=0; $row<=10; $row++)
                        <tr class="" id="row{{$row}}">

                            @for ($col=0; $col<=10; $col++)

                                @if ($row == 0)
                                    @if ($col > 0)
                                        <td class="cell has-text-centered bs-plot-cell-header">{{$col}}</td>
                                    @else
                                        <td class="cell bs-grid-title">&nbsp;</td>
                                    @endif
                                @else
                                    @if ($col == 0)
                                        @if ($row > 0)
                                            <td class="cell has-text-centered bs-plot-cell-header">{{getAlpha($row)}}</td>
                                        @else
                                            <td class="cell bs-grid-title">&nbsp;</td>
                                        @endif
                                    @else
                                        <td class="cell grid-cell has-text-centered bs-pos-cell-blank bs-cursor-default"
                                            id="cell_{{$row}}_{{$col}}" onclick="onClickAllocateCell(this);" onmouseover="$(this).removeClass('bs-cursor-default').addClass('bs-cursor-pointer')" onmouseout="$(this).removeClass('bs-cursor-pointer').addClass('bs-cursor-default')">O</td>
                                    @endif
                                @endif

                            @endfor
                        </tr>
                    @endfor

                    </tbody>
                </table>

                <div class="field">&nbsp;</div>
                <div class="field">&nbsp;</div>
            </div>
        </div>
    </div>

@endsection

@section('page-scripts')
    <script type="text/javascript">
        var gameId = {{$game->id}};
        var fleetId = {{$fleetId}};
        var playerTwoId = {{$game->player_two_id ? : 0}};
        var fleetVessels = [];
        var fleetVessel = {};
        var fleetVesselLocations = [];
        var gridSize = 10;
        var randomMode = false;
        var fleetVesselsClone = null;

        const FLEET_LOCATION_SIZE = {{$fleetLocationSize}};
        const BY_ROW = 'byRow';
        const BY_COL = 'byCol';
        const DIMENSION_HORIZONTAL = 'h';
        const DIMENSION_VERTICAL = 'v';

        // Load all the existing data for the fleet
        @if (isset($fleet) && count($fleet) > 0)
            @foreach ($fleet as $fleetVessel)

                fleetVesselLocations = [];

                @foreach ($fleetVessel->locations as $fleetVesselLocation)
                    fleetVesselLocations[fleetVesselLocations.length] = {
                        id: {{$fleetVesselLocation['id']}},
                        fleet_vessel_id: {{$fleetVesselLocation['fleet_vessel_id']}},
                        row: {{$fleetVesselLocation['row']}},
                        col: {{$fleetVesselLocation['col']}},
                        vessel_name: '{{$fleetVesselLocation['vessel_name']}}',
                    };
                @endforeach

                fleetVessel = {
                    fleetVesselId: {{$fleetVessel->fleet_vessel_id}},
                    vessel_name: '{{$fleetVessel->vessel_name}}',
                    status: '{{$fleetVessel->status}}',
                    length: {{$fleetVessel->length}},
                    points: {{$fleetVessel->points}},
                    locations: fleetVesselLocations
                };

                fleetVessels[fleetVessels.length] = fleetVessel;
            @endforeach
        @endif

        /**
         * Allocates a cell to a vessel
         */
        function onClickAllocateCell(elem)
        {
            if (allVesselsPlotted()) {
                showNotification('All the vessels have been plotted.');
                return false;
            }

            if (true == randomMode) {
                showNotification('Cancel random allocation of vessels to continue');
                return false;
            }

            let selected = $("input[type='radio'][name='vessel']:checked");
            if (selected.length <= 0) {
                showNotification('Please select a vessel to allocate to this position');
                return false;
            }

            if ($(elem).hasClass('bs-pos-cell-plotted')) {
                showNotification('The location you clicked on has already been taken');
                return false;
            }

            // Get row/col from the clicked element
            let elemIdData = elem.id.split('_');
            let row = parseInt(elemIdData[1]);
            let col = parseInt(elemIdData[2]);

            let fleetVesselId = selected.val();
            fleetVessel = findFleetVessel(fleetVesselId);

            // If already started then we undo that and remove it from the locations for this vessel
            if ($(elem).hasClass('bs-pos-cell-started')) {
                // User re-clicked on an allocated cell, so undo the selection
                removeCellAllocation(elem, row, col);
                return false;

            } else if (0 == fleetVessel.locations.length || $(elem).hasClass('bs-pos-cell-available')) {
                // Ok, we can start the plotting (length==0), or this cell can be plotted (it is pinkly available)
            } else {
                showNotification('Please click on an available (pink) location');
                return false;
            }
            // Add this location to the current vessel
            fleetVessel.locations[fleetVessel.locations.length] = {
                id: 0,
                fleet_vessel_id: fleetVesselId,
                row: row,
                col: col,
                vessel_name: fleetVessel.vessel_name
            };
            // Set the cell as started, as is useful for analysing cells in the callback function
            $('#cell_' + row + '_' + col).addClass('bs-pos-cell-started');

            // See if we can fill in any gaps in the set of locations
            fleetVessel = fillLocationGaps(fleetVessel);

            // Add game, fleet and opponent ids for some checking server side
            fleetVessel.gameId = gameId;
            fleetVessel.fleetId = fleetId;
            fleetVessel.playerTwoId = playerTwoId;
            fleetVessel.subjectRow = row;
            fleetVessel.subjectCol = col;
            fleetVessel.user_token = getCookie('user_token');

            // ========================================================================
            // Post the new location to the server and await the return in the callback
            ajaxCall('setVesselLocation', JSON.stringify(fleetVessel), updateFleetVessel);
        }

        /**
         * Check the locations, if there are any gaps fill them
         */
        function fillLocationGaps(fleetVessel)
        {
            if (fleetVessel.locations.length <= 1) {
                // No gaps available
                return fleetVessel;
            }

            let rows = [];
            let cols = [];
            // Load the arrays with the current positions
            for (let i=0; i<fleetVessel.locations.length; i++) {
                let location = fleetVessel.locations[i];
                rows[rows.length] = location.row;
                cols[cols.length] = location.col;
            }

            // Either the rows will be the same or the cols will
            if (rows[0] == rows[1]) {
                // We are dealing with cols, sort with a numeric sort compare function
                // If the result is negative, a is sorted before b.
                // If the result is positive, b is sorted before a.
                // If the result is 0, no changes are done with the sort order of the two values
                cols.sort(function(a, b){return a - b});
                let col = 0;
                let row = rows[0];  // Any row element will do, as they are all the same
                for (let i=0; i<cols.length; i++) {
                    if (i == 0) {
                        col = cols[0];
                        $('#cell_' + row + '_' + col).addClass('bs-pos-cell-started');
                        col += 1;   // We start at the next col
                    } else {
                        while (col < cols[i]) {
                            if (!$('#cell_' + row + '_' + col).hasClass('bs-pos-cell-started')) {
                                fleetVessel.locations[fleetVessel.locations.length] = {
                                    id: 0,
                                    fleet_vessel_id: fleetVessel.fleetVesselId,
                                    row: row,
                                    col: col,
                                    vessel_name: fleetVessel.vessel_name
                                };
                                $('#cell_' + row + '_' + col).addClass('bs-pos-cell-started');
                            }
                            col++;
                        }
                    }
                }
            } else  {
                rows.sort(function(a, b){return a - b});
                let row = 0;
                let col = cols[0];  // Any col element will do, as they are all the same
                for (let i=0; i<rows.length; i++) {
                    if (i == 0) {
                        row = rows[0];
                        $('#cell_' + row + '_' + col).addClass('bs-pos-cell-started');
                        row += 1;   // We start at the next row
                    } else {
                        while (row < rows[i]) {
                            if (!$('#cell_' + row + '_' + col).hasClass('bs-pos-cell-started')) {
                                // Any col element will do, just use the first one
                                fleetVessel.locations[fleetVessel.locations.length] = {
                                    id: 0,
                                    fleet_vessel_id: fleetVessel.fleetVesselId,
                                    row: row,
                                    col: col,
                                    vessel_name: fleetVessel.vessel_name
                                };
                                $('#cell_' + row + '_' + col).addClass('bs-pos-cell-started');
                            }
                            row++;
                        }
                    }
                }
            }

            return fleetVessel;
        }

        /**
         * User has clicked on a started cell, or has abandoned allocating for a vessel
         * by clicking on another vessel.  We remove the cell from the set of allocated ones.
         */
        function removeCellAllocation(elem, row, col)
        {
            fleetVesselByRowCol = findFleetVesselByRowCol(row, col);
            if (fleetVessel.fleetVesselId != fleetVesselByRowCol.fleetVesselId) {
                showNotification('This location is occupied by another vessel');
                return false;
            }

            fleetVessel.subjectRow = 0;
            fleetVessel.subjectCol = 0;
            // If there is one or more remaining allocated elems then we want to reposition to one on return
            for (let i=0; i<fleetVessel.locations.length; i++) {
                let location = fleetVessel.locations[i];
                if (location.row != row || location.col != col) {
                    fleetVessel.subjectRow = location.row;
                    fleetVessel.subjectCol = location.col;
                    break;
                }
            }
            // Ok, release this plotted cell
            let location = {
                gameId: gameId,
                fleetVessel: fleetVessel,
                row: row,
                col: col,
                user_token: getCookie('user_token')
            };

            // ========================================================================
            ajaxCall('removeVesselLocation', JSON.stringify(location), updateFleetVessel);
            // Clear the cell and availability
            setElemStatusClass(elem, '');
            $(elem).html('O');
            // Generic removal of all cells flagged as available
            $('.grid-cell').removeClass('bs-pos-cell-available');

            showNotification('Location has been cleared');
        }

        /**
         * Selects a vessel to start plotting its locations.  We uncheck any existing
         * vessel started locations and available spaces
         */
        function onClickSelectVessel(rowElem)
        {
            // Generic removal of all cells flagged as available or started
            $('.grid-cell').removeClass('bs-pos-cell-available');
            let starteds = $('.bs-pos-cell-started');
            if (starteds.length > 0) {
                let started = starteds[0];
                // Get row/col from the clicked element
                let elemIdData = started.id.split('_');
                let row = parseInt(elemIdData[1]);
                let col = parseInt(elemIdData[2]);
                let fleetVesselStarted = findFleetVesselByRowCol(row, col);
                // No other locations will be involved
                fleetVesselStarted.subjectRow = 0;
                fleetVesselStarted.subjectCol = 0;
                // If there are any started cells then we remove them all server side
                let location = {
                    gameId: gameId,
                    fleetVessel: fleetVesselStarted,
                    user_token: getCookie('user_token')
                };

                // ========================================================================
                ajaxCall('removeAllVesselLocations', JSON.stringify(location), updateFleetVessel);
            }
            // Clear all started cells
            $('.bs-pos-cell-started').each(function() {
                $(this).html('O');
                $(this).removeClass('bs-pos-cell-started');
            });

            return false;
        }

        /**
         * Find the fleet vessel details based on the fleet vessel id
         */
        function findFleetVessel(fleetVesselId)
        {
            for (let i=0; i<fleetVessels.length; i++) {
                let fleetVessel = fleetVessels[i];
                if (fleetVesselId == fleetVessel.fleetVesselId) {
                    return fleetVessel;
                }
            }
            alert('Error: Could not find fleet vessel for id ' + fleetVesselId);
        }

        /**
         * Find the fleet vessel details by row/col location
         */
        function findFleetVesselByRowCol(row, col)
        {
            for (let i=0; i<fleetVessels.length; i++) {
                let fleetVessel = fleetVessels[i];
                for (let j=0; j<fleetVessel.locations.length; j++) {
                    if (fleetVessel.locations[j].row == row && fleetVessel.locations[j].col == col) {
                        return fleetVessel;
                    }
                }
            }
            alert('Error: Could not find fleet vessel for row ' + row + ' and col' + col);
        }

        /**
         * Plot vessels which have been allocated positions on the grid
         */
        function plotFleetLocations()
        {
            if (null == fleetVessels || [] == fleetVessels || 0 == fleetVessels.length) {
                return;
            }
            for (let i = 0; i < fleetVessels.length; i++) {
                let fleetVessel = fleetVessels[i];
                // Update the status, which may have changed
                $('#status_' + fleetVessel.fleetVesselId).html(fleetVessel.status);
                // Plot each location
                for (let j = 0; j < fleetVessel.locations.length; j++) {
                    let location = fleetVessel.locations[j];
                    let cssClass = 'bs-pos-cell-started';
                    if ('{{FleetVessel::FLEET_VESSEL_PLOTTED}}' == fleetVessel.status) {
                        cssClass = 'bs-pos-cell-plotted';
                        // Disable the corresponding radio button, as this vessel is fully plotted
                        $('#radio_id_' + location.fleet_vessel_id).prop("disabled", true);
                    }
                    let tableCell = $('#cell_' + location.row + '_' + location.col);
                    setElemStatusClass(tableCell, cssClass);
                    tableCell.html(location.vessel_name.toUpperCase().charAt(0));
                }
                // NB If the selected vessel from above is now plotted then deselect it
                let selected = $("input[type='radio'][name='vessel']:checked");
                if (selected.length > 0) {
                    let fleetVesselId = selected.val();
                    fleetVessel = findFleetVessel(fleetVesselId);
                    if ('{{FleetVessel::FLEET_VESSEL_PLOTTED}}' == fleetVessel.status) {
                        $("input[type='radio'][name='vessel']").prop('checked', false);
                        // Select the next available one
                        selectFirstAvailableVessel();
                    }
                }
            }
        }

        /**
         * Highlight available cells
         */
        function availableCells(row, col, fleetVessel)
        {
            // Generic removal of all cells flagged as available, we are going to recalculate which ones are available
            $('.grid-cell').removeClass('bs-pos-cell-available');

            // Exit if the vessel is already plotted, we are done with this vessel
            if ('{{FleetVessel::FLEET_VESSEL_PLOTTED}}' == fleetVessel.status) {
                return;
            }

            let numberOfAvailableHorizontalCells = 0;
            let numberOfAvailableVerticalCells = 0;
            let numberOfAvailableCells = 0;
            let requiredLen = (fleetVessel.length - fleetVessel.locations.length);

            let tryRow = row - (fleetVessel.length - 1);
            let tryCol = col - (fleetVessel.length - 1);

            let enoughRoom = true;
            if (fleetVessel.locations.length == 1) {
                numberOfAvailableHorizontalCells += plotAvailableLocations(DIMENSION_HORIZONTAL, fleetVessel.length, tryRow, tryCol);
                numberOfAvailableVerticalCells += plotAvailableLocations(DIMENSION_VERTICAL, fleetVessel.length, tryRow, tryCol);
                if (numberOfAvailableHorizontalCells < requiredLen && numberOfAvailableVerticalCells < requiredLen) {
                    enoughRoom = false;
                }
            } else {
                numberOfAvailableCells = plotSubsequentLocations(fleetVessel.length);
                if (numberOfAvailableCells < requiredLen) {
                    enoughRoom = false;
                }
            }
            // Check that there is somewhere to go
            if (false == enoughRoom) {
                showNotification('There is not enough room or no squares available in that position. Please move it elsewhere.');
                return false;
            }

            if (false == randomMode) {
                showNotification('Now click on one of the highlighted (pink) locations');
            }

            // There is enough room.  Notify caller, this is needed by the random allocation processing.
            return true;
        }

        /**
         * Working either horizontally or vertically we examine each potential position
         * and check whether there is enough space for the vessel. If there is then we mark
         * the available squares with pink to indicate they are available.
         */
        function plotAvailableLocations(dimension, vesselLength, tryRow, tryCol)
        {
            let avail = [];
            let elem = {};
            let counter = 0;
            let itrLen = (2 * vesselLength) - 1;
            let offset = vesselLength - 1;

            for (let n=0; n<itrLen; n++) {
                if (DIMENSION_HORIZONTAL == dimension) {
                    // We are looking horizontally
                    if ((tryCol + n) <= 0 || (tryCol + n) > gridSize) continue;
                    elem = $('#cell_' + (tryRow + offset) + '_' + (tryCol + n));
                } else {
                    // We are looking vertically
                    if ((tryRow + n) <= 0 || (tryRow + n) > gridSize) continue;
                    elem = $('#cell_' + (tryRow + n) + '_' + (tryCol + offset));
                }
                // Plotted happens in manual mode and started happens in random mode
                if ($(elem).hasClass('bs-pos-cell-plotted') || (randomMode && $(elem).hasClass('bs-pos-cell-started'))) {
                    // If before the centre we clear the array otherwise we just stop
                    if (n < offset) {
                        avail = [];
                    } else {
                        break;
                    }
                } else {
                    avail[avail.length] = n;
                }
            }
            if (avail.length >= (offset + 1)) {
                // There is enough space for the vessel, but is our position (rowIdx or colIdx) among those available
                for (let m=0; m<avail.length; m++) {
                    if (DIMENSION_HORIZONTAL == dimension) {
                        elem = $('#cell_' + (tryRow + offset) + '_' + (tryCol + avail[m])); // We are looking horizontally
                    } else {
                        elem = $('#cell_' + (tryRow + avail[m]) + '_' + (tryCol + offset)); // We are looking vertically
                    }
                    setElemStatusClass(elem, 'bs-pos-cell-available');
                    counter += 1;
                }
            }

            return counter;
        }

        /**
         * When more than one location has been started then the available squares can only be in
         * the same dimension as them.  So where are the started squares and where next can they go?
         * We mark the available squares with pink to indicate they are available.
         */
        function plotSubsequentLocations(vesselLength)
        {
            let numberOfAvailableCells = 0;
            let rows = [];
            let cols = [];
            let elems = $('.bs-pos-cell-started').get();
            // How many available cells do we need?
            let availableCellsNeeded = vesselLength - elems.length;
            // Extract the rows and cols from the started elements
            for (let i=0; i<elems.length; i++) {
                let elem = elems[i];
                // Get row/col from the started element
                let elemIdData = $(elem).prop('id').split('_');
                rows[rows.length] = parseInt(elemIdData[1]);
                cols[cols.length] = parseInt(elemIdData[2])
            }
            // Either the rows will be the same or the cols will be the same
            // We are plotting available cells, which can go at either end of the started cells
            if (rows[0] == rows[1]) {
                // All on the same row, so we are dealing with different columns
                cols.sort(function(a, b){return a - b});
                let startCol = (cols[0] - 1);
                let row = rows[0];  // Any row element will do, as they are all the same
                // Going backwards because we may encounter a plotted location
                for (let i=availableCellsNeeded; i>0; i--) {
                    if (startCol <= 0) {
                        break;
                    }
                    let elem = $('#cell_' + row + '_' + startCol);
                    if (elem.hasClass('bs-pos-cell-plotted')) {
                        break;
                    }
                    setElemStatusClass(elem, 'bs-pos-cell-available');
                    startCol -= 1;
                    numberOfAvailableCells += 1;
                }
                // Going forwards until the end or a plotted cell
                startCol = (cols[cols.length - 1] + 1);
                for (let i=0; i<availableCellsNeeded; i++) {
                    if (startCol > gridSize) {
                        break;
                    }
                    let elem = $('#cell_' + row + '_' + startCol);
                    if (elem.hasClass('bs-pos-cell-plotted')) {
                        break;
                    }
                    setElemStatusClass(elem, 'bs-pos-cell-available');
                    startCol += 1;
                    numberOfAvailableCells += 1;
                }
            } else  {
                // All on the same column, so we are dealing with different rows
                rows.sort(function(a, b){return a - b});
                let startRow = (rows[0] - 1);
                let col = cols[0];  // Any col element will do, as they are all the same
                // Going backwards because we may encounter a plotted location
                for (let i=availableCellsNeeded; i>0; i--) {
                    if (startRow <= 0) {
                        break;
                    }
                    let elem = $('#cell_' + startRow + '_' + col);
                    if (elem.hasClass('bs-pos-cell-plotted')) {
                        break;
                    }
                    setElemStatusClass(elem, 'bs-pos-cell-available');
                    startRow -= 1;
                    numberOfAvailableCells += 1;
                }
                // Now go forwards from the highest started location
                startRow = (rows[rows.length - 1] + 1);
                for (let i=0; i<availableCellsNeeded; i++) {
                    if (startRow > gridSize) {
                        break;
                    }
                    let elem = $('#cell_' + startRow + '_' + col);
                    if (elem.hasClass('bs-pos-cell-plotted')) {
                        break;
                    }
                    setElemStatusClass(elem, 'bs-pos-cell-available');
                    startRow += 1;
                    numberOfAvailableCells += 1;
                }
            }

            return numberOfAvailableCells;
        }

        /**
         * Set the element to one status class
         */
        function setElemStatusClass(elem, newClass)
        {
            $(elem).removeClass('bs-pos-cell-available');
            $(elem).removeClass('bs-pos-cell-started');
            $(elem).removeClass('bs-pos-cell-plotted');
            $(elem).addClass(newClass);
        }

        /**
         * Callback function to handle the asynchronous Ajax call
         */
        function setGameStatus(returnedGameStatus)
        {
            $('#gameStatus').html(returnedGameStatus.gameStatus);
            if ('{{Game::STATUS_READY}}' == returnedGameStatus.gameStatus
                    || '{{Game::STATUS_ENGAGED}}' == returnedGameStatus.gameStatus
            ) {
                $('#engageLink').show();
            }

            $('#singlePlayerButtonId').prop('disabled', true);
            if ('{{Game::STATUS_WAITING}}' == returnedGameStatus.gameStatus) {
                $('#singlePlayerButtonId').prop('disabled', false);
            }
        }

        /**
         * Clicking anywhere on a table row selects that radio button, for convenience
         */
        function selectRow(fleetVesselId)
        {
            let elem = $('#radio_id_' + fleetVesselId);
            elem.prop('checked', true);
            // Process the selected eleemnt
            onClickSelectVessel(elem.get(0));
        }

        /**
         * Callback function to handle the asynchronous Ajax call
         * @param returnedFleetVessel: is the returned data for this callback
         */
        function updateFleetVessel(returnedFleetVessel)
        {
            let row = returnedFleetVessel.subjectRow;
            let col = returnedFleetVessel.subjectCol;
            // Update the fleet vessel as a result of the new location
            let fleetVessel = null;
            for (let i=0; i<fleetVessels.length; i++) {
                if (fleetVessels[i].fleetVesselId == returnedFleetVessel.fleetVesselId) {
                    fleetVessels[i].status = returnedFleetVessel.status;
                    fleetVessels[i].locations = returnedFleetVessel.locations;

                    fleetVessel = fleetVessels[i];
                }
            }

            if ('{{FleetVessel::FLEET_VESSEL_PLOTTED}}' == fleetVessel.status) {
                showNotification('Vessel locations saved');
            }

            if (0 != row && 0 != col) {
                // NB This function must be called here else we encounter a timing issue
                // between the Ajax call and the testing of the status of the returned fleet vessel
                // Plot available cells around the clicked cell, if any.
                availableCells(row, col, fleetVessel);
            }

            // Set the attributes of the clicked cell, by replotting all fleet locations
            plotFleetLocations();

            // Check the status of the game, as it may have changed
            let statusCheck = {
                gameId: returnedFleetVessel.gameId,
                user_token: getCookie('user_token')
            };
            // =====================================================================
            ajaxCall('getGameStatus', JSON.stringify(statusCheck), setGameStatus);
        }

        /**
         * Randomly plot an entire set of vessels
         */
        function goRandom()
        {
            randomMode = true;

            $("#cancelRandomButtonId").prop("disabled", false);
            $("#saveRandomButtonId").prop("disabled", false);

            // Uncheck all radio buttons.  User must cancel random to continue editing locations.
            $("input[type='radio'][name='vessel']").prop('checked', false);
            let cells = clearGrid();
            cells.addClass('unoccupied');       // Sets all cells as being available

            // Disable all the radio buttons
            $(':radio').prop("disabled", true);
            // Keep collection of all grid cells
            let gridCells = $('.grid-cell');

            if (null == fleetVesselsClone) {
                // Back up the fleet vessels so we can go back if the user cancels
                fleetVesselsClone = jQuery.extend(true, [], fleetVessels);
            }

            for (let i=0; i<fleetVessels.length; i++) {
                // For each vessel we find space on the grid randomly.  Then allocate that space to the vessel.
                let fleetVessel = fleetVessels[i];
                fleetVessel.status = '{{FleetVessel::FLEET_VESSEL_AVAILABLE}}';
                // Try up to 10 times to find enough space for the vessel
                for (let j=0; j<10; j++) {
                    // Select a cell at random from those available
                    let unoccupiedCell = selectUnoccupiedCell();
                    // The available processing uses the details of the location to work out available cells
                    fleetVessel.locations = [];
                    fleetVessel.locations[0] = {
                        id: 0,
                        fleet_vessel_id: fleetVessel.fleetVesselId,
                        row: unoccupiedCell.rowInt,
                        col: unoccupiedCell.colInt,
                        vessel_name: fleetVessel.vessel_name
                    };

                    // Flag available cells and if successful we randomly choose a set
                    if (availableCells(unoccupiedCell.rowInt, unoccupiedCell.colInt, fleetVessel))
                    {
                        let vesselLocationsFulfilled = false;
                        let randDimension = Math.floor(Math.random() * 2) + 1;   // A number between 1 and 2
                        for (let k=0; k<2; k++) {
                            // We know that one of the dimensions has enough locations, so we'll try twice
                            if (2 == randDimension) {
                                // Try to get matching row elems
                                let sortedRowAvailables = getSortedAvailables(BY_ROW, unoccupiedCell.rowInt);
                                if (sortedRowAvailables.length >= fleetVessel.length) {
                                    // There are at least enough available cells, grab them and add
                                    // corresponding locations to the fleet vessel
                                    addLocationsToFleetVessel(sortedRowAvailables, fleetVessel);
                                    // Get ready for the next vessel
                                    gridCells.removeClass('bs-pos-cell-available');
                                    // We are done with this vessel
                                    vesselLocationsFulfilled = true;
                                    break;
                                }
                                // Try cols next time
                                randDimension = 1;
                            } else {
                                // Try to get matching col elems
                                let sortedColAvailables = getSortedAvailables(BY_COL, unoccupiedCell.colInt);
                                if (sortedColAvailables.length >= fleetVessel.length) {
                                    convertArrayAndAddLocationsToFleetVessel(sortedColAvailables, fleetVessel);
                                    gridCells.removeClass('bs-pos-cell-available');
                                    vesselLocationsFulfilled = true;
                                    break;
                                }
                                // Try rows next time
                                randDimension = 2;
                            }
                        }
                        if (true == vesselLocationsFulfilled) {
                            break;
                        }
                    }
                }
            }

            gridCells.removeClass('unoccupied');
            gridCells.removeClass('bs-pos-cell-available');

            let checkStartedCells = $('.bs-pos-cell-started' );
            if (FLEET_LOCATION_SIZE != checkStartedCells.length) {
                console.log('Fleet vessel overlap detected with required cells ' + FLEET_LOCATION_SIZE + ' and allocated cells ' + checkStartedCells.length);
                alert('Fleet vessel overlap detected.  Please try again.');
            }
        }

        /**
         * We have a set of locations, but they are in col/row format
         * Here we convert them to row/col and call the common function to add the corresponding
         * locations to the fleet vessel
         */
        function convertArrayAndAddLocationsToFleetVessel(availableCellsColRow, fleetVessel)
        {
            // We need to switch around the col/row and then call the common add locations function
            let availableCellsRowCol = [];
            for (let i=0; i<availableCellsColRow.length; i++) {
                let colRow = availableCellsColRow[i];
                let elemIdData = colRow.split('_');
                let colInt = parseInt(elemIdData[0]);
                let rowInt = parseInt(elemIdData[1]);
                // Do the old switcheroo
                availableCellsRowCol[availableCellsRowCol.length] = (rowInt + '_' + colInt);
            }
            // Now add corresponding locations
            addLocationsToFleetVessel(availableCellsRowCol, fleetVessel);
        }

        /**
         * We have a set of available locations, create location objects and add them to the fleet vessel
         */
        function addLocationsToFleetVessel(availableCells, fleetVessel)
        {
            // For simplicity we will re-add the existing location
            fleetVessel.locations = [];

            // We try not to start from the edge all the time
            let start = (parseInt(availableCells.length) - parseInt(fleetVessel.length));
            if (2 < start) start -= 1;
            let idx = 0;

            for (let i=parseInt(start); i<(parseInt(fleetVessel.length) + parseInt(start)); i++) {
                let cell = availableCells[i];
                let elemIdData = cell.split('_');
                let rowInt = parseInt(elemIdData[0]);
                let colInt = parseInt(elemIdData[1]);
                // Set this cell to be occupied and assign the first letter of the vessel type
                let elem = $('#cell_' + rowInt + '_' + colInt);
                setElemStatusClass(elem, 'bs-pos-cell-started');
                elem.removeClass('unoccupied');
                elem.html(fleetVessel.vessel_name.toUpperCase().charAt(0));
                fleetVessel.locations[idx++] = {
                    id: 0,
                    fleet_vessel_id: fleetVessel.fleetVesselId,
                    row: rowInt,
                    col: colInt,
                    vessel_name: fleetVessel.vessel_name
                };

            }
        }

        /**
         * We have a number of available cells. They can be both horizontal and vertical.
         * Here we build an array in either row or col order.
         * We are called with a randomly selected dimension.
         * The resulting array is sorted numerically and returned.
         */
        function getSortedAvailables(dimension, idxInt)
        {
            let sortedAvailables = [];
            let availables = $('.bs-pos-cell-available');

            for (let i=0; i<availables.length; i++) {
                let elem = availables[i];
                let elemIdData = $(elem).prop('id').split('_');
                let rowInt = parseInt(elemIdData[1]);
                let colInt = parseInt(elemIdData[2]);
                // NB When building the arrays we need to keep the cell row and col numbers separate
                // because we have to allow for row or col 10, when we come to split them up later
                if (BY_ROW == dimension && rowInt == idxInt) {
                    sortedAvailables[sortedAvailables.length] = (elemIdData[1] + '_' + elemIdData[2]);
                } else if (BY_COL == dimension && colInt == idxInt) {
                    sortedAvailables[sortedAvailables.length] = (elemIdData[2] + '_' + elemIdData[1]);
                }

            }
            sortedAvailables.sort(function(a, b){return a - b});    // numeric sort ascending

            return sortedAvailables;
        }

        /**
         * Here we obtain a collection of unoccupied cells. Then we randomly select one of them and return
         * it to the caller.  That cell will be used to see if there is enough room around it to plot all the
         * locations of the fleet vessel under consideration.  If not we will try again, otherwise either the
         * rows or cols will be selected to plot the vessel.
         */
        function selectUnoccupiedCell()
        {
            let selectedAvailableCells = $('.unoccupied');
            // Obtain a random unoccupied cell
            let cellNumber = Math.floor(Math.random() * selectedAvailableCells.length) + 1;
            // Work out its row/col from its id
            let elem = selectedAvailableCells.eq(cellNumber - 1);
            let elemIdData = elem.prop('id').split('_');
            let rowInt = parseInt(elemIdData[1]);
            let colInt = parseInt(elemIdData[2]);

            return {
                elem: elem,
                rowInt: rowInt,
                colInt: colInt
            };
        }

        /**
         * Cancel the random allocation of cells
         */
        function cancelRandom()
        {
            if (false == randomMode) {
                return;
            }
            randomMode = false;

            $("#cancelRandomButtonId").prop("disabled", true);
            $("#saveRandomButtonId").prop("disabled", true);

            // Enable all the radio buttons
            $(':radio').prop("disabled", false);
            // Restore the original set of locations
            clearGrid();
            fleetVessels = fleetVesselsClone;
            // Clear the back up collection
            fleetVesselsClone = null;
            // Replot the original set of locations, if any
            plotFleetLocations();
        }

        /**
         * Save the random allocation of cells
         */
        function saveRandom()
        {
            if (false == randomMode) {
                return;
            }
            randomMode = false;

            $("#cancelRandomButtonId").prop("disabled", true);
            $("#saveRandomButtonId").prop("disabled", true);

            // Convert all fleet vessel started locations to plotted locations
            $('.bs-pos-cell-started').addClass('bs-pos-cell-plotted');
            $('.bs-pos-cell-plotted').removeClass('bs-pos-cell-started');

            for (let i = 0; i < fleetVessels.length; i++) {
                fleetVessels[i].status = '{{FleetVessel::FLEET_VESSEL_PLOTTED}}';
            }
            let postData = {
                gameId: gameId,
                fleetId: fleetId,
                fleetVessels: fleetVessels,
                user_token: getCookie('user_token')
            };

            // ========================================================================
            // Post the new vessel locations to the server and await the return in the callback
            ajaxCall('replaceFleetVesselLocations', JSON.stringify(postData), replacedFleetVesselLocations);

        }

        /**
         * Start again with the editing of the fleet
         */
        function startAgain()
        {
            randomMode = false;

            $("#cancelRandomButtonId").prop("disabled", true);
            $("#saveRandomButtonId").prop("disabled", true);

            // We are starting again entirely
            clearGrid();
            // Clear all current locations
            for (let i = 0; i < fleetVessels.length; i++) {
                fleetVessels[i].locations = [];
            }
            let postData = {
                gameId: gameId,
                fleetId: fleetId,
                fleetVessels: fleetVessels,
                user_token: getCookie('user_token')
            };

            // ========================================================================
            // Post the new vessel locations to the server and await the return in the callback
            ajaxCall('replaceFleetVesselLocations', JSON.stringify(postData), replacedFleetVesselLocations);
        }

        /**
         * Callback from replacing fleet locations with the randomly generated set
         */
        function replacedFleetVesselLocations(returnedFleetVesselData)
        {
            // Check the result and reload page
            let fleetVesselCount = returnedFleetVesselData.fleetVesselCount;
            let fleetVesselLocationCount = returnedFleetVesselData.fleetVesselLocationCount;

            location.reload();
        }

        /**
         * Set the game to be single player
         */
        function singlePlayerGame()
        {
            let postData = {
                gameId: gameId,
                user_token: getCookie('user_token')
            };

            // ========================================================================
            // Post the new vessel locations to the server and await the return in the callback
            ajaxCall('setGameToSinglePlayer', JSON.stringify(postData), singlePlayerGameResult);

            return true;
        }

        /**
         * Result of setting the game to be single player
         */
        function singlePlayerGameResult(returnedSinglePlayerData)
        {
            if ('{{Game::STATUS_READY}}' == returnedSinglePlayerData.status)
            {
                // Load the new fleet data into the existing variables
                loadSinglePlayerFleet(returnedSinglePlayerData.fleet);

                // Randomly set the new fleet details
                goRandom();
                saveRandom();

                return true;
            }
            showNotification('There was a problem with the setting of the game to single player');

            return false;
        }

        /**
         * Load the single-player fleet data into a jscript structure
         * replacing the fleetVessels data, so we can randomly set it,
         * save it and exit to tyhe play grid
         */
        function loadSinglePlayerFleet(singlePlayerFleetData)
        {
            if (undefined != singlePlayerFleetData && null != singlePlayerFleetData) {

                fleetVessels = [];

                for (let i=0; i<singlePlayerFleetData.length; i++) {
                    let fleetVessel = singlePlayerFleetData[i];

                    // NB this is a new unplotted fleet, so there are no locations as yet
                    fleetVesselElem = {
                        fleetVesselId: fleetVessel.fleet_vessel_id,
                        vessel_name: fleetVessel.vessel_name,
                        status: fleetVessel.status,
                        length: fleetVessel.length,
                        points: fleetVessel.points,
                        locations: []
                    };

                    fleetVessels[fleetVessels.length] = fleetVesselElem;
                }
            }
        }

        /**
         * Examine the vessels, are they all plotted?
         */
        function allVesselsPlotted()
        {
            let allPlotted  = true;
            for (let i=0; i<fleetVessels.length; i++) {
                let fleetVessel = fleetVessels[i];
                if (fleetVessel.status != '{{FleetVessel::FLEET_VESSEL_PLOTTED}}') {
                    allPlotted = false;
                }
            }
            return allPlotted;
        }

        /**
         * Check the vessels and select the first available radio button
         */
        function selectFirstAvailableVessel()
        {
            if (null == fleetVessels || [] == fleetVessels || 0 == fleetVessels.length) {
                return;
            }
            for (let i = 0; i < fleetVessels.length; i++) {
                let fleetVessel = fleetVessels[i];
                if ('{{FleetVessel::FLEET_VESSEL_PLOTTED}}' != fleetVessel.status) {
                    $('#radio_id_' + fleetVessel.fleetVesselId).prop("checked", true);
                    break;
                }
            }
        }

        /**
         * Clears the entire grid
         */
        function clearGrid()
        {
            // Generic removal of all classes of all cells
            let cells = $('.grid-cell');
            cells.removeClass('bs-pos-cell-available');
            cells.removeClass('bs-pos-cell-started');
            cells.removeClass('bs-pos-cell-plotted');
            cells.html('O');

            return cells;
        }

        $(document).ready( function()
        {
            setCookie('user_token', $('#userToken').val(), 1);

            $('#engageLink').hide();
            @if (Game::STATUS_READY == $game->status || Game::STATUS_ENGAGED == $game->status)
                $('#engageLink').show();
            @endif

            $('#singlePlayerButtonId').prop('disabled', true);
            @if (Game::STATUS_WAITING == $game->status)
                $('#singlePlayerButtonId').prop('disabled', false);
            @endif

            plotFleetLocations();
            selectFirstAvailableVessel();

            return true;
        });
    </script>
@endsection