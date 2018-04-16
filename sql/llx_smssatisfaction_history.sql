create table llx_smssatisfaction_history (
  rowid integer AUTO_INCREMENT PRIMARY KEY,
  fk_facture integer NOT NULL,
  date timestamp NOT NULL
)ENGINE=innodb;