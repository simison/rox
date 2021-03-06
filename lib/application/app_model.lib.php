<?php
//**************************************************
// This project incorporates code, provided by
// Philipp Hunstein <hunstein@respice.de> and
// Seong-Min Kang <kang@respice.de>, taken from
// respice - Platform PT.
/**
 * Contains the abstract model base
 *
 * @package core
 * @author The myTravelbook Team <http://www.sourceforge.net/projects/mytravelbook>
 * @copyright Copyright (c) 2005-2006, myTravelbook Team
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License (GPL)
 * @version $Id: app_model.lib.php 68 2006-06-23 12:10:27Z kang $
 */
/**
 * Model base class
 * 
 * @package core
 * @author The myTravelbook Team <http://www.sourceforge.net/projects/mytravelbook>
 * @copyright Copyright (c) 2005-2006, myTravelbook Team
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License (GPL)
 * @abstract
 */
abstract class PAppModel 
{
    protected $dao;
    protected $prefix;
    protected $namespace;
    protected $className;
    protected $tableName;
    
    private $_cols = array();
    
    /**
     * Constructor
     */
    public function __construct() 
    {
        // instantiate the dao
        $db = PVars::getObj('config_rdbms');
        if (!$db) {
            throw new PException('DB config error!');
        }
        $dao = PDB::get($db->dsn, $db->user, $db->password);
        $this->dao =& $dao;

        MOD_params::get()->loadParams();
    }
    
    /**
     * Destructor
     * 
     * @param void
     */
    public function __destruct() 
    {
        unset($this->dao);
    }
}
