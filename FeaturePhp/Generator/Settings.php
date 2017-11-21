<?

namespace FeaturePhp\Generator;

class Settings extends \FeaturePhp\AbstractSettings {    
    public function __construct($cfg, $directory = ".") {
        parent::__construct($cfg, $directory);
    }

    public static function emptyInstance() {
        return self::fromArray(array());
    }
}

?>