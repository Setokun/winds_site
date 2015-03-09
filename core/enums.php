<?php
/**
 * Description of enums
 * @author Damien.D & Stephane.G
 */
abstract class Enumeration {
    static function getConstants(){
        return (new ReflectionClass(get_called_class()))
                ->getConstants();
    }
}

abstract class USER_TYPE extends Enumeration {
    const PLAYER        = 'player',
          MODERATOR     = 'moderator',
          ADMINISTRATOR = 'administrator';
}
abstract class USER_STATUS extends Enumeration {
    const CREATED   = 'created',
          ACTIVATED = 'activated',
          DELETING  = 'deleting',
          BANISHED  = 'banished';
}
abstract class ADDON_TYPE extends Enumeration {
    const THEME = 'theme',
          LEVEL = 'level';
}
abstract class ADDON_STATUS extends Enumeration {
    const ACCEPTED   = 'accepted',
          REFUSED    = 'refused',
          TOMODERATE = 'tomoderate';
}
abstract class LEVEL_TYPE extends Enumeration {
    const NONE   = 'none',
          BASIC  = 'basic',
          CUSTOM = 'custom';
}
abstract class SUBJECT_STATUS extends Enumeration {
    const ACTIVE = 'active',
          CLOSED = 'closed';
}
abstract class API_ACTION extends Enumeration {
    const GET_THEMES                 = 'getThemes',
          GET_BASIC_LEVELS           = 'getBasicLevels',
          GET_CUSTOM_LEVELS          = 'getCustomLevels',
          GET_LEVELS_TO_MODERATE     = 'getLevelsToModerate',
          GET_SCORES                 = 'getScores',
          GET_RANKS                  = 'getRanks',
          DOWNLOAD_PROFILE           = 'downloadProfile',
          DOWNLOAD_THEME             = 'downloadTheme',
          DOWNLOAD_BASIC_LEVEL       = 'downloadBasicLevel',
          DOWNLOAD_CUSTOM_LEVEL      = 'downloadCustomLevel',
          DOWNLOAD_LEVEL_TO_MODERATE = 'downloadLevelToModerate',
          UPLOAD_CUSTOM_LEVEL        = 'uploadCustomLevel',
          UPLOAD_SCORES              = 'uploadScores';
}