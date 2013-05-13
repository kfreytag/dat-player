<?

class PlayerConfig
{
    protected $networkId;
    protected $venueId;
    protected $playerId;
    protected $environment;
    protected $internalIp;
    protected $assetHost;


    public function setAssetHost($assetHost)
    {
        $this->assetHost = $assetHost;
    }

    public function getAssetHost()
    {
        return $this->assetHost;
    }

    public function setEnvironment($environment)
    {
        $this->environment = $environment;
    }

    public function getEnvironment()
    {
        return $this->environment;
    }

    public function setInternalIp($internalIp)
    {
        $this->internalIp = $internalIp;
    }

    public function getInternalIp()
    {
        return $this->internalIp;
    }

    public function setNetworkId($networkId)
    {
        $this->networkId = $networkId;
    }

    public function getNetworkId()
    {
        return $this->networkId;
    }

    public function setPlayerId($playerId)
    {
        $this->playerId = $playerId;
    }

    public function getPlayerId()
    {
        return $this->playerId;
    }

    public function setVenueId($venueId)
    {
        $this->venueId = $venueId;
    }

    public function getVenueId()
    {
        return $this->venueId;
    }
}

?>