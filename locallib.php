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
 * Library of functions and constants for the local_dta plugin.
 *
 * @package   local_dta
 * @copyright Salvador Banderas Rovira
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

const LOCAL_DTA_COMPONENTS = [
    "experience" => 1,
    "case" => 2,
    "resource" => 3,
    "user" => 4
];

// RESOURCES is duplicated as a component and as a modifier bc it can be both
// we need to refactor this in the future, but not now it's fine

const LOCAL_DTA_MODIFIERS = [
    "theme" => 1,
    "tag" => 2,
    "resource" => 3,
];

const LOCAL_DTA_THEMES = [
    "Digital Technology" => 1,
    "Classroom Management" => 2,
    "Communication and Relationship Building" => 3,
    "Diversity and Inclusion" => 4,
    "Professional Collaboration and Development" => 5,
    "School Culture" => 6,
    "Curriculum Planning and Development" => 7,
    "Others" => 8
];
