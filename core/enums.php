<?php
/**
 * Description of enums
 * @author Damien.D & Stephane.G
 */

abstract class USER_TYPE {
    const PLAYER='player',
          MODERATOR='moderator',
          ADMINISTRATOR='administrator';
}
abstract class USER_STATUS {
    const CREATED='created',
          ACTIVATED='activated',
          DELETING="deleting",
          BANISHED='banished';
}
abstract class ADDON_TYPE {
    const THEME='theme',
          LEVEL='level';
}
abstract class ADDON_STATUS {
    const ACCEPTED='accepted',
          REFUSED='refused',
          TOMODERATE='tomoderate';
}
abstract class LEVEL_TYPE {
    const NONE='none',
          BASIC='basic',
          CUSTOM='custom';
}
abstract class SUBJECT_STATUS {
    const ACTIVE='active',
          CLOSED='closed';
}
abstract class API_ACTION {
    const GET_THEMES='getThemes',
          GET_CUSTOM_LEVELS='getCustomLevels',
          GET_LEVELS_TOMODERATE='getLevelsToModerate',
          GET_RANKS='getRanks',
          DOWNLOAD_USER_ACCOUNT='downloadUserAccount',
          DOWNLOAD_THEME='downloadTheme',
          DOWNLOAD_CUSTOM_LEVEL='downloadCustomLevel',
          DOWNLOAD_LEVEL_TOMODERATE='downloadLevelToModerate',
          UPLOAD_CUSTOM_LEVEL='uploadCustomLevel',
          UPLOAD_SCORES='uploadScores';
}