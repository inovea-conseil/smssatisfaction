<?php
/* SMS Satisfaction
 * Copyright (C) 2017       Inovea-conseil.com     <info@inovea-conseil.com>
 */

/**
 * \defgroup SMS Satisfaction
 * \file    core/modules/modSmssatisfaction.class.php
 * \ingroup satisfaction
 *
 * SMS Satisfaction
 */

include_once DOL_DOCUMENT_ROOT .'/core/modules/DolibarrModules.class.php';

require_once DOL_DOCUMENT_ROOT.'/core/class/extrafields.class.php';

/**
 *  Description and activation class for module MyModule
 */
class modSmssatisfaction extends DolibarrModules
{
	/**
	 * Constructor. Define names, constants, directories, boxes, permissions
	 *
	 * @param DoliDB $db Database handler
	 */
    public function __construct($db) {
        global $langs,$conf;

        $this->db = $db;
        
        $this->numero = 432413;
                
        $this->rights_class = 'Smssatisfaction';

        $this->family = "Inovea Conseil";
	$this->special = 0;

        $this->module_position = 500;

        $this->name = "smssatisfaction";

        // Module description, used if translation string 'ModuleXXXDesc' not found (where XXX is value of numeric property 'numero' of module)
        $this->description = "Module432413Desc";
        $this->editor_name = 'Inovea Conseil';
        $this->editor_url = 'https://www.inovea-conseil.com';

        $this->version = '1.0';

        $this->const_name = 'MAIN_MODULE_'.strtoupper($this->name);
        
        $this->picto='inoveaconseil@smssatisfaction';

        $this->module_parts = array(
            /*'css' => array('/smsintervention/css/SmsIntervention.css'),
            'hooks' => array(
                'invoicecard',
            )*/
        );

        $this->dirs = array();

        // Config pages. Put here list of php page, stored into dolitest/admin directory, to use to setup module.
        $this->config_page_url = array("setup.php@smssatisfaction");

        // Dependencies
        $this->hidden = false;
        $this->depends = array();
        $this->requiredby = array();
        $this->conflictwith = array();
        $this->phpmin = array(5,0);
        $this->need_dolibarr_version = array(3,7);
        $this->langfiles = array("smssatisfaction@smssatisfaction");
        
        $this->const = array();

        $this->tabs = array();

        if (! isset($conf->smssatisfaction) || ! isset($conf->smssatisfaction->enabled)) {
                $conf->smssatisfaction=new stdClass();
                $conf->smssatisfaction->enabled=0;
        }
        
        // Dictionaries
        $this->dictionaries=array();
        $this->boxes = array();	

        // Cronjobs
        $this->cronjobs = array();

        // Permissions
        $this->rights = array();
        /*$r=0;
        $this->rights[$r][0] = 900024;
        $this->rights[$r][1] = 'Envoi de SMS depuis la fiche intervention';
        $this->rights[$r][3] = 0;
        $this->rights[$r][4] = 'send';
        $r++;*/

        $this->menu = array();
        $r=0;
        $r=1;
    }

    /**
     * Init function
     *
     * @param      string	$options    Options when enabling module ('', 'noboxes')
     * @return     int             	1 if OK, 0 if KO
     */
    public function init($options='')
    {
        $sql = array();

        $this->_load_tables('/smssatisfaction/sql/');
        
        //VERIF EXTRAFIELD Facture
        $extrafields = new ExtraFields($this->db);
        $extrafields_facture = $extrafields->fetch_name_optionals_label('facture');
        $pos = count($extrafields_facture);
        
        // Statut
        if (!isset($extrafields_facture['satisfaction_client'])) {
            $params = array(
                'options' => array(
                    1 => 'Client satisfait',
                    0 => 'Client non satisfait',
                )
            );
            $extrafields->addExtraField('satisfaction_client', "Satisfaction Client", 'select', $pos++, null, 'facture', 0, 0, '', $params, true, '', 0, 0);
        }
        
        //active cron
        require_once DOL_DOCUMENT_ROOT.'/core/lib/admin.lib.php';
        activateModule('modCron');

        return $this->_init($sql, $options);
    }

    /**
     * Function called when module is disabled.
     * Remove from database constants, boxes and permissions from Dolibarr database.
     * Data directories are not deleted
     *
     * @param      string	$options    Options when enabling module ('', 'noboxes')
     * @return     int             	1 if OK, 0 if KO
     */
    public function remove($options = '')
    {
        $sql = array();

        return $this->_remove($sql, $options);
    }

}

