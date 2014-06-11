<?php
//////////////////////////////////////////////////////////////////////
// Created by armand
// Date: 6/3/14
// Time: 9:51 AM
// For: CookieSync


namespace CookieSync\Traits;

trait ModelBatchTrait {

    private function gameBatch($batchSize = 10)
    {
        $globalGameCount = \Game::count();

        for ($i = 0; $i < $globalGameCount; $i += $batchSize)
        {
            foreach(\Game::skip($i)->take($batchSize)->get() as $game)
            {
                yield $game;
            }
        }
    }

    private function saveBatch($batchSize = 10)
    {
        $globalSaveCount = \Save::count();

        for ($i = 0; $i < $globalSaveCount; $i += $batchSize)
        {
            foreach(\Save::skip($i)->take($batchSize)->get() as $save)
            {
                yield $save;
            }
        }
    }

    private function queryBatch($query, $batchSize = 10)
    {
        $globalSaveCount = $query->count();

        for ($i = 0; $i < $globalSaveCount; $i += $batchSize)
        {
            foreach($query->skip($i)->take($batchSize)->get() as $save)
            {
                yield $save;
            }
        }
    }

} 
