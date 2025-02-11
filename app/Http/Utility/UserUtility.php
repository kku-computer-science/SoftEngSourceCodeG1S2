<?php

use App\Models\User;

class UserUtility
{
    public static function getUserSearchKey($userId, $sourceId)
    {
        $user = User::find($userId);
        $source = $user->source()->where('source_data.id', $sourceId)->first();
        return $source->pivot->search_id;
    }

    public static function addUserSearchKey($userId, $sourceId, $newSearchId)
    {
        $user = User::find($userId);
        $user->source()->attach($sourceId, ['search_id' => $newSearchId]);
    }
}