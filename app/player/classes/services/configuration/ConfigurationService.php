<?

require_once ('player/classes/services/BaseService.php');
require_once ('player/functions/sqlite3.php');

class ConfigurationService extends BaseService
{

    public static function loadPlayerConfig()
    {

        $sql = 'SELECT * FROM configuration';
        if ($row = self::$db->query($sql))
        {
            $playerConfig = new PlayerConfig();
            $playerConfig->setNetworkId($row['network_id']);
            $playerConfig->setVenueId($row['venue_id']);
            $playerConfig->setPlayerId($row['player_id']);
            $playerConfig->setEnvironment($row['environment']);
            $playerConfig->setInternalIp($row['internal_id']);
            $playerConfig->setAssetHost($row['asset_host']);
            return $playerConfig;
        }

        return null;
    }

    public static function savePlayerConfig ($playerConfig)
    {

    }

}

?>