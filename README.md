# I244-Projekt
Võrgurakendused I projekt: Flash Card

Aadress: http://dev.metsvahi.ee/

--
##Kirjeldus
Kuna iga tudeng peab tegema kontrolltöid ja eksameid, otsustasin luua veebi kus saaks õppida nendeks asjadeks äärmiselt effektiiselt. 
Flash Card on veeb kus on kasutajate loodud teemad ja nende kohta küsimused ning neid saab siis läbi teha.
Selleks, et läbida neid teemasid ei ole kontot vaja, küll aga konto kasutamine lubab luua oma enda teemasid ja soovi korral neid avalikustada ka teistele. 
Samuti sisse loginud kasutajatel on võimalik valida erinevate teemade vahel et teha pilti veits ilusamaks.


###Kuidas teema läbimine käib on järgmine:
1. Kasutaja valib teema. Nagu näiteks hetkel on seal esimene CCNA ehk Cisco.
2. Teema valides hakkavad keskmisele mängukaardile ilmuma küsimused ükshaaval suvalises järjekorras
3. Kasutaja mõtleb kas ta teab antud küsimusele vastust.
4. Kui vastus on näieks liiga pikk või tahab midagi ülesse märkida siis vasakul pool on notepad.
5. Vastust näeb kui küsimuskaardi peale klõpsata.
6. Kasutaja valib ise kas ta vastus oli õige või vale. Mäng käib aususe peale, eesmärk on ikkagi õppida ja keegi statistikat ei pea.
7. Kui kasutaja valid vale, siis see küsimus pannakse "potti" tagasi ja seda küsitakse uuesti millagil
8. Kui kasutaja valis õige, siis võetakse uus küsimus kuni küsimused saavad otsa ja kasutaja viiakse peamenüüsse tagasi.


--
##Koodi poolelt
###Projekt kasutab 4 klassi
1. /classes/mysql.php = **ma ei kirjutanud seda klassi ise:**olen varem seda klassi kasutanud ja see teeb mysql andmebaasiga suhtlemise kõvasti kergemaks. See ei eemalda vajadust näiteks teada mysql süntaksi vms aga nagu ka koodist näha siis teeb igasugused päringud lühemaks.Samas loogiliselt oli vaja teha omad muudatused vastavalt projekti vajadustele.
2. /classes/page.php = Antud klass kontrollib siis mida kuvatakse lehel.
3. /classes/template.php = Tegin selle klassi et kasutada templaate faile ja html osa elegantsemalt sisse tuua.
4. /classes/user.php = Klass kontrollib tervet kasutaja spetsiifilist osa nagu mängu skoor, andmete valideerimine, sisse logimine jms

###Lisafailid
1. define.php = hoiab kõiki muutujaid nagu andmebaasi tabelid, mysql andmed jms
2. functions.php = hoiab erinevaid funktsioone mida läheb vaja. Hetkel polegi seal väga peale paari lühendava funktsiooni.
3. config.php = antud faili sissetõmbamine käivitab terve andmebaasi ja klasside ühenduse.

###Indeks
1. Index.php = terve tegevus tegelikult toimub ainult läbi index.php

###Js
1. Projekt kasutab jquery 2.2.4 kuid hetkel suht minimaalsel moel. Kavas seda rohkem kasutada näiteks ajaxi päringute ja animatsioonide jaoks.


--
#To-do
1. Uute teemade lisamine kasutajate poolt - lihtsalt pole jõudnud teha seda veel
2. eemade läbimise kohta statistika kasutajatele külge
3. Uute küsimuste küsimine läbi ajax requestide et tervet lehte igakord ei peaks laadima uuesti


--
#Hinnand
Üldiselt tuli projekt päris hästi välja siiamaani: kõik asjad on ilusti organiseeritud ja kood näeb ilus välja. Iga asja jaoks on oma klass ning suutsin need ilusti kokku siduda. 
Mis aga hetkel probleemiks jäi oli arendusaja puudus ehk ei ole veel suutnud kõiki featureid sisse panna mis oleks vaja, kuidagi tähelepanu hajus rohkem korrektse frameworki loomiseks nagu kõik klassid jms ning hiljem kujunduse peale nagu vahetatavad kaardi ja taustapildid. Koodi enda üle olen aga uhke.

--
#Plaanid
Kindlasti see projekt ei sure siin ära ning teen jõudsalt edasi kuna see on kasulik tööriist millega õppida. Mida rohkem aineid siia lisada seda kasulikumaks see läheb mitte ainult minu jaoks vaid ka ehk mu kursuse kaaslaste kuna ka neil on vaja samu aineid läbida. 

Muideks nii koodis kui ka andmebaasis tegin ma koha sisulehte haldamise jaoks aga leitsin et pole seda veel vaja niiet kärpisin praegu välja.

Oleks lahe lisada visuaalselt näiteks kaardipakki kust tõmmates tulevad küsimuskaardid välja, õigesti vastates lähevad hunnikusse ja valest vastates tagasi pakki (see seob siis kokku ajaxiga küsimuste laadimisel).
