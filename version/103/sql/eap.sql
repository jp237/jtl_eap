create table xplugin_jtl_eap_fulllog (
logid int not null primary key auto_increment,
customer_vname varchar(20) null,
customer_nname varchar(20) null,
warenkorb varchar(20) null,
zahlungsart varchar(56) null,
pruefung varchar(56) null,
ergebnis varchar(56) null,
tstamp varchar(56) null,
error varchar(128) null,
abschluss varchar(56),
sessToken varchar(32)
);
