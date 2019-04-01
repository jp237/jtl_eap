create table xplugin_jtl_eap_log (
keapLog int not null primary key auto_increment,
ceapResponseCode varchar(50) not null,
ceapResponseText text,
ceapLogEintrag text,
dErstellt datetime not null
);

create table xplugin_jtl_eap_zahlungsarten (
kZahlungsart int not null,
nMaxScore varchar(20) not null,
nMaxName varchar(20) not null,
nAbBetrag float(10,2) not null
);

