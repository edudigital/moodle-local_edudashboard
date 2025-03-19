
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
  //
  /**
   * Plugin version and other meta-data are defined here.
   *
   * @package     local_edudashboard
   * @copyright   2025 edudigital <geral@edudigital-learn.com>
   * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
   */


define(['jquery', 'core/str'], function($, str) {
    return {
        loadLangStrings: function(callback) {
            str.get_strings([
                {key: 'viewFullscreen', component: 'local_edudashboard'},
                {key: 'exitFullscreen', component: 'local_edudashboard'},
                {key: 'printChart', component: 'local_edudashboard'},
                {key: 'downloadPNG', component: 'local_edudashboard'},
                {key: 'downloadJPEG', component: 'local_edudashboard'},
                {key: 'downloadPDF', component: 'local_edudashboard'},
                {key: 'downloadSVG', component: 'local_edudashboard'},
                {key: 'contextButtonTitle', component: 'local_edudashboard'}
            ]).done(function(strings) {
                const lang = {
                    viewFullscreen: strings[0],
                    exitFullscreen: strings[1],
                    printChart: strings[2],
                    downloadPNG: strings[3],
                    downloadJPEG: strings[4],
                    downloadPDF: strings[5],
                    downloadSVG: strings[6],
                    contextButtonTitle: strings[7]
                };
                callback(lang);
            }).fail(function() {
                console.error('Error loading language strings');
            });
        }
    };
});
