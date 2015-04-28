=== Custom Dashboard Widget ===
Contributors: Papik81
Tags: dashboard, message form, contact form, dashboard widget, user info
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=ER96JFW7V7UJG&lc=CZ&amount=5%2e00¤cy_code=USD&bn=PP%2dDonationsBF%3abtn_donate_LG%2egif%3aNonHosted
Requires at least: 3.5
Tested up to: 4.1.1
License: GPLv2
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Umožòuje vytvoøit jednoduchý nástìnkový widget pro informace uživatelù a umožòuje jim posílat zprávy na pøedem definované e-mailovou adresu, pøíp. adresy.
== Description ==
Umožòuje vytvoøit jednoduchý nástìnkový widget pro informace uživatelù a umožòuje jim posílat zprávy na pøedem definované e-mailovou adresu, pøíp. adresy. Správci stránek mohou zvolit, jaké skupiny uživatelù uvidí tento widget na nástìnce a mohou zároveò mìnit i zástupce ve formuláøi. jako výchozí e-mail pøíjemce je nastaven e-mail správce stránek. Shortcody a obrázky jsou též podporovány. Czech translation available only.

== Installation ==
Nejlepší je nainstalovat pøímo z prostøedí WordPress. Pokud je tøeba ruèní instalace, ujistìte se, že jsou plugin soubory ve složce s názvem  "custom-dashboard-widget" (ne dvì vnoøené složky) ve složce WordPress pluginù, obvykle ve "wp-content / plugins ".

== Frequently Asked Questions ==
### Kde mohu upravit formuláø? ###
Pøjdìte do Nastavní a tam najdìte "Widget vlastní nástìnky".

###  Pokud nastavím heslo nutné pro editaci a zapomenu jej, je zpùsob, jak jej obnovit? ###
Nejjednodušší zpùsob k obnovení hesla je plugin smazat a znovu naisntalovat. Druhý, obtížnìjší pro pokroèilé uživatele: v databázi najdìte tabulku options (obvykle "wp_options"),
najdìte název hodnoty "cdw_password" a smažte jej, a pøed uložením vyberte funkci "md5". Místo prázdného pole zde mùžete dát k nastavení vlastní heslo). Výchozí md5 hodnota pro prázdné heslo by mìla být "d41d8cd98f00b204e9800998ecf8427e"

== Changelog ==


= Version 1.0.0 =
* výchozí verze
 
= Version 1.0.1 =
* Bugfix: opraveny problémy s ukládáním hesla

= Version 1.0.2 =
* Administrátoøi si nyní mohou zvolit, pro jakou roli lze zobrazit kontaktní formuláø
* Zprávy jsou nyní zasílány v HTML formátu
* Bugfix: titulek po odeslání
* Bugfix: titulek po odeslání zprávy
* Bugfix: pole kopie e-mailu lze nyní vymazat
* Bugfix: potøebné styly cdw_fix.css