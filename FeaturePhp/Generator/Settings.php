<?

namespace FeaturePhp\Generator;
use \FeaturePhp as fphp;

class Settings extends fphp\AbstractSettings {    
    public function __construct($cfg, $directory = ".") {
        parent::__construct($cfg, $directory);
    }

    public static function emptyInstance() {
        return self::fromArray(array());
    }
}

?>