<?php
#
# スキーマ更新ツール
#
require_once 'MDB2.php';
require_once dirname(__FILE__) . '/config.php';

class Schema {
    var $separator = ';';
    var $cn = null;
    var $lastError = null;

    function connect($dsn) {
        $this->cn =& MDB2::connect($dsn);
        if (PEAR::isError($this->cn)) {
            $this->lastError = $this->cn->getMessage();
            return false;
        } else {
            return true;
        }
    }

    function disconnect() {
        $this->cn->disconnect();
    }

    function getVersion() {
        $query = 'SELECT * FROM db_schema';
        $rs =& $this->cn->query($query);
        if (PEAR::isError($rs)) {
            $this->lastError = $rs->getMessage();
            return null;
        } else {
            $row = $rs->fetchRow(MDB2_FETCHMODE_ASSOC);
            return $row['version'];
        }
    }

    function setVersion($version) {
        $query = 'UPDATE db_schema SET version = ?';
        $sth = $this->cn->prepare($query, array('integer'), MDB2_PREPARE_MANIP);
        $rs =& $sth->execute($version);
        if (PEAR::isError($rs)) {
            $this->lastError = $rs->getMessage();
            return false;
        } else {
            return true;
        }
    }

    function getLastError() {
        return $this->lastError;
    }

    function executeQuery($query, $nested = 0) {
        if ($nested == 0) {
            $this->separator = ';';
        }

        $queries = split($this->separator, $query);
        while ($q = array_shift($queries)) {
            if (preg_match('/^@SEPARATOR=(.+)$/', $q, $m)) {
                # セパレータ変更
                $origQuery = join($this->separator, $queries);
                $this->separator = $m[1];
                $this->executeQuery($origQuery, 1);
                $queries = array();
            } elseif (trim($q)) {
                $rs =& $this->cn->query($q);
                if (PEAR::isError($rs)) {
                    return false;
                }
            }
        }

        return true;
    }
}

class SchemaUpdateApp {
    var $schema;
	var $dir;

    function init($dsn, $sql_dir) {
        $this->dir = $sql_dir;

        $this->schema = new Schema();
        if (!$this->schema->connect($dsn)) {
            printf("Result:\n  %s\n", $this->schema->getLastError());
            exit;
		}
    }

    function run() {
        if ($this->isDuplicatedUpdates()) {
            exit;
        }

        $version = $this->schema->getVersion();
        if (is_null($version)) {
            printf("Error: Could not get current schema version: %s\n",
                   $this->schema->getLastError());
            exit;
        }

        printf("Schema Version: %3d\n", $version);
        $updates = $this->getUpdates($version);
        foreach ($updates as $update_version => $update) {
            printf("New update found: %s", $update['filename']);
            if ($this->schema->executeQuery($update['query'])) {
                printf(" ... OK.\n");
                $this->schema->setVersion($update_version);
            } else {
                printf(" ... failed.\n");
                printf("Result:\n  %s\n", $this->schema->getLastError());
                break;
            }
        }

        if ($updates) {
            $version = $this->schema->getVersion();
            printf("\n");
            printf("Schema Version: %3d\n", $version);
        }
    }

    function isDuplicatedUpdates() {
        $updates = $this->getUpdateFiles();
        $versions = array();

        foreach ($updates as $update) {
            if (isset($versions[$update['version']])) {
                printf("Duplicated update version has detected: %s\n", $update['filename']);
                return true;
            } else {
                $versions[$update['version']] = 1;
            }
            $v = $update['version'];
        }
        
        return false;
    }

    function getUpdates($version) {
        $files = $this->getUpdateFiles($version);
        $updates = array();
        foreach ($files as $file) {
            $updates[$file['version']] = $file;
        }

        ksort($updates);
        return $updates;
    }

    function getUpdateFiles($base_version = 0) {
        $updates = array();
        $dir = opendir($this->dir);
        if ($dir) {
            while ($fn = readdir($dir)) {
                if (preg_match('/^(\d+)_/', $fn, $m)) {
                    $update_version = intval($m[0]);
                    if ($base_version < $update_version) {
                        $updates[] = array(
                                        'version'  => $update_version,
                                        'filename' => $fn,
                                        'query'    => file_get_contents($this->dir . '/' . $fn),
                                     );
                    } 
                }
            }
        }

        return $updates;
    }
}


$app = new SchemaUpdateApp();
$app->init($dsn, $sql_dir);
$app->run();

?>
