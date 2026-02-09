<?php

use Illuminate\Support\Facades\Log;
use \App\FleetVessel;

class SinglePlayerHandler
{
    const GRID_SIZE = 10;
    const ROW_OK = 'row';
    const COL_OK = 'col';

    private $gameId = 0;
    private $allMoves = [];
    private $workGrid = [];
    private $hitVessels = [];
    private $smallestVesselSize = 0;
    private $nextRow = 0;
    private $nextCol = 0;

    public function processSinglePlayerMoves()
    {
        // We get System's all moves and plot them on a 10x10 grid.
        $this->workGrid = $this->buildEmptyWorkGrid();

        // Check what moves have already been attempted
        if (!isset($this->allMoves) || count($this->allMoves) <= 0)
        {
            // There are no moves, so choose a starting point at random
            list($this->nextRow, $this->nextCol) = $this->handleFirstMove();
        } else
        {
            // Load an array representing the current distribution of moves
            $this->loadWorkGridAndHitArray();

            if (!isset($this->hitVessels) || count($this->hitVessels) <= 0)
            {
                // No hits, just generate the next move based on the last one
                list($this->nextRow, $this->nextCol) =
                    $this->getNextAvailableCell($this->allMoves[0]->row, $this->allMoves[0]->col);
            } else
            {
                // We have at least one non-destroyed vessel, which we have hit.  We need to finish it off.
                list($this->nextRow, $this->nextCol) = $this->handleHitVessel();
            }
        }
    }

    /**
     * Handles the identification of the next row/col once we have hit at least one vessel
     */
    private function handleHitVessel()
    {
        foreach ($this->hitVessels as $hitVesselMoves) {
            // If there are more than one hit vessel, we concentrate on whichever comes first
            if (1 == count($hitVesselMoves)) {
                return $this->handleHitVesselWithOneHit($hitVesselMoves[0]);
            } else {
                return $this->handleHitVesselWithMultiHits($hitVesselMoves);
            }
        }
        throw new Exception('handleHitVessel: unexpected mismatch in count of hit vessels');
    }

    /**
     * Handles a vessel which has been hit more than once
     */
    private function handleHitVesselWithMultiHits($hitVesselMoves)
    {
        // We know there are at least 2 entries
        // All moves will be either the same row or the same col
        if ($hitVesselMoves[0]->row == $hitVesselMoves[1]->row) {
            // Same row, therefore we are working with the columns
            $colArray = [];
            foreach ($hitVesselMoves as $aMove) {
                $colArray[] = $aMove->col;
            }
            sort($colArray);
            $endIdx = count($colArray) - 1;

            switch (true) {
                case $this->isFree($hitVesselMoves[0]->row, ($colArray[0] - 1)):
                    $nextRow = $hitVesselMoves[0]->row;
                    $nextCol = $colArray[0] - 1;
                    break;

                case $this->isFree($hitVesselMoves[0]->row, ($colArray[$endIdx] + 1)):
                    $nextRow = $hitVesselMoves[0]->row;
                    $nextCol = $colArray[$endIdx] + 1;
                    break;

                default:
                    throw new Exception("handleHitVesselWithMultiHits: unexpectedly, using cols, there is no available cell to hit");
            }
        } else{
            // Same column, therefore we are working with the rows
            $rowArray = [];
            foreach ($hitVesselMoves as $aMove) {
                $rowArray[] = $aMove->row;
            }
            sort($rowArray);
            $endIdx = count($rowArray) - 1;

            switch (true) {
                case $this->isFree(($rowArray[0] - 1), $hitVesselMoves[0]->col):
                    $nextRow = $rowArray[0] - 1;
                    $nextCol = $hitVesselMoves[0]->col;
                    break;

                case $this->isFree(($rowArray[$endIdx] + 1), $hitVesselMoves[0]->col):
                    $nextRow = $rowArray[$endIdx] + 1;
                    $nextCol = $hitVesselMoves[0]->col;
                    break;

                default:
                    throw new Exception("handleHitVesselWithMultiHits: unexpectedly, using rows, there is no available cell to hit");
            }
        }

        return [$nextRow, $nextCol];
    }

    /**
     * Handles a vessel which has been hit only once
     */
    private function handleHitVesselWithOneHit($aMove)
    {
        // We can piggy back the analysis of space when no hits have been secured
        // We are checking there is room for the vessel we have just hit
		$this->setSmallestVesselSize($aMove->length);
        // We nullify the row/col so that we include that cell in the calc of spaces available
        $this->workGrid[$aMove->row][$aMove->col] = null;

        $rowOrColOk = $this->isRoomForSmallestVessel($aMove->row, $aMove->col);


        switch (true) {
            case ($this->isFree($aMove->row, ($aMove->col - 1)) && self::ROW_OK == $rowOrColOk):
                $nextRow = $aMove->row;
                $nextCol = $aMove->col - 1;
                break;

            case (
                $this->isFree(($aMove->row - 1), $aMove->col) && self::COL_OK == $rowOrColOk):
                $nextRow = $aMove->row - 1;
                $nextCol = $aMove->col;
                break;

            case ($this->isFree($aMove->row, ($aMove->col + 1)) && self::ROW_OK == $rowOrColOk):
                $nextRow = $aMove->row;
                $nextCol = $aMove->col + 1;
                break;

            case ($this->isFree(($aMove->row + 1), $aMove->col) && self::COL_OK == $rowOrColOk):
                $nextRow = $aMove->row + 1;
                $nextCol = $aMove->col;
                break;

            default:
                throw new Exception("handleHitVesselWithOneHit: unexpectedly, there is no available cell to hit");
        }
        return [$nextRow, $nextCol];
    }

