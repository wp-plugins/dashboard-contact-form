=== Custom Dashboard Widget ===
Contributors: Papik81
Tags: dashboard, message form, contact form, dashboard widget, user info
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=ER96JFW7V7UJG&lc=CZ&amount=5%2e00�cy_code=USD&bn=PP%2dDonationsBF%3abtn_donate_LG%2egif%3aNonHosted
Requires at least: 3.5
Tested up to: 4.1.1
License: GPLv2
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Umo��uje vytvo�it jednoduch� n�st�nkov� widget pro informace u�ivatel� a umo��uje jim pos�lat zpr�vy na p�edem definovan� e-mailovou adresu, p��p. adresy.
== Description ==
Umo��uje vytvo�it jednoduch� n�st�nkov� widget pro informace u�ivatel� a umo��uje jim pos�lat zpr�vy na p�edem definovan� e-mailovou adresu, p��p. adresy. Spr�vci str�nek mohou zvolit, jak� skupiny u�ivatel� uvid� tento widget na n�st�nce a mohou z�rove� m�nit i z�stupce ve formul��i. jako v�choz� e-mail p��jemce je nastaven e-mail spr�vce str�nek. Shortcody a obr�zky jsou t� podporov�ny. Czech translation available only.

== Installation ==
Nejlep�� je nainstalovat p��mo z prost�ed� WordPress. Pokud je t�eba ru�n� instalace, ujist�te se, �e jsou plugin soubory ve slo�ce s n�zvem  "custom-dashboard-widget" (ne dv� vno�en� slo�ky) ve slo�ce WordPress plugin�, obvykle ve "wp-content / plugins ".

== Frequently Asked Questions ==
### Kde mohu upravit formul��? ###
P�jd�te do Nastavn� a tam najd�te "Widget vlastn� n�st�nky".

###  Pokud nastav�m heslo nutn� pro editaci a zapomenu jej, je zp�sob, jak jej obnovit? ###
Nejjednodu��� zp�sob k obnoven� hesla je plugin smazat a znovu naisntalovat. Druh�, obt�n�j�� pro pokro�il� u�ivatele: v datab�zi najd�te tabulku options (obvykle "wp_options"),
najd�te n�zev hodnoty "cdw_password" a sma�te jej, a p�ed ulo�en�m vyberte funkci "md5". M�sto pr�zdn�ho pole zde m��ete d�t k nastaven� vlastn� heslo). V�choz� md5 hodnota pro pr�zdn� heslo by m�la b�t "d41d8cd98f00b204e9800998ecf8427e"

== Changelog ==


= Version 1.0.0 =
* v�choz� verze
 
= Version 1.0.1 =
* Bugfix: opraveny probl�my s ukl�d�n�m hesla

= Version 1.0.2 =
* Administr�to�i si nyn� mohou zvolit, pro jakou roli lze zobrazit kontaktn� formul��
* Zpr�vy jsou nyn� zas�l�ny v HTML form�tu
* Bugfix: titulek po odesl�n�
* Bugfix: titulek po odesl�n� zpr�vy
* Bugfix: pole kopie e-mailu lze nyn� vymazat
* Bugfix: pot�ebn� styly cdw_fix.css