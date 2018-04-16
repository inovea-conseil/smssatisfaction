<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */



class smssatisfactioncron {
    
    public function exec() {
        global $conf, $db;
        
        $regexTel = '/^(\+33|0)(6|7)([0-9]{8})/';
        
        // Verification de la config SMS
        
        // Vérifie les constantes SMS et si la connexion se fait etc...
        $applicationKey = $conf->global->SMSINTERVENTION_APPLICATION_KEY;
        $applicationSecret = $conf->global->SMSINTERVENTION_APPLICATION_SECRET;
        $consumerKey = $conf->global->SMSINTERVENTION_CONSUMER_KEY;
        $contentSms = $conf->global->SMSSATISFACTION_CONTENT;

        if ($applicationKey != '' && $applicationSecret != '' && $consumerKey != '' && $contentSms != '') {
            require_once DOL_DOCUMENT_ROOT . '/compta/facture/class/facture.class.php';
        
            //Selectionne toutes les facture payé
            $sql = "SELECT * FROM ".MAIN_DB_PREFIX."facture WHERE entity = " . $conf->entity . " AND paye = 1";
            $sql .= " AND rowid NOT IN (SELECT fk_facture FROM ".MAIN_DB_PREFIX."smssatisfaction_history)";

            $result = $db->query($sql);
            $nbfacture = $db->num_rows($result);

            for ($i = 0; $i < $nbfacture; $i++) {
                $obj = $db->fetch_object($result);

                $f = new Facture($db);
                $f->fetch($obj->rowid);

                //On vérifie si le client est satisfait
                if ($f->array_options['options_satisfaction_client'] == 1) {
                    require_once DOL_DOCUMENT_ROOT . '/societe/class/societe.class.php';
                    $societe = new Societe($db);
                    $societe->fetch($f->socid);
                    if (!preg_match($regexTel, $societe->phone)) {
                        $phone_to_check = array('phone_pro', 'phone_perso', 'phone_mobile');

                        $contacts = $societe->contact_array_objects();

                        foreach ($contacts as $contact) {
                            foreach ($phone_to_check as $phone_field) {
                                if (!preg_match($regexTel, $contact->{$phone_field})) {
                                    continue;
                                } else {
                                    $telSms = $contact->{$phone_field};
                                    break 2;
                                }
                            }
                        }

                    } else {
                        $telSms = $societe->phone;
                    }

                    if (isset($telSms)) {
                        require_once dol_buildpath('/smssatisfaction/lib/sms.php');
                        
                        // on formate le numéro au format internationnal
                        $telSms = substr($telSms, 0, 1) == '0' ? '+33'.substr($telSms, 1, strlen($telSms)-1) : $telSms;
                        try {
                            $sms = new sms($applicationKey, $applicationSecret, $consumerKey);
                            if (!$sms->sendSMS($contentSms, $telSms)) {
                                $errorSms = 'SMS_SEND_FAILED';
                            } else {
                                $sql2 = 'INSERT INTO ' . MAIN_DB_PREFIX . 'smssatisfaction_history' . '(fk_facture, date)';
                                $sql2 .= ' VALUES ('.$obj->rowid.', NULL)';

                                
                                
                                $resql = $db->query($sql2);
                            }
                        } catch (Exception $ex) {
                            file_put_contents(__DIR__.'/error_log', $sql);
                            dol_syslog(get_class($this)." ".$ex->getMessage(), LOG_ERR);
                        }
                    }
                    
                }
                
            }
        }
        
        
        
        
    }
}