    /**
     * Checks that the targetcell is in the grid and is not already occupied
     */
    private function isFree($row, $col)
    {
        if (
            (0 == $row || $row > self::GRID_SIZE) ||
            (0 == $col || $col > self::GRID_SIZE)
        ) {
            return false;
        }
        return (null == $this->workGrid[$row][$col]);
    }

    /**
     * Examines all the existing moves and loads them into a couple of arrays
     */
    private function loadWorkGridAndHitArray()
    {
        // Plot all the existing moves onto a work grid
        foreach ($this->allMoves as $aMove) {
            $this->workGrid[$aMove->row][$aMove->col] = $aMove;
            // While we are doing this we load an array of vessels we have hit, that have not been destroyed
            if ($aMove->hit_vessel && FleetVessel::FLEET_VESSEL_DESTROYED != $aMove->fleet_vessel_status) {
                $this->hitVessels[$aMove->hit_vessel_id][] = $aMove;
            }
        }
    }

    /**
     * Generates a random first move
     */
    private function handleFirstMove()
    {
        return([rand(1, 10), rand(1, 10)]);
    }

    /**
     * Returns the next available cell
     */
    private function getNextAvailableCell($startRow, $startCol)
    {
        // We attempt to leave a gap, as checkerboard pattern is most efficient strategy
        // But some cells cannot be where the opponent has their vessels because there isn't enough room
        // We use the minimum length of the remaining vessels.  Thus, if 3 cells is the minimum length
        // then the next available cell must be part of at least 3 spaces.
        $startCol += 2;
        $newRow = false;
        if ($startCol == self::GRID_SIZE + 1) {
            // We were hitting odd numbered cells, go to next row on evens
            $startCol = 2;
            $newRow = true;
        } elseif ($startCol == self::GRID_SIZE + 2) {
            // We were hitting even numbered cells, go to next row on odds
            $startCol = 1;
            $newRow = true;
        }
        if ($newRow) {
            $startRow += 1;
            if ($startRow > self::GRID_SIZE) {
                $startRow = 1;
            }
        }

        // Try twice to find the next cell
        for ($n=0; $n<2; $n++) {
            for ($i = $startRow; $i <= self::GRID_SIZE; $i++) {
                for ($j = $startCol; $j <= self::GRID_SIZE; $j++) {
                    if (
                        null == $this->workGrid[$i][$j]
                        && false != $this->isRoomForSmallestVessel($i, $j)
                    ) {
                        return [$i, $j];
                    }
                }
            }
            // Hmmm, could not find an available cell, start from the beginning and try again
            $startRow = 1;
            $startCol = 1;
        }
        throw new Exception('getNextAvailableCell: could not find an available cell');
    }

    /**
     * Checks for a given row/col that it is part of a set of cells capable of storing
     * the smallest vessel in the opponent's fleet.
     * Note that this only applies to vessels that have not yet been hit.  If a vessel has been hit
     * then we just chase the cells around that vessel.
     */
    public function isRoomForSmallestVessel($startRow, $startCol)
    {
        // Using the start row first, we navigate the columns, go towards zero and then come forward to count how many
        // available cells there are.  If more than smallest vessel size then this cell is available
        for ($i=$startCol; $i>0; $i--) {
            if (null != $this->workGrid[$startRow][$i]) {
                break;
            }
        }
        // $i = the starting column, but add one because we dropped out with a non-null or zero
        $i = $i + 1;
        for ($j=$i; $j<=self::GRID_SIZE; $j++) {
            if (null != $this->workGrid[$startRow][$j]) {
                break;
            }
        }
        if (($j - $i) >= $this->smallestVesselSize) {
            // The extent of available cells is equal to or greater than the space needed
            return self::ROW_OK;
        }
        // If we get here then there wasn't enough room on the row, try the column
        // Using the start col first, we navigate the rows, go towards zero and then come forward to count how many
        // available cells there are.  If more than smallest vessel size then this cell is available
        for ($i=$startRow; $i>0; $i--) {
            if (null != $this->workGrid[$i][$startCol]) {
                break;
            }
        }
        // $i = the starting row, but add one because we dropped out with a non-null or zero
        $i = $i + 1;
        for ($j=$i; $j<=self::GRID_SIZE; $j++) {
            if (null != $this->workGrid[$j][$startCol]) {
                break;
            }
        }
        if (($j - $i) >= $this->smallestVesselSize) {
            // The extent of available cells is equal to or greater than the space needed
            return self::COL_OK;
        }

        return false;
    }

    /**
     * Returns an array of arrays forming a gridsize grid
     */
    private function buildEmptyWorkGrid()
    {
        $grid = [];
        for ($i=1; $i<=self::GRID_SIZE; $i++) {
            $gridCol = [];
            for ($j=1; $j<=self::GRID_SIZE; $j++) {
                $gridCol[$j] = null;
            }
            $grid[$i] = $gridCol;
        }
        return $grid;
    }

    public function setGameId($gameId)
    {
        $this->gameId = $gameId;
    }

    public function setAllMoves($allMoves)
    {
        $this->allMoves = $allMoves;
    }

    public function getNextRow()
    {
        return $this->nextRow;
    }

    public function getNextCol()
    {
        return $this->nextCol;
    }

    public function getSmallestVesselSize()
    {
        return $this->smallestVesselSize;
    }

    public function setNextRow($nextRow)
    {
        $this->nextRow = $nextRow;
    }

    public function setNextCol($nextCol)
    {
        $this->nextCol = $nextCol;
    }

    public function setSmallestVesselSize($smallestVesselSize)
    {
        $this->smallestVesselSize = $smallestVesselSize;
    }

    public function setWorkGrid($workGrid)
    {
        $this->workGrid = $workGrid;
    }
}