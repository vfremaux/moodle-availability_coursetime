<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Language strings.
 *
 * @package availability_coursetime
 * @copyright 2016 Valery Fremaux
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['description'] = 'Empêche l\'accès avant q\'un certain temps soit passé dans un certain cours.';
$string['pluginname'] = 'Restriction sur temps passé dans un cours';
$string['full_coursetime'] = 'Disponible lorsque le temps passé dans le cours <strong>{$a->fullname}</strong> excède <strong>{$a->timespent}</strong> minutes';
$string['short_coursetime'] = 'Disponible après <strong>{$a->timespent}</strong> minutes passés dans le cours <strong>{$a->shortname}</strong>';
$string['title'] = 'Temps passé';
$string['incourse'] = 'minutes passées dans le cours ';
$string['conditiontitle'] = 'Plus de ';
$string['error_nulltimespent'] = 'Vous devez entrer un nombre';
$string['error_nocourse'] = 'Vous devez choisir un cours de référence';

