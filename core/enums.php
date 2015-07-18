<?php
/**
 * Description of enums file
 * @author Damien.D & Stephane.G
 */

/**
 * Abstract class used to centralize "getContants" method.
 */
abstract class Enumeration {
    /**
     * Get an array of the enumeration's constants.
     * @return array
     */
    static function getConstants(){
        return (new ReflectionClass(get_called_class()))
                ->getConstants();
    }
}

/**
 * Enumeration of the user's types.
 */
abstract class USER_TYPE extends Enumeration {
    const PLAYER        = 'player',
          MODERATOR     = 'moderator',
          ADMINISTRATOR = 'administrator';
}

/**
 * Enumeration of the user's statuses.
 */
abstract class USER_STATUS extends Enumeration {
    const CREATED   = 'created',
          ACTIVATED = 'activated',
          DELETING  = 'deleting',
          DELETED   = 'deleted',
          BANISHED  = 'banished';
}

/**
 * Enumeration of the level's types.
 */
abstract class LEVEL_TYPE extends Enumeration {
    const BASIC  = 'basic',
          CUSTOM = 'custom';
}

/**
 * Enumeration of the level's statuses.
 */
abstract class LEVEL_STATUS extends Enumeration {
    const ACCEPTED   = 'accepted',
          REFUSED    = 'refused',
          TOMODERATE = 'tomoderate';
}

/**
 * Enumeration of the level's modes.
 */
abstract class LEVEL_MODE extends Enumeration {
    const STANDARD = 'standard',
          BOSS     = 'boss';
}

/**
 * Enumeration of the forum subject's statuses.
 */
abstract class SUBJECT_STATUS extends Enumeration {
    const ACTIVE = 'active',
          CLOSED = 'closed';
}

/**
 * Enumeration of the API's actions.
 */
abstract class API_ACTION extends Enumeration {
    const GET_THEMES                 = 'getThemes',
          GET_LEVEL_INFOS            = 'getLevelInfos',
          GET_BASIC_LEVELS           = 'getBasicLevels',
          GET_CUSTOM_LEVELS          = 'getCustomLevels',
          GET_LEVELS_TO_MODERATE     = 'getLevelsToModerate',
          GET_SCORES                 = 'getScores',
          GET_RANKS                  = 'getRanks',
          GET_TROPHIES               = 'getTrophies',
          DOWNLOAD_PROFILE           = 'downloadProfile',
          DOWNLOAD_THEME             = 'downloadTheme',
          DOWNLOAD_BASIC_LEVEL       = 'downloadBasicLevel',
          DOWNLOAD_CUSTOM_LEVEL      = 'downloadCustomLevel',
          DOWNLOAD_LEVEL_TO_MODERATE = 'downloadLevelToModerate',
          UPLOAD_CUSTOM_LEVEL        = 'uploadCustomLevel',
          UPLOAD_SCORES              = 'uploadScores';
